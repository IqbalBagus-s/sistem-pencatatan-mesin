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

    public function create()
    {
        return view('crane_matras.create');
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'nomer_crane_matras' => 'required|integer|between:1,15',
            'bulan' => 'required|date_format:Y-m',
            'tanggal' => 'required|integer|between:1,31',
            'checked_by' => 'required|string',
        ]);

        // Check for duplicate record
        $existingRecord = CraneMatrasCheck::where('nomer_crane_matras', $request->nomer_crane_matras)
            ->where('bulan', $request->bulan)
            ->where('tanggal', $request->tanggal)
            ->first();
        
        if ($existingRecord) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data tersebut sudah ada!');
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create Crane Matras Check record
            $craneMatrasCheck = CraneMatrasCheck::create([
                'nomer_crane_matras' => $request->nomer_crane_matras,
                'bulan' => $request->bulan,
                'tanggal' => $request->tanggal,
                'checked_by' => $request->checked_by,
                'approved_by' => null, // Will be handled separately
            ]);
            
            // Get the ID of the newly created record
            $checkId = $craneMatrasCheck->id;
            
            // Define the checked items for Crane Matras
            $items = [
                1 => 'INVERTER',
                2 => 'KONTAKTOR',
                3 => 'THERMAL OVERLOAD',
                4 => 'PUSH BOTTOM',
                5 => 'MOTOR',
                6 => 'BREAKER',
                7 => 'TRAFO',
                8 => 'CONECTOR BUSBAR',
                9 => 'REL BUSBAR',
                10 => 'GREASE',
                11 => 'RODA',
                12 => 'RANTAI',
            ];
            
            // Process each item
            foreach ($items as $itemId => $itemName) {
                $checkKey = "check_{$itemId}";
                $keteranganKey = "keterangan_{$itemId}";
                
                $resultData = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                    'check' => isset($request->$checkKey) ? $request->$checkKey : '-',
                    'keterangan' => isset($request->$keteranganKey) ? $request->$keteranganKey : null,
                ];
                
                // Create the result record
                CraneMatrasResult::create($resultData);
            }
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('crane-matras.index')
                ->with('success', 'Data berhasil disimpan!');
                
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
