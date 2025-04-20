<?php

namespace App\Http\Controllers;
use App\Models\AutoloaderCheck;
use App\Models\AutoloaderDetail;
use App\Models\AutoloaderResultTable1;
use App\Models\AutoloaderResultTable2;
use App\Models\AutoloaderResultTable3;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF

use Illuminate\Http\Request;

class AutoloaderController extends Controller
{
    public function index(Request $request)
    {
        $query = AutoloaderCheck::query();

        // Filter berdasarkan checked_by atau approved_by jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('checkerAndApprover', function ($q) use ($search) {
                $q->where('checked_by', 'LIKE', $search)
                  ->orWhere('approved_by', 'LIKE', $search);
            });
        }

        // Filter berdasarkan nomor autoloader
        if ($request->filled('search_autoloader')) {
            $query->where('nomer_autoloader', $request->search_autoloader);
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

        // Filter berdasarkan shift
        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->with('checkerAndApprover')->paginate(10)->appends($request->query());

        return view('autoloader.index', compact('checks'));
    }
    
    public function create()
    {
        return view('autoloader.create');
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'nomer_autoloader' => 'required|integer|between:1,23',
            'shift' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Check for duplicate record
        $existingRecord = AutoloaderCheck::where('nomer_autoloader', $request->nomer_autoloader)
            ->where('shift', $request->shift)
            ->where('bulan', $request->bulan)
            ->first();
        
        if ($existingRecord) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data dengan nomor autoloader, shift, dan bulan yang sama sudah ada!');
        }

        // Start a database transaction
        DB::beginTransaction();
    
        try {
            // Create Autoloader Check record
            $autoloaderCheck = AutoloaderCheck::create([
                'nomer_autoloader' => $request->nomer_autoloader,
                'shift' => $request->shift,
                'bulan' => $request->bulan,
            ]);
            
            // Get the ID of the newly created record
            $checkId = $autoloaderCheck->id;
            
            // Define the checked items
            $items = [
                1 => 'Filter',
                2 => 'Selang',
                3 => 'Panel Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Thermal Overload',
                6 => 'MCB',
            ];
            
            // Process each item
            foreach ($items as $itemId => $itemName) {
                // Prepare data structures for all three tables
                $resultData1 = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                $resultData2 = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                $resultData3 = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                // Process checks for Table 1 (days 1-11)
                for ($j = 1; $j <= 11; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData1["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData1["tanggal{$j}"] = '-'; // Default value
                    }
                    
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData1["keterangan_tanggal{$j}"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData1["keterangan_tanggal{$j}"] = null;
                    }
                }
                
                // Process checks for Table 2 (days 12-22)
                for ($j = 12; $j <= 22; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData2["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData2["tanggal{$j}"] = '-'; // Default value
                    }
                    
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData2["keterangan_tanggal{$j}"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData2["keterangan_tanggal{$j}"] = null;
                    }
                }
                
                // Process checks for Table 3 (days 23-31)
                for ($j = 23; $j <= 31; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData3["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData3["tanggal{$j}"] = '-'; // Default value
                    }
                    
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData3["keterangan_tanggal{$j}"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData3["keterangan_tanggal{$j}"] = null;
                    }
                }
                
                // Create the result records for all tables
                AutoloaderResultTable1::create($resultData1);
                AutoloaderResultTable2::create($resultData2);
                AutoloaderResultTable3::create($resultData3);
            }
            
            // Process checked_by information for all days (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $checkedByKey = "checked_by_{$i}";
                $checkNumKey = "check_num_{$i}";
                
                if ($request->has($checkedByKey) && !empty($request->$checkedByKey)) {
                    AutoloaderDetail::create([
                        'tanggal_check_id' => $checkId,
                        'tanggal' => $i, // Using the column number as the day
                        'checked_by' => $request->$checkedByKey,
                        'approved_by' => null, // Approval would be handled separately
                    ]);
                }
            }
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('autoloader.index')
                ->with('success', 'Data Autoloader berhasil disimpan!');
                
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Ambil data AutoloaderCheck berdasarkan ID
        $check = AutoloaderCheck::findOrFail($id);
        
        // Ambil semua detail checker untuk check ini
        $checkerDetails = AutoloaderDetail::where('tanggal_check_id', $check->id)->get();
        
        // Muat tabel hasil untuk form
        $resultTable1 = $check->resultTable1;
        $resultTable2 = $check->resultTable2;
        $resultTable3 = $check->resultTable3;
        
        // Buat collection untuk hasil check yang lebih mudah digunakan dalam view
        $results = collect();
        
        // Buat pemetaan tanggal ke checked_by dari detail checker
        $checkerMap = [];
        foreach ($checkerDetails as $detail) {
            $checkerMap[$detail->tanggal] = $detail->checked_by;
        }
        
        // Tambahkan debugging untuk melihat isi tabel hasil
        // dd($resultTable1->toArray(), $resultTable2->toArray(), $resultTable3->toArray());
        
        // Proses tabel 1 (hari 1-11)
        foreach ($resultTable1 as $result) {
            $itemId = $result->checked_items;
            
            for ($i = 1; $i <= 11; $i++) {
                $tanggalField = "tanggal{$i}";
                $keteranganField = "keterangan_tanggal{$i}";
                
                // Pastikan kita tidak mengubah nilai null menjadi string kosong untuk field result
                $results->push([
                    'tanggal' => $i,
                    'item_id' => $itemId,
                    'result' => $result->$tanggalField, // Simpan nilai asli (bahkan jika null)
                    'keterangan' => $result->$keteranganField ?? '',
                    'checked_by' => $checkerMap[$i] ?? ''
                ]);
            }
        }
        
        // Proses tabel 2 (hari 12-22)
        foreach ($resultTable2 as $result) {
            $itemId = $result->checked_items;
            
            for ($i = 12; $i <= 22; $i++) {
                $tanggalField = "tanggal{$i}";
                $keteranganField = "keterangan_tanggal{$i}";
                
                $results->push([
                    'tanggal' => $i,
                    'item_id' => $itemId,
                    'result' => $result->$tanggalField, // Simpan nilai asli (bahkan jika null)
                    'keterangan' => $result->$keteranganField ?? '',
                    'checked_by' => $checkerMap[$i] ?? ''
                ]);
            }
        }
        
        // Proses tabel 3 (hari 23-31)
        foreach ($resultTable3 as $result) {
            $itemId = $result->checked_items;
            
            for ($i = 23; $i <= 31; $i++) {
                $tanggalField = "tanggal{$i}";
                $keteranganField = "keterangan_tanggal{$i}";
                
                $results->push([
                    'tanggal' => $i,
                    'item_id' => $itemId,
                    'result' => $result->$tanggalField, // Simpan nilai asli (bahkan jika null)
                    'keterangan' => $result->$keteranganField ?? '',
                    'checked_by' => $checkerMap[$i] ?? ''
                ]);
            }
        }
        
        // Tambahkan debugging untuk melihat hasil akhir collection
        // dd($results->toArray());
        
        return view('autoloader.edit', compact('check', 'results'));
    }
}
