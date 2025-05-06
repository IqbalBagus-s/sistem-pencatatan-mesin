<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CraneMatrasCheck;
use App\Models\CraneMatrasResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF

class CraneMatrasControler extends Controller
{
    public function index(Request $request, $checkId = null)
    {
        $query = CraneMatrasCheck::query();

        // Jika ada ID pengecekan spesifik
        if ($checkId) {
            $query->where('check_id', $checkId);
            
            // Ambil data check untuk ditampilkan di view
            $check = CraneMatrasCheck::findOrFail($checkId);
        } else {
            // Jika tidak ada ID spesifik, kita bisa filter berdasarkan parameter lain
            
            // Filter berdasarkan item yang diperiksa
            if ($request->filled('item')) {
                $query->where('checked_items', 'LIKE', '%' . $request->item . '%');
            }
            
            // Filter berdasarkan hasil pengecekan
            if ($request->filled('check')) {
                $query->where('check', $request->check);
            }
            
            // Kita bisa juga melakukan join untuk filter berdasarkan nomor crane atau bulan
            if ($request->filled('crane') || $request->filled('bulan')) {
                $query->join('crane_matras_checks', 'crane_matras_results.check_id', '=', 'crane_matras_checks.id');
                
                if ($request->filled('crane')) {
                    $query->where('crane_matras_checks.nomer_crane_matras', $request->crane);
                }
                
                if ($request->filled('bulan')) {
                    try {
                        $bulan = date('m', strtotime($request->bulan));
                        $tahun = date('Y', strtotime($request->bulan));
                        $query->where('crane_matras_checks.bulan', $bulan)
                              ->orWhere('crane_matras_checks.bulan', $tahun . '-' . $bulan)
                              ->orWhere('crane_matras_checks.bulan', $bulan . '/' . $tahun);
                    } catch (\Exception $e) {
                        return redirect()->back()->with('error', 'Format bulan tidak valid.');
                    }
                }
                
                // Pastikan kita memilih kolom yang benar
                $query->select('crane_matras_results.*');
            }
            
            $check = null;
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $results = $query->paginate(15)->appends($request->query());

        return view('crane_matras.index', compact('results', 'check'));
    }

}
