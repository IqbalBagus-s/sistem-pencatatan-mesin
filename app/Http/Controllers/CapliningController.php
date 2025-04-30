<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\CapliningCheck;
use App\Models\CapliningResult;

class CapliningController extends Controller
{
    public function index(Request $request)
    {
        $query = CapliningCheck::query();

        // Filter berdasarkan nama checker atau approver
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('checked_by', 'LIKE', $search)
                ->orWhere('approved_by', 'LIKE', $search);
            });
        }

        // Filter berdasarkan nomor caplining
        if ($request->filled('search_caplining')) {
            $query->where('nomer_caplining', $request->search_caplining);
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            try {
                $tanggal = $request->tanggal;
                $query->whereDate('tanggal', $tanggal);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Format tanggal tidak valid.');
            }
        }

        // Ambil data dengan paginasi dan eager load hasil pemeriksaan
        $checks = $query->with('results')->paginate(10)->appends($request->query());
        
        // Load semua data tambahan untuk setiap check
        foreach ($checks as $check) {
            // Dapatkan informasi checker dan approver
            $check->allCheckers = collect([$check->checked_by])
                ->filter()
                ->unique()
                ->values()
                ->toArray();
                
            // Hitung jumlah item yang sudah dicheck
            $checkedItems = 0;
            foreach ($check->results as $result) {
                // Hitung berapa check yang sudah diisi
                $checksCompleted = 0;
                for ($i = 1; $i <= 5; $i++) {
                    if (!empty($result->{"check{$i}"})) {
                        $checksCompleted++;
                    }
                }
                
                if ($checksCompleted > 0) {
                    $checkedItems++;
                }
            }
            
            $check->checkedItemsCount = $checkedItems;
            
            // Status approval
            $check->isApproved = !empty($check->approved_by);
        }

        return view('caplining.index', compact('checks'));
    }
}
