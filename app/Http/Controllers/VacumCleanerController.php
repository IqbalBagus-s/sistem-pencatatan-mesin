<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VacumCleanerCheck;
use App\Models\VacumCleanerResultsTable1;
use App\Models\VacumCleanerResultsTable2;

class VacumCleanerController extends Controller
{
    public function index(Request $request)
    {
        $query = VacumCleanerCheck::query();
    
        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('checker_minggu1', 'LIKE', $search)
                  ->orWhere('checker_minggu2', 'LIKE', $search)
                  ->orWhere('approver_minggu1', 'LIKE', $search)
                  ->orWhere('approver_minggu2', 'LIKE', $search);
            });
        }
    
        // Filter berdasarkan nomor vacuum cleaner
        if ($request->filled('search_vacuum_cleaner')) {
            $query->where('nomer_vacum_cleaner', $request->search_vacuum_cleaner);
        }
        
        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            try {
                $bulan = $request->bulan;
                $query->where('bulan', $bulan);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Format bulan tidak valid.');
            }
        }
    
        // Ambil data dengan paginasi
        $checks = $query->paginate(10)->appends($request->query());
        
        // Load semua data tambahan untuk setiap check
        foreach ($checks as $check) {
            // Dapatkan semua checker unik
            $check->allCheckers = collect([$check->checker_minggu1, $check->checker_minggu2])
                ->filter()
                ->unique()
                ->values()
                ->toArray();
                
            // Hitung jumlah hari dalam bulan
            $year = substr($check->bulan, 0, 4);
            $month = substr($check->bulan, 5, 2);
            $check->daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            
            // Hitung jumlah tanggal yang disetujui
            $approvedCount = 0;
            
            // Cek approval minggu 1-2
            if (!empty($check->approver_minggu1)) {
                $approvedCount += 1;
            }
            
            // Cek approval minggu 3-4
            if (!empty($check->approver_minggu2)) {
                $approvedCount += 1;
            }
            
            $check->approvedDatesCount = $approvedCount;
        }
    
        return view('vacuum_cleaner.index', compact('checks'));
    }

    public function create()
    {
        return view('vacuum_cleaner.create');
    }
}
