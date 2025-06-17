<?php

namespace App\Http\Controllers;
use App\Models\AutoloaderCheck;
use App\Models\AutoloaderDetail;
use App\Models\AutoloaderResultTable1;
use App\Models\AutoloaderResultTable2;
use App\Models\AutoloaderResultTable3;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF
use App\Traits\WithAuthentication;

use Illuminate\Http\Request;

class AutoloaderController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $query = AutoloaderCheck::query();
    
        // Filter berdasarkan checker username jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('checkerAndApprover.checker', function ($q) use ($search) {
                $q->where('username', 'LIKE', $search);
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

        // Urutkan berdasarkan data terbaru
        $query->orderBy('created_at', 'desc');
    
        // Ambil data dengan paginasi
        $checks = $query->with('checkerAndApprover')->paginate(10)->appends($request->query());
        
        // Load all unique checkers for each check
        foreach ($checks as $check) {
            // Get all unique checker IDs
            $checkerIds = AutoloaderDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('checker_id')
                ->pluck('checker_id')
                ->unique()
                ->toArray();
            // Ambil username dari id checker
            $check->allCheckers = [];
            if (!empty($checkerIds)) {
                $check->allCheckers = \App\Models\Checker::whereIn('id', $checkerIds)->pluck('username')->toArray();
            }
            
            // Get year and month from bulan field
            $year = substr($check->bulan, 0, 4);
            $month = substr($check->bulan, 5, 2);
            
            // Calculate days in month
            $check->daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            
            // Count checked dates
            $check->filledDatesCount = AutoloaderDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('checker_id')
                ->count();
            
            // Count approved dates
            $check->approvedDatesCount = AutoloaderDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('approver_id')
                ->count();
                
            // Debug output - remove this in production
            // \Log::debug("Check ID: {$check->id}, Days in month: {$check->daysInMonth}, Approved count: {$check->approvedDatesCount}");
        }
    
        return view('autoloader.index', compact('checks', 'user', 'currentGuard'));
    }
    
    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        return view('autoloader.create', compact('user', 'currentGuard'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        // Custom error messages untuk validasi
        $customMessages = [
            'nomer_autoloader.required' => 'Silakan pilih nomor autoloader terlebih dahulu!',
            'shift.required' => 'Silakan pilih shift terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!'
        ];

        // Validate input dengan custom messages
        $validated = $request->validate([
            'nomer_autoloader' => 'required|integer|between:1,23',
            'shift' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ], $customMessages);

        // Check for duplicate record
        $existingRecord = AutoloaderCheck::where('nomer_autoloader', $request->nomer_autoloader)
            ->where('shift', $request->shift)
            ->where('bulan', $request->bulan)
            ->first();

        if ($existingRecord) {
            // Format bulan dari Y-m menjadi nama bulan dan tahun (contoh: Mei 2025)
            $formattedMonth = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Pesan error dengan detail data duplikat
            $errorMessage = "Data duplikat ditemukan untuk Autoloader Nomor {$request->nomer_autoloader}, Shift {$request->shift}, dan Bulan {$formattedMonth}!";
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
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
            
            // Process checker_id information for all days (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $checkerKey = "checker_id_{$i}";
                if ($request->has($checkerKey) && !empty($request->$checkerKey)) {
                    // Cari ID checker berdasarkan username
                    $checker = \App\Models\Checker::where('username', $request->$checkerKey)->first();
                    if ($checker) {
                        AutoloaderDetail::create([
                            'tanggal_check_id' => $checkId,
                            'tanggal' => $i, // Using the column number as the day
                            'checker_id' => $checker->id,
                            'approver_id' => null, // Approval would be handled separately
                        ]);
                    }
                }
            }
            
            // LOG AKTIVITAS - Tambahkan setelah data berhasil disimpan
            $formattedMonth = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            $shiftText = "Shift " . $request->shift;
            
            Activity::logActivity(
                'checker',                                              // user_type
                $user->id,                                       // user_id
                $user->username,                                 // user_name
                'created',                                              // action
                'Checker ' . $user->username . ' membuat pemeriksaan Autoloader Nomor ' . $request->nomer_autoloader . ' untuk ' . $shiftText . ' bulan ' . $formattedMonth,  // description
                'autoloader_check',                                     // target_type
                $autoloaderCheck->id,                                   // target_id
                [
                    'nomer_autoloader' => $request->nomer_autoloader,
                    'shift' => $request->shift,
                    'bulan' => $request->bulan,
                    'bulan_formatted' => $formattedMonth,
                    'total_items' => count($items),
                    'items_checked' => array_values($items),
                    'status' => $autoloaderCheck->status ?? 'belum_disetujui'
                ]                                                       // details (JSON)
            );
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('autoloader.index')
                ->with('success', 'Data berhasil disimpan!');
                
        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Gunakan trait untuk mendapatkan model berdasarkan hashid
        $check = app(AutoloaderCheck::class)->resolveRouteBinding($hashid);
        $id = $check->id; // Simpan ID untuk kompatibilitas dengan kode yang ada
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = AutoloaderResultTable1::where('check_id', $id)->get();
        $resultsTable2 = AutoloaderResultTable2::where('check_id', $id)->get();
        $resultsTable3 = AutoloaderResultTable3::where('check_id', $id)->get();
        
        // Ambil data detail (checker_id dan approver_id)
        $detailChecks = AutoloaderDetail::with('checker')->where('tanggal_check_id', $id)->get();
        
        // Siapkan data untuk view dalam format yang sesuai dengan helper function
        $results = collect();
        
        // Buat array untuk menyimpan data checker username berdasarkan tanggal
        $checkerData = [];
        // Buat array untuk menyimpan data approver username berdasarkan tanggal
        $approverData = [];
        // Proses data checker_id dan approver_id dulu agar tersedia untuk digunakan kemudian
        foreach ($detailChecks as $detail) {
            $checkerData[$detail->tanggal] = $detail->checker ? $detail->checker->username : null;
            $approverData[$detail->tanggal] = $detail->approver ? $detail->approver->username : '';
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
                        // Cek apakah ada data checker_id dan approver_id untuk tanggal ini
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checker_id' => $checker,
                            'approver_id' => $approver
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
                        // Cek apakah ada data checker_id dan approver_id untuk tanggal ini
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checker_id' => $checker,
                            'approver_id' => $approver
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
                        // Cek apakah ada data checker_id dan approver_id untuk tanggal ini
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checker_id' => $checker,
                            'approver_id' => $approver
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checker_id dan approver_id untuk tanggal yang mungkin belum memiliki item
        for ($j = 1; $j <= 31; $j++) {
            if (isset($checkerData[$j]) && !$results->where('tanggal', $j)->where('checker_id', '!=', null)->count()) {
                $results->push([
                    'tanggal' => $j,
                    'checker_id' => $checkerData[$j],
                    'approver_id' => $approverData[$j] ?? ''
                ]);
            }
        }
        
        return view('autoloader.edit', compact('check', 'results', 'user', 'currentGuard'));
    }

    public function update(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Validasi input
        $validated = $request->validate([
            'nomer_autoloader' => 'required|integer|between:1,23',
            'shift' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Model AutoloaderCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $autoloaderCheck = (new AutoloaderCheck)->resolveRouteBinding($hashid);

        // Cek apakah ada perubahan pada data utama (nomer_autoloader, shift, bulan)
        if ($autoloaderCheck->nomer_autoloader != $request->nomer_autoloader || 
            $autoloaderCheck->shift != $request->shift || 
            $autoloaderCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = AutoloaderCheck::where('nomer_autoloader', $request->nomer_autoloader)
                ->where('shift', $request->shift)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $autoloaderCheck->id) // Kecualikan record saat ini
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
            $existingTable1Data = AutoloaderResultTable1::where('check_id', $autoloaderCheck->id)->get()->keyBy('checked_items');
            $existingTable2Data = AutoloaderResultTable2::where('check_id', $autoloaderCheck->id)->get()->keyBy('checked_items');
            $existingTable3Data = AutoloaderResultTable3::where('check_id', $autoloaderCheck->id)->get()->keyBy('checked_items');
            
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
                    $resultData1['check_id'] = $autoloaderCheck->id;
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
                    $resultData2['check_id'] = $autoloaderCheck->id;
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
                    $resultData3['check_id'] = $autoloaderCheck->id;
                    $resultData3['checked_items'] = $itemName;
                    AutoloaderResultTable3::create($resultData3);
                }
            }
            
            // Ambil data checker_id yang sudah ada
            $existingDetails = AutoloaderDetail::where('tanggal_check_id', $autoloaderCheck->id)
                ->get()
                ->keyBy('tanggal');
            
            // Proses informasi checker untuk semua hari (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $checkerKey = "checked_by_{$i}";
                if ($request->has($checkerKey) && !empty($request->$checkerKey)) {
                    // Cari ID checker berdasarkan username
                    $checker = \App\Models\Checker::where('username', $request->$checkerKey)->first();
                    $detailData = [
                        'checker_id' => $checker ? $checker->id : null,
                    ];
                    $existingDetail = $existingDetails->get($i);
                    if ($existingDetail) {
                        // Update data yang sudah ada
                        $existingDetail->update($detailData);
                    } else {
                        // Buat data baru
                        $detailData['tanggal_check_id'] = $autoloaderCheck->id;
                        $detailData['tanggal'] = $i;
                        $detailData['approver_id'] = null;
                        AutoloaderDetail::create($detailData);
                    }
                } elseif ($existingDetails->has($i)) {
                    // Jika tidak ada data di form tapi ada di database, update nilai checker_id menjadi null
                    $existingDetails->get($i)->update(['checker_id' => null]);
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('autoloader.index')
                ->with('success', 'Data berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model AutoloaderCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new AutoloaderCheck)->resolveRouteBinding($hashid);
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = AutoloaderResultTable1::where('check_id', $check->id)->get();
        $resultsTable2 = AutoloaderResultTable2::where('check_id', $check->id)->get();
        $resultsTable3 = AutoloaderResultTable3::where('check_id', $check->id)->get();
        
        // Ambil data detail dengan eager loading untuk checker dan approver
        $detailChecks = AutoloaderDetail::with(['checker', 'approver'])
            ->where('tanggal_check_id', $check->id)
            ->get();
        
        // Siapkan data untuk view dalam format yang sesuai dengan helper function
        $results = collect();
        
        // Buat array untuk menyimpan data checker dan approver berdasarkan tanggal
        $checkerData = [];
        $approverData = [];
        
        // Proses data checker dan approver dengan username
        foreach ($detailChecks as $detail) {
            $checkerData[$detail->tanggal] = [
                'id' => $detail->checker_id,
                'username' => $detail->checker ? $detail->checker->username : null
            ];
            $approverData[$detail->tanggal] = [
                'id' => $detail->approver_id,
                'username' => $detail->approver ? $detail->approver->username : null
            ];
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
            
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checker_id' => $checker['id'] ?? null,
                            'checker_username' => $checker['username'] ?? null,
                            'approver_id' => $approver['id'] ?? null,
                            'approver_username' => $approver['username'] ?? null
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
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checker_id' => $checker['id'] ?? null,
                            'checker_username' => $checker['username'] ?? null,
                            'approver_id' => $approver['id'] ?? null,
                            'approver_username' => $approver['username'] ?? null
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
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $row->$keteranganField,
                            'checker_id' => $checker['id'] ?? null,
                            'checker_username' => $checker['username'] ?? null,
                            'approver_id' => $approver['id'] ?? null,
                            'approver_username' => $approver['username'] ?? null
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data untuk tanggal yang hanya memiliki checker/approver tanpa item
        for ($j = 1; $j <= 31; $j++) {
            if ((isset($checkerData[$j]) || isset($approverData[$j])) && 
                !$results->where('tanggal', $j)->where('checker_id', '!=', null)->count()) {
                $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                
                $results->push([
                    'tanggal' => $j,
                    'checker_id' => $checker['id'] ?? null,
                    'checker_username' => $checker['username'] ?? null,
                    'approver_id' => $approver['id'] ?? null,
                    'approver_username' => $approver['username'] ?? null
                ]);
            }
        }
        
        return view('autoloader.show', compact('check', 'results', 'user', 'currentGuard'));
    }

    public function approve(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser(['approver']);
        if (!is_object($user)) return $user;
        if (!$this->isAuthenticatedAs('approver')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
        }
        
        // Validasi input
        $request->validate([
            'approver_id_*' => 'sometimes|string',
            'approve_num_*' => 'sometimes|integer|between:1,31',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Model AutoloaderCheck akan otomatis resolve hashid menjadi model instance
            // karena menggunakan trait Hashidable
            $check = (new AutoloaderCheck)->resolveRouteBinding($hashid);
            
            // Proses informasi penanggung jawab untuk semua hari (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $approverKey = "approver_id_{$i}";
                $approveNumKey = "approve_num_{$i}";
                
                if ($request->has($approverKey) && !empty($request->input($approverKey))) {
                    // Cari detail yang sudah ada untuk tanggal ini
                    $detail = AutoloaderDetail::where('tanggal_check_id', $check->id)
                        ->where('tanggal', $i)
                        ->first();
                    
                    if ($detail) {
                        // Update jika detail sudah ada
                        $detail->update([
                            'approver_id' => $request->$approverKey
                        ]);
                    } else {
                        // Buat baru jika tidak ada detail
                        AutoloaderDetail::create([
                            'tanggal_check_id' => $check->id,
                            'tanggal' => $i,
                            'checker_id' => null, // Checker akan diisi nanti
                            'approver_id' => $request->$approverKey,
                        ]);
                    }
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('autoloader.index')
                ->with('success', 'Data penanggung jawab berhasil disimpan!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika ada kesalahan
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reviewPdf($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model AutoloaderCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new AutoloaderCheck)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait
        $form = Form::findOrFail(10); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = AutoloaderResultTable1::where('check_id', $check->id)->get();
        $resultsTable2 = AutoloaderResultTable2::where('check_id', $check->id)->get();
        $resultsTable3 = AutoloaderResultTable3::where('check_id', $check->id)->get();
        
        // Ambil data detail dengan relasi ke user checker dan approver
        $detailChecks = AutoloaderDetail::with(['checker:id,username', 'approver:id,username'])
            ->where('tanggal_check_id', $check->id)
            ->get();
        
        // Siapkan data untuk view dalam format yang sesuai
        $results = collect();
        
        // Buat array untuk menyimpan data checker dan approver berdasarkan tanggal
        $checkerData = [];
        $approverData = [];
        $checkerNames = [];
        $approverNames = [];
        
        // Proses data checker_id dan approver_id serta ambil username
        foreach ($detailChecks as $detail) {
            $checkerData[$detail->tanggal] = $detail->checker_id;
            $approverData[$detail->tanggal] = $detail->approver_id ?? '';
            
            // Ambil username checker dan approver
            $checkerNames[$detail->tanggal] = $detail->checker ? $detail->checker->username : null;
            $approverNames[$detail->tanggal] = $detail->approver ? $detail->approver->username : null;
        }
        
        // Define the checked items for Autoloader
        $items = [
            1 => 'Filter',
            2 => 'Selang',
            3 => 'Panel Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Thermal Overload',
            6 => 'MCB',
        ];
        
        // Proses data dari tabel 1 (tanggal 1-11)
        foreach ($resultsTable1 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        $checkerName = isset($checkerNames[$j]) ? $checkerNames[$j] : null;
                        $approverName = isset($approverNames[$j]) ? $approverNames[$j] : null;
                        
                        $keterangan = isset($row->$keteranganField) ? $row->$keteranganField : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $keterangan,
                            'checker_id' => $checker,
                            'approver_id' => $approver,
                            'checker_name' => $checkerName,
                            'approver_name' => $approverName
                        ]);
                    }
                }
            }
        }
        
        // Proses data dari tabel 2 (tanggal 12-22)
        foreach ($resultsTable2 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 12; $j <= 22; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        $checkerName = isset($checkerNames[$j]) ? $checkerNames[$j] : null;
                        $approverName = isset($approverNames[$j]) ? $approverNames[$j] : null;
                        
                        $keterangan = isset($row->$keteranganField) ? $row->$keteranganField : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $keterangan,
                            'checker_id' => $checker,
                            'approver_id' => $approver,
                            'checker_name' => $checkerName,
                            'approver_name' => $approverName
                        ]);
                    }
                }
            }
        }
        
        // Proses data dari tabel 3 (tanggal 23-31)
        foreach ($resultsTable3 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 23; $j <= 31; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        $checkerName = isset($checkerNames[$j]) ? $checkerNames[$j] : null;
                        $approverName = isset($approverNames[$j]) ? $approverNames[$j] : null;
                        
                        $keterangan = isset($row->$keteranganField) ? $row->$keteranganField : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $keterangan,
                            'checker_id' => $checker,
                            'approver_id' => $approver,
                            'checker_name' => $checkerName,
                            'approver_name' => $approverName
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checker_id dan approver_id untuk tanggal yang belum memiliki item
        for ($j = 1; $j <= 31; $j++) {
            if (isset($checkerData[$j]) && !$results->where('tanggal', $j)->where('checker_id', '!=', null)->count()) {
                $results->push([
                    'tanggal' => $j,
                    'checker_id' => $checkerData[$j],
                    'approver_id' => $approverData[$j] ?? '',
                    'checker_name' => $checkerNames[$j] ?? null,
                    'approver_name' => $approverNames[$j] ?? null
                ]);
            }
        }
        
        // Render view sebagai HTML untuk preview PDF
        return view('autoloader.review_pdf', compact('check', 'results', 'form', 'formattedTanggalEfektif', 'items', 'user', 'currentGuard'));
    }

    public function downloadPdf($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model AutoloaderCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new AutoloaderCheck)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait
        $form = Form::findOrFail(10); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = AutoloaderResultTable1::where('check_id', $check->id)->get();
        $resultsTable2 = AutoloaderResultTable2::where('check_id', $check->id)->get();
        $resultsTable3 = AutoloaderResultTable3::where('check_id', $check->id)->get();
        
        // Ambil data detail dengan relasi ke user checker dan approver
        $detailChecks = AutoloaderDetail::with(['checker:id,username', 'approver:id,username'])
            ->where('tanggal_check_id', $check->id)
            ->get();
        
        // Siapkan data untuk view dalam format yang sesuai
        $results = collect();
        
        // Buat array untuk menyimpan data checker dan approver berdasarkan tanggal
        $checkerData = [];
        $approverData = [];
        $checkerNames = [];
        $approverNames = [];
        
        // Proses data checker_id dan approver_id serta ambil username
        foreach ($detailChecks as $detail) {
            $checkerData[$detail->tanggal] = $detail->checker_id;
            $approverData[$detail->tanggal] = $detail->approver_id ?? '';
            
            // Ambil username checker dan approver
            $checkerNames[$detail->tanggal] = $detail->checker ? $detail->checker->username : null;
            $approverNames[$detail->tanggal] = $detail->approver ? $detail->approver->username : null;
        }
        
        // Define the checked items for Autoloader
        $items = [
            1 => 'Filter',
            2 => 'Selang',
            3 => 'Panel Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Thermal Overload',
            6 => 'MCB',
        ];
        
        // Proses data dari tabel 1 (tanggal 1-11)
        foreach ($resultsTable1 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        $checkerName = isset($checkerNames[$j]) ? $checkerNames[$j] : null;
                        $approverName = isset($approverNames[$j]) ? $approverNames[$j] : null;
                        
                        $keterangan = isset($row->$keteranganField) ? $row->$keteranganField : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $keterangan,
                            'checker_id' => $checker,
                            'approver_id' => $approver,
                            'checker_name' => $checkerName,
                            'approver_name' => $approverName
                        ]);
                    }
                }
            }
        }
        
        // Proses data dari tabel 2 (tanggal 12-22)
        foreach ($resultsTable2 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 12; $j <= 22; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        $checkerName = isset($checkerNames[$j]) ? $checkerNames[$j] : null;
                        $approverName = isset($approverNames[$j]) ? $approverNames[$j] : null;
                        
                        $keterangan = isset($row->$keteranganField) ? $row->$keteranganField : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $keterangan,
                            'checker_id' => $checker,
                            'approver_id' => $approver,
                            'checker_name' => $checkerName,
                            'approver_name' => $approverName
                        ]);
                    }
                }
            }
        }
        
        // Proses data dari tabel 3 (tanggal 23-31)
        foreach ($resultsTable3 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 23; $j <= 31; $j++) {
                    $tanggalField = "tanggal{$j}";
                    $keteranganField = "keterangan_tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $checker = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approver = isset($approverData[$j]) ? $approverData[$j] : null;
                        $checkerName = isset($checkerNames[$j]) ? $checkerNames[$j] : null;
                        $approverName = isset($approverNames[$j]) ? $approverNames[$j] : null;
                        
                        $keterangan = isset($row->$keteranganField) ? $row->$keteranganField : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'keterangan' => $keterangan,
                            'checker_id' => $checker,
                            'approver_id' => $approver,
                            'checker_name' => $checkerName,
                            'approver_name' => $approverName
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checker_id dan approver_id untuk tanggal yang belum memiliki item
        for ($j = 1; $j <= 31; $j++) {
            if (isset($checkerData[$j]) && !$results->where('tanggal', $j)->where('checker_id', '!=', null)->count()) {
                $results->push([
                    'tanggal' => $j,
                    'checker_id' => $checkerData[$j],
                    'approver_id' => $approverData[$j] ?? '',
                    'checker_name' => $checkerNames[$j] ?? null,
                    'approver_name' => $approverNames[$j] ?? null
                ]);
            }
        }
        
        // Generate nama file PDF
        $bulanNama = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        $bulanFormat = date('m', strtotime($check->bulan));
        $tahun = date('Y', strtotime($check->bulan));
        $bulanText = $bulanNama[$bulanFormat];
        
        $filename = 'Autoloader_nomer_' . $check->nomer_autoloader . '_shift_' . $check->shift . '_' . $bulanText . '_' . $tahun . '.pdf';
        
        // Render view sebagai HTML
        $html = view('autoloader.review_pdf', compact('check', 'results', 'form', 'formattedTanggalEfektif', 'items', 'user', 'currentGuard'))->render();
        
        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Atur ukuran dan orientasi halaman (opsional)
        $dompdf->setPaper('A4', 'potrait');
        
        // Render PDF (mengubah HTML menjadi PDF)
        $dompdf->render();
        
        // Download file PDF
        return $dompdf->stream($filename, [
            'Attachment' => false, // Set true untuk download, false untuk preview di browser
        ]);
    }
}