<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\VacumCleanerCheck;
use App\Models\VacumCleanerResultsTable1;
use App\Models\VacumCleanerResultsTable2;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF

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

        $query->orderBy('created_at', 'desc');
    
        // Ambil data dengan paginasi
        $checks = $query->paginate(10)->appends($request->query());
        
        // Load semua data tambahan untuk setiap check
        foreach ($checks as $check) {
            // Dapatkan semua checker unik
            $check->allCheckers = collect([$check->checker_minggu2, $check->checker_minggu4])
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

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nomer_vacuum_cleaner' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);
    
        // Debug: Cek data yang diterima dari form
        Log::info('Data dari form vacuum cleaner:', $request->all());
    
        // Periksa apakah data sudah ada
        $existingRecord = VacumCleanerCheck::where('nomer_vacum_cleaner', $request->nomer_vacuum_cleaner)
            ->where('bulan', $request->bulan)
            ->first();

        if ($existingRecord) {
            // Ambil nilai yang duplikat
            $nomerVacuum = $request->nomer_vacuum_cleaner;
            $bulan = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Buat pesan error dengan informasi spesifik
            $pesanError = "Data sudah ada untuk Vacuum Cleaner nomor {$nomerVacuum} pada bulan {$bulan}!";
            
            return redirect()->back()
                ->withInput()
                ->with('error', $pesanError);
        }
    
        // Mulai transaksi database
        DB::beginTransaction();
    
        try {
            // Data utama untuk tabel vacuum cleaner checks
            $data = [
                'nomer_vacum_cleaner' => $request->nomer_vacuum_cleaner,
                'bulan' => $request->bulan,
            ];
            
            // Set checker dan tanggal berdasarkan form data untuk minggu ke-2
            if ($request->has('check_num_1') && $request->check_num_1 == '1') {
                $data['checker_minggu2'] = $request->checked_by_1;
                
                // Parsing tanggal dengan format yang benar (DD-MM-YYYY)
                if (!empty($request->check_date_1)) {
                    $dateParts = explode('-', $request->check_date_1);
                    if (count($dateParts) === 3) {
                        // Format yang disimpan adalah YYYY-MM-DD (format database standar)
                        $data['tanggal_dibuat_minggu2'] = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                    }
                }
            }
            
            // Set checker dan tanggal berdasarkan form data untuk minggu ke-4
            if ($request->has('check_num_2') && $request->check_num_2 == '2') {
                $data['checker_minggu4'] = $request->checked_by_2;
                
                // Parsing tanggal dengan format yang benar (DD-MM-YYYY)
                if (!empty($request->check_date_2)) {
                    $dateParts = explode('-', $request->check_date_2);
                    if (count($dateParts) === 3) {
                        // Format yang disimpan adalah YYYY-MM-DD (format database standar)
                        $data['tanggal_dibuat_minggu4'] = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                    }
                }
            }
            
            // Buat record VacumCleanerCheck
            $vacuumCleanerCheck = VacumCleanerCheck::create($data);
            
            // Log untuk memastikan record berhasil dibuat
            Log::info('Record vacuum cleaner check dibuat dengan ID: ' . $vacuumCleanerCheck->id);
            
            // Ambil ID dari record yang baru dibuat
            $checkId = $vacuumCleanerCheck->id;
            
            // Definisikan item yang diperiksa
            $items = [
                1 => 'Kebersihan Body',
                2 => 'Motor',
                3 => 'Selang',
                4 => 'Aksesoris',
                5 => 'Filter',
                6 => 'Bostel',
                7 => 'Kabel',
            ];
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Data untuk tabel minggu 2
                $resultData1 = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                // Set nilai check untuk minggu 2
                if (isset($request->check_1[$itemId])) {
                    $resultData1['minggu2'] = $request->check_1[$itemId];
                } else {
                    $resultData1['minggu2'] = '-';
                }
                
                // Set keterangan untuk minggu 2
                if (isset($request->keterangan_1[$itemId])) {
                    $resultData1['keterangan_minggu2'] = $request->keterangan_1[$itemId];
                } else {
                    $resultData1['keterangan_minggu2'] = null;
                }
                
                // Data untuk tabel minggu 4
                $resultData2 = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                // Set nilai check untuk minggu 4
                if (isset($request->check_2[$itemId])) {
                    $resultData2['minggu4'] = $request->check_2[$itemId];
                } else {
                    $resultData2['minggu4'] = '-';
                }
                
                // Set keterangan untuk minggu 4
                if (isset($request->keterangan_2[$itemId])) {
                    $resultData2['keterangan_minggu4'] = $request->keterangan_2[$itemId];
                } else {
                    $resultData2['keterangan_minggu4'] = null;
                }
                
                // Buat record hasil pemeriksaan untuk kedua tabel
                $table1Result = VacumCleanerResultsTable1::create($resultData1);
                $table2Result = VacumCleanerResultsTable2::create($resultData2);
                
                // Log untuk memastikan record hasil berhasil dibuat
                Log::info("Item #{$itemId} ({$itemName}) berhasil disimpan ke table1 dengan ID: " . $table1Result->id);
                Log::info("Item #{$itemId} ({$itemName}) berhasil disimpan ke table2 dengan ID: " . $table2Result->id);
            }
            
            // Commit transaksi
            DB::commit();
            
            Log::info('Transaksi vacuum cleaner berhasil disimpan');
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('success', 'Data berhasil disimpan!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            // Log error detail untuk debugging
            Log::error('Error saat menyimpan data vacuum cleaner: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            // Retrieve the vacuum cleaner check record with the given ID
            $check = VacumCleanerCheck::findOrFail($id);
            
            // Retrieve the related items from both tables
            $resultsTable1 = VacumCleanerResultsTable1::where('check_id', $id)->get();
            $resultsTable2 = VacumCleanerResultsTable2::where('check_id', $id)->get();
            
            // Prepare collection to store all results in a structured format
            $results = collect();
            
            // Define the items we check for vacuum cleaners
            $itemsMap = [
                1 => 'Kebersihan Body',
                2 => 'Motor',
                3 => 'Selang',
                4 => 'Aksesoris',
                5 => 'Filter',
                6 => 'Bostel',
                7 => 'Kabel',
            ];
            
            // Process data from table 1 (minggu2 data)
            foreach ($resultsTable1 as $row) {
                $itemId = array_search($row->checked_items, $itemsMap);
                
                if ($itemId) {
                    $results->push([
                        'minggu' => 2,
                        'item_id' => $itemId,
                        'item_name' => $row->checked_items,
                        'result' => $row->minggu2,
                        'keterangan' => $row->keterangan_minggu2,
                        'checked_by' => $check->checker_minggu1,
                        'approved_by' => $check->approver_minggu1
                    ]);
                }
            }
            
            // Process data from table 2 (minggu4 data)
            foreach ($resultsTable2 as $row) {
                $itemId = array_search($row->checked_items, $itemsMap);
                
                if ($itemId) {
                    $results->push([
                        'minggu' => 4,
                        'item_id' => $itemId,
                        'item_name' => $row->checked_items,
                        'result' => $row->minggu4,
                        'keterangan' => $row->keterangan_minggu4,
                        'checked_by' => $check->checker_minggu2,
                        'approved_by' => $check->approver_minggu2
                    ]);
                }
            }
            
            // Group results by minggu for easier access in the view
            $groupedResults = $results->groupBy('minggu');
            
            // Check which weeks have checkers
            $check_num_1 = $check->checker_minggu1 ? 1 : null;
            $check_num_2 = $check->checker_minggu2 ? 2 : null;
            
            // Get all needed data ready for the view
            $data = [
                'check' => $check,
                'results' => $results,
                'groupedResults' => $groupedResults,
                'itemsMap' => $itemsMap,
                'check_num_1' => $check_num_1,
                'check_num_2' => $check_num_2,
            ];
            
            return view('vacuum_cleaner.edit', $data);
            
        } catch (\Exception $e) {
            // Log error detail for debugging
            Log::error('Error saat mengambil data vacuum cleaner untuk edit: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('error', 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'nomer_vacuum_cleaner' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);
    
        // Cari data vacuum cleaner yang akan diupdate
        $vacuumCheck = VacumCleanerCheck::findOrFail($id);
    
        // Cek apakah ada perubahan pada data utama (nomer_vacuum_cleaner, bulan)
        if ($vacuumCheck->nomer_vacum_cleaner != $request->nomer_vacuum_cleaner || 
            $vacuumCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = VacumCleanerCheck::where('nomer_vacum_cleaner', $request->nomer_vacuum_cleaner)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $id) // Kecualikan record saat ini
                ->first();
            
            if ($existingRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data dengan nomor vacuum cleaner dan bulan yang sama sudah ada!');
            }
        }
    
        // Mulai transaksi database
        DB::beginTransaction();
    
        try {
            // Update data VacumCleanerCheck
            $vacuumCheck->update([
                'nomer_vacum_cleaner' => $request->nomer_vacuum_cleaner,
                'bulan' => $request->bulan,
            ]);
            
            // Definisikan items yang diperiksa
            $items = [
                1 => 'Kebersihan Body',
                2 => 'Motor',
                3 => 'Selang',
                4 => 'Aksesoris',
                5 => 'Filter',
                6 => 'Bostel',
                7 => 'Kabel',
            ];
            
            // Update data untuk minggu 2 (table 1)
            if ($request->has('check_1')) {
                $existingTable1Data = VacumCleanerResultsTable1::where('check_id', $id)->get()->keyBy('checked_items');
                
                foreach ($items as $itemId => $itemName) {
                    $result = isset($request->check_1[$itemId]) ? $request->check_1[$itemId] : '-';
                    $keterangan = isset($request->keterangan_1[$itemId]) ? $request->keterangan_1[$itemId] : null;
                    
                    $table1Record = $existingTable1Data->get($itemName);
                    
                    if ($table1Record) {
                        // Update record yang sudah ada
                        $table1Record->update([
                            'minggu2' => $result,
                            'keterangan_minggu2' => $keterangan
                        ]);
                    } else {
                        // Buat record baru jika belum ada
                        VacumCleanerResultsTable1::create([
                            'check_id' => $id,
                            'checked_items' => $itemName,
                            'minggu2' => $result,
                            'keterangan_minggu2' => $keterangan
                        ]);
                    }
                }
            }
            
            // Update data untuk minggu 4 (table 2)
            if ($request->has('check_2')) {
                $existingTable2Data = VacumCleanerResultsTable2::where('check_id', $id)->get()->keyBy('checked_items');
                
                foreach ($items as $itemId => $itemName) {
                    $result = isset($request->check_2[$itemId]) ? $request->check_2[$itemId] : '-';
                    $keterangan = isset($request->keterangan_2[$itemId]) ? $request->keterangan_2[$itemId] : null;
                    
                    $table2Record = $existingTable2Data->get($itemName);
                    
                    if ($table2Record) {
                        // Update record yang sudah ada
                        $table2Record->update([
                            'minggu4' => $result,
                            'keterangan_minggu4' => $keterangan
                        ]);
                    } else {
                        // Buat record baru jika belum ada
                        VacumCleanerResultsTable2::create([
                            'check_id' => $id,
                            'checked_items' => $itemName,
                            'minggu4' => $result,
                            'keterangan_minggu4' => $keterangan
                        ]);
                    }
                }
            }
            
            // Update data checker untuk minggu ke-2
            // PERUBAHAN: Hanya update jika checkbox dipilih, jangan reset jika tidak dipilih
            if ($request->has('check_num_1') && !empty($request->check_num_1)) {
                $tanggal = null;
                if (!empty($request->check_date_1)) {
                    $tanggal = Carbon::createFromFormat('d-m-Y', $request->check_date_1)->format('Y-m-d');
                }
                
                $vacuumCheck->update([
                    'checker_minggu2' => $request->checked_by_1,
                    'tanggal_dibuat_minggu2' => $tanggal
                ]);
            }
            // Hapus blok elseif yang mereset nilai checker
            
            // Update data checker untuk minggu ke-4
            // PERUBAHAN: Hanya update jika checkbox dipilih, jangan reset jika tidak dipilih
            if ($request->has('check_num_2') && !empty($request->check_num_2)) {
                $tanggal = null;
                if (!empty($request->check_date_2)) {
                    $tanggal = Carbon::createFromFormat('d-m-Y', $request->check_date_2)->format('Y-m-d');
                }
                
                $vacuumCheck->update([
                    'checker_minggu4' => $request->checked_by_2,
                    'tanggal_dibuat_minggu4' => $tanggal
                ]);
            }
            // Hapus blok elseif yang mereset nilai checker
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('success', 'Data berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            // Log error untuk debugging
            Log::error('Error saat memperbarui data vacuum cleaner: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            // Ambil data utama vacuum cleaner check
            $check = VacumCleanerCheck::findOrFail($id);
            
            // Ambil data hasil dari kedua tabel
            $resultsTable1 = VacumCleanerResultsTable1::where('check_id', $id)->get();
            $resultsTable2 = VacumCleanerResultsTable2::where('check_id', $id)->get();
            
            // Siapkan collection untuk menyimpan semua hasil dalam format terstruktur
            $results = collect();
            
            // Definisikan item-item yang diperiksa untuk vacuum cleaner
            $itemsMap = [
                1 => 'Kebersihan Body',
                2 => 'Motor',
                3 => 'Selang',
                4 => 'Aksesoris',
                5 => 'Filter',
                6 => 'Bostel',
                7 => 'Kabel',
            ];
            
            // Proses data dari tabel 1 (data minggu 2)
            foreach ($resultsTable1 as $row) {
                $itemId = array_search($row->checked_items, $itemsMap);
                
                if ($itemId) {
                    $results->push([
                        'minggu' => 2,
                        'item_id' => $itemId,
                        'item_name' => $row->checked_items,
                        'result' => $row->minggu2,
                        'keterangan' => $row->keterangan_minggu2,
                        'checked_by' => $check->checker_minggu1,
                        'approved_by' => $check->approver_minggu1
                    ]);
                }
            }
            
            // Proses data dari tabel 2 (data minggu 4)
            foreach ($resultsTable2 as $row) {
                $itemId = array_search($row->checked_items, $itemsMap);
                
                if ($itemId) {
                    $results->push([
                        'minggu' => 4,
                        'item_id' => $itemId,
                        'item_name' => $row->checked_items,
                        'result' => $row->minggu4,
                        'keterangan' => $row->keterangan_minggu4,
                        'checked_by' => $check->checker_minggu2,
                        'approved_by' => $check->approver_minggu2
                    ]);
                }
            }
            
            // Kelompokkan hasil berdasarkan minggu untuk akses yang lebih mudah di view
            $groupedResults = $results->groupBy('minggu');
            
            // Periksa minggu mana yang memiliki checker
            $check_num_1 = $check->checker_minggu1 ? 1 : null;
            $check_num_2 = $check->checker_minggu2 ? 2 : null;
            
            // Siapkan semua data yang dibutuhkan untuk view
            $data = [
                'check' => $check,
                'results' => $results,
                'groupedResults' => $groupedResults,
                'itemsMap' => $itemsMap,
                'check_num_1' => $check_num_1,
                'check_num_2' => $check_num_2,
            ];
            
            return view('vacuum_cleaner.show', $data);
            
        } catch (\Exception $e) {
            // Catat detail error untuk debugging
            Log::error('Error saat menampilkan data vacuum cleaner: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('error', 'Terjadi kesalahan saat menampilkan data: ' . $e->getMessage());
        }
    }

    public function approve(Request $request, $id)
    {
        // Validasi input data
        $request->validate([
            'approved_by_minggu2' => 'sometimes|string',
            'approved_by_minggu4' => 'sometimes|string',
            'approve_minggu2' => 'sometimes|string',
            'approve_minggu4' => 'sometimes|string',
        ]);
    
        try {
            // Ambil data VacumCleanerCheck berdasarkan ID
            $check = VacumCleanerCheck::findOrFail($id);
            $updated = false; // Flag untuk menandakan apakah ada data yang diupdate
            
            // Update penanggung jawab minggu ke-2 jika ada
            if ($request->has('approve_minggu2') && $request->approve_minggu2 == '2') {
                $check->approver_minggu2 = $request->approved_by_minggu2;
                $updated = true;
            }
            
            // Update penanggung jawab minggu ke-4 jika ada
            if ($request->has('approve_minggu4') && $request->approve_minggu4 == '4') {
                $check->approver_minggu4 = $request->approved_by_minggu4;
                $updated = true;
            }
            
            // Simpan perubahan jika ada data yang diupdate
            if ($updated) {
                $check->save();
                return redirect()->route('vacuum-cleaner.index')
                    ->with('success', 'Data penanggung jawab berhasil disimpan!');
            } else {
                // Jika tidak ada data yang diupdate, berikan pesan peringatan
                return redirect()->back()
                    ->with('warning', 'Tidak ada data penanggung jawab yang dipilih untuk disimpan.');
            }
                    
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
