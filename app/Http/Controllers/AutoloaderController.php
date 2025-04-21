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
        // Ambil data utama autoloader check
        $check = AutoloaderCheck::findOrFail($id);
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = AutoloaderResultTable1::where('check_id', $id)->get();
        $resultsTable2 = AutoloaderResultTable2::where('check_id', $id)->get();
        $resultsTable3 = AutoloaderResultTable3::where('check_id', $id)->get();
        
        // Ambil data detail (checked_by)
        $detailChecks = AutoloaderDetail::where('tanggal_check_id', $id)->get();
        
        // Siapkan data untuk view dalam format yang sesuai dengan helper function
        $results = collect();
        
        // Buat array untuk menyimpan data checked_by berdasarkan tanggal
        $checkedByData = [];
        
        // Proses data checked_by dulu agar tersedia untuk digunakan kemudian
        foreach ($detailChecks as $detail) {
            $checkedByData[$detail->tanggal] = $detail->checked_by;
        }
        
        // Proses data dari tabel 1 (tanggal 1-11)
        foreach ($resultsTable1 as $row) {
            $itemId = array_search($row->checked_items, [
                1 => 'Filter',
                2 => 'Selang',
                3 => 'Panel Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Thermal Overload',
                6 => 'MCB',
            ]);
            
            // Jika item ditemukan, proses untuk setiap tanggal (1-11)
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        // Cek apakah ada data checked_by untuk tanggal ini
                        $checkedBy = isset($checkedByData[$j]) ? $checkedByData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checked_by' => $checkedBy
                        ]);
                    }
                }
            }
        }
        
        // Proses data dari tabel 2 (tanggal 12-22)
        foreach ($resultsTable2 as $row) {
            $itemId = array_search($row->checked_items, [
                1 => 'Filter',
                2 => 'Selang',
                3 => 'Panel Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Thermal Overload',
                6 => 'MCB',
            ]);
            
            if ($itemId) {
                for ($j = 12; $j <= 22; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        // Cek apakah ada data checked_by untuk tanggal ini
                        $checkedBy = isset($checkedByData[$j]) ? $checkedByData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checked_by' => $checkedBy
                        ]);
                    }
                }
            }
        }
        
        // Proses data dari tabel 3 (tanggal 23-31)
        foreach ($resultsTable3 as $row) {
            $itemId = array_search($row->checked_items, [
                1 => 'Filter',
                2 => 'Selang',
                3 => 'Panel Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Thermal Overload',
                6 => 'MCB',
            ]);
            
            if ($itemId) {
                for ($j = 23; $j <= 31; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        // Cek apakah ada data checked_by untuk tanggal ini
                        $checkedBy = isset($checkedByData[$j]) ? $checkedByData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checked_by' => $checkedBy
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checked_by untuk tanggal yang mungkin belum memiliki item
        for ($j = 1; $j <= 31; $j++) {
            if (isset($checkedByData[$j]) && !$results->where('tanggal', $j)->where('checked_by', '!=', null)->count()) {
                $results->push([
                    'tanggal' => $j,
                    'checked_by' => $checkedByData[$j]
                ]);
            }
        }
        
        return view('autoloader.edit', compact('check', 'results'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'nomer_autoloader' => 'required|integer|between:1,23',
            'shift' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);
    
        // Cari data autoloader yang akan diupdate
        $autoloaderCheck = AutoloaderCheck::findOrFail($id);
    
        // Cek apakah ada perubahan pada data utama (nomer_autoloader, shift, bulan)
        if ($autoloaderCheck->nomer_autoloader != $request->nomer_autoloader || 
            $autoloaderCheck->shift != $request->shift || 
            $autoloaderCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = AutoloaderCheck::where('nomer_autoloader', $request->nomer_autoloader)
                ->where('shift', $request->shift)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $id) // Kecualikan record saat ini
                ->first();
            
            if ($existingRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data dengan nomor autoloader, shift, dan bulan yang sama sudah ada!');
            }
        }
    
        // Mulai transaksi database
        DB::beginTransaction();
    
        try {
            // Update data AutoloaderCheck
            $autoloaderCheck->update([
                'nomer_autoloader' => $request->nomer_autoloader,
                'shift' => $request->shift,
                'bulan' => $request->bulan,
            ]);
            
            // Definisikan items yang diperiksa
            $items = [
                1 => 'Filter',
                2 => 'Selang',
                3 => 'Panel Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Thermal Overload',
                6 => 'MCB',
            ];
            
            // Ambil data existing dari ketiga tabel
            $existingTable1Data = AutoloaderResultTable1::where('check_id', $id)->get()->keyBy('checked_items');
            $existingTable2Data = AutoloaderResultTable2::where('check_id', $id)->get()->keyBy('checked_items');
            $existingTable3Data = AutoloaderResultTable3::where('check_id', $id)->get()->keyBy('checked_items');
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Update atau buat record untuk tabel 1 (hari 1-11)
                $table1Record = $existingTable1Data->get($itemName);
                $resultData1 = [];
                
                for ($j = 1; $j <= 11; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData1["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData1["tanggal{$j}"] = '-';
                    }
                    
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData1["keterangan_tanggal{$j}"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData1["keterangan_tanggal{$j}"] = null;
                    }
                }
                
                if ($table1Record) {
                    // Update record yang sudah ada
                    $table1Record->update($resultData1);
                } else {
                    // Buat record baru jika belum ada
                    $resultData1['check_id'] = $id;
                    $resultData1['checked_items'] = $itemName;
                    AutoloaderResultTable1::create($resultData1);
                }
                
                // Update atau buat record untuk tabel 2 (hari 12-22)
                $table2Record = $existingTable2Data->get($itemName);
                $resultData2 = [];
                
                for ($j = 12; $j <= 22; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData2["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData2["tanggal{$j}"] = '-';
                    }
                    
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData2["keterangan_tanggal{$j}"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData2["keterangan_tanggal{$j}"] = null;
                    }
                }
                
                if ($table2Record) {
                    // Update record yang sudah ada
                    $table2Record->update($resultData2);
                } else {
                    // Buat record baru jika belum ada
                    $resultData2['check_id'] = $id;
                    $resultData2['checked_items'] = $itemName;
                    AutoloaderResultTable2::create($resultData2);
                }
                
                // Update atau buat record untuk tabel 3 (hari 23-31)
                $table3Record = $existingTable3Data->get($itemName);
                $resultData3 = [];
                
                for ($j = 23; $j <= 31; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData3["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData3["tanggal{$j}"] = '-';
                    }
                    
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData3["keterangan_tanggal{$j}"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData3["keterangan_tanggal{$j}"] = null;
                    }
                }
                
                if ($table3Record) {
                    // Update record yang sudah ada
                    $table3Record->update($resultData3);
                } else {
                    // Buat record baru jika belum ada
                    $resultData3['check_id'] = $id;
                    $resultData3['checked_items'] = $itemName;
                    AutoloaderResultTable3::create($resultData3);
                }
            }
            
            // Ambil data checked_by yang sudah ada
            $existingDetails = AutoloaderDetail::where('tanggal_check_id', $id)
                ->get()
                ->keyBy('tanggal');
            
            // Proses informasi checked_by untuk semua hari (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $checkedByKey = "checked_by_{$i}";
                
                if ($request->has($checkedByKey) && !empty($request->$checkedByKey)) {
                    $detailData = [
                        'checked_by' => $request->$checkedByKey,
                    ];
                    
                    $existingDetail = $existingDetails->get($i);
                    
                    if ($existingDetail) {
                        // Update data yang sudah ada
                        $existingDetail->update($detailData);
                    } else {
                        // Buat data baru
                        $detailData['tanggal_check_id'] = $id;
                        $detailData['tanggal'] = $i;
                        $detailData['approved_by'] = null;
                        AutoloaderDetail::create($detailData);
                    }
                } elseif ($existingDetails->has($i)) {
                    // Jika tidak ada data di form tapi ada di database, update nilai checked_by menjadi null
                    $existingDetails->get($i)->update(['checked_by' => null]);
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('autoloader.index')
                ->with('success', 'Data Autoloader berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
