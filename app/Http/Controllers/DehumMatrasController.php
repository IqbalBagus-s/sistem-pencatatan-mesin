<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DehumMatrasCheck;
use App\Models\DehumMatrasDetail;
use App\Models\DehumMatrasResultsTable1;
use App\Models\DehumMatrasResultsTable2;
use App\Models\DehumMatrasResultsTable3;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use App\Traits\WithAuthentication;

class DehumMatrasController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $query = DehumMatrasCheck::query();

        // Filter berdasarkan checker_id atau approver_id jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('detail', function ($q) use ($search) {
                $q->where('checker_id', 'LIKE', $search)
                ->orWhere('approver_id', 'LIKE', $search);
            });
        }

        // Filter berdasarkan nomor dehum matras
        if ($request->filled('search_dehum_matras')) {
            $query->where('nomer_dehum_matras', $request->search_dehum_matras);
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

        $query->orderBy('created_at', 'desc');

        // Ambil data dengan paginasi
        $checks = $query->with('detail')->paginate(10)->appends($request->query());
        
        // Load informasi tambahan untuk setiap check
        foreach ($checks as $check) {
            // Get all unique checkers
            $checkerIds = DehumMatrasDetail::where('tanggal_check_id', $check->id)
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
            $check->filledDatesCount = DehumMatrasDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('checker_id')
                ->count();
            
            // Count approved dates
            $check->approvedDatesCount = DehumMatrasDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('approver_id')
                ->count();
        }

        return view('dehum-matras.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        return view('dehum-matras.create', compact('user', 'currentGuard'));
    }
    
    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        // Custom error messages untuk validasi
        $customMessages = [
            'nomer_dehum_matras.required' => 'Silakan pilih nomor dehum matras terlebih dahulu!',
            'shift.required' => 'Silakan pilih shift terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!'
        ];

        // Validate input dengan custom messages
        $validated = $request->validate([
            'nomer_dehum_matras' => 'required|integer|between:1,23',
            'shift' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ], $customMessages);

        // Check for duplicate record
        $existingRecord = DehumMatrasCheck::where('nomer_dehum_matras', $request->nomer_dehum_matras)
            ->where('shift', $request->shift)
            ->where('bulan', $request->bulan)
            ->first();
        
        if ($existingRecord) {
            // Format bulan dari Y-m menjadi nama bulan dan tahun (contoh: Mei 2025)
            $formattedMonth = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Pesan error dengan detail data duplikat
            $errorMessage = "Data duplikat ditemukan untuk Dehum Matras Nomor {$request->nomer_dehum_matras}, Shift {$request->shift}, dan Bulan {$formattedMonth}!";
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create Dehum Matras Check record
            $dehumMatrasCheck = DehumMatrasCheck::create([
                'nomer_dehum_matras' => $request->nomer_dehum_matras,
                'shift' => $request->shift,
                'bulan' => $request->bulan,
            ]);
            
            // Get the ID of the newly created record
            $checkId = $dehumMatrasCheck->id;
            
            // Define the checked items for Dehum Matras
            $items = [
                1 => 'Kompressor',
                2 => 'Kabel',
                3 => 'NFB',
                4 => 'Motor',
                5 => 'Water Cooler in',
                6 => 'Water Cooler Out',
                7 => 'Temperatur Output Udara',
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
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData1["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData1["tanggal{$j}"] = null; // Default value
                    }
                }
                
                // Process checks for Table 2 (days 12-22)
                for ($j = 12; $j <= 22; $j++) {
                    $checkKey = "check_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData2["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData2["tanggal{$j}"] = null; // Default value
                    }
                }
                
                // Process checks for Table 3 (days 23-31)
                for ($j = 23; $j <= 31; $j++) {
                    $checkKey = "check_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData3["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData3["tanggal{$j}"] = null; // Default value
                    }
                }
                
                // Create the result records for all tables
                DehumMatrasResultsTable1::create($resultData1);
                DehumMatrasResultsTable2::create($resultData2);
                DehumMatrasResultsTable3::create($resultData3);
            }
            
            // Process checker_id information for all days (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $checkerIdKey = "checker_id_{$i}";
                
                if ($request->has($checkerIdKey) && !empty($request->$checkerIdKey)) {
                    DehumMatrasDetail::create([
                        'tanggal_check_id' => $checkId,
                        'tanggal' => $i, // Using the column number as the day
                        'checker_id' => $request->$checkerIdKey,
                        'approver_id' => null, // Approval would be handled separately
                    ]);
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
                'Checker ' . $user->username . ' membuat pemeriksaan Dehum Matras Nomor ' . $request->nomer_dehum_matras . ' untuk ' . $shiftText . ' bulan ' . $formattedMonth,  // description
                'dehum_matras_check',                                   // target_type
                $dehumMatrasCheck->id,                                  // target_id
                [
                    'nomer_dehum_matras' => $request->nomer_dehum_matras,
                    'shift' => $request->shift,
                    'bulan' => $request->bulan,
                    'bulan_formatted' => $formattedMonth,
                    'total_items' => count($items),
                    'items_checked' => array_values($items),
                    'status' => $dehumMatrasCheck->status ?? 'belum_disetujui'
                ]                                                       // details (JSON)
            );
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('dehum-matras.index')
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
        
        // Model DehumMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new DehumMatrasCheck)->resolveRouteBinding($hashid);
        
        // Get the real ID untuk query lainnya
        $id = $check->id;
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = DehumMatrasResultsTable1::where('check_id', $id)->get();
        $resultsTable2 = DehumMatrasResultsTable2::where('check_id', $id)->get();
        $resultsTable3 = DehumMatrasResultsTable3::where('check_id', $id)->get();
        
        // Ambil data detail dengan join untuk mendapatkan username dari model Checker dan Approver
        $detailChecks = DehumMatrasDetail::where('tanggal_check_id', $id)
            ->leftJoin('checkers', 'dehum_matras_details.checker_id', '=', 'checkers.id')
            ->leftJoin('approvers', 'dehum_matras_details.approver_id', '=', 'approvers.id')
            ->select(
                'dehum_matras_details.*',
                'checkers.username as checker_username',
                'approvers.username as approver_username'
            )
            ->get();
        
        // Siapkan data untuk view dalam format yang sesuai dengan helper function
        $results = collect();
        
        // Define the checked items for Dehum Matras
        $items = [
            1 => 'Kompressor',
            2 => 'Kabel',
            3 => 'NFB',
            4 => 'Motor',
            5 => 'Water Cooler in',
            6 => 'Water Cooler Out',
            7 => 'Temperatur Output Udara',
        ];
        
        // Proses data dari tabel 1 (tanggal 1-11)
        foreach ($resultsTable1 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
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
                    
                    if (isset($row->$tanggalField)) {
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
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
                    
                    if (isset($row->$tanggalField)) {
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checker dan approver untuk setiap tanggal
        foreach ($detailChecks as $detail) {
            // Cari apakah sudah ada data untuk tanggal ini
            $existingData = $results->where('tanggal', $detail->tanggal)->first();
            
            if ($existingData) {
                // Update data yang sudah ada dengan informasi checker dan approver
                $results = $results->map(function ($item) use ($detail) {
                    if ($item['tanggal'] == $detail->tanggal) {
                        $item['checked_by'] = $detail->checker_username; // Gunakan username untuk tampilan
                        $item['checker_id'] = $detail->checker_id; // Tambahkan checker_id untuk form
                        $item['approved_by'] = $detail->approver_username; // Gunakan username untuk tampilan
                        $item['approver_id'] = $detail->approver_id; // Tambahkan approver_id untuk form
                    }
                    return $item;
                });
            } else {
                // Tambahkan data baru jika belum ada
                $results->push([
                    'tanggal' => $detail->tanggal,
                    'checked_by' => $detail->checker_username,
                    'checker_id' => $detail->checker_id,
                    'approved_by' => $detail->approver_username,
                    'approver_id' => $detail->approver_id
                ]);
            }
        }
        
        return view('dehum-matras.edit', compact('check', 'results', 'user', 'currentGuard'));
    }
    
    public function update(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Validasi input
        $validated = $request->validate([
            'nomer_dehum_matras' => 'required|integer|between:1,23',
            'shift' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Model DehumMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $dehumMatrasCheck = (new DehumMatrasCheck)->resolveRouteBinding($hashid);

        // Cek apakah ada perubahan pada data utama (nomer_dehum_matras, shift, bulan)
        if ($dehumMatrasCheck->nomer_dehum_matras != $request->nomer_dehum_matras || 
            $dehumMatrasCheck->shift != $request->shift || 
            $dehumMatrasCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = DehumMatrasCheck::where('nomer_dehum_matras', $request->nomer_dehum_matras)
                ->where('shift', $request->shift)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $dehumMatrasCheck->id) // Kecualikan record saat ini
                ->first();
            
            if ($existingRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data dengan nomor dehum matras, shift, dan bulan yang sama sudah ada!');
            }
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Update data DehumMatrasCheck
            $dehumMatrasCheck->update([
                'nomer_dehum_matras' => $request->nomer_dehum_matras,
                'shift' => $request->shift,
                'bulan' => $request->bulan,
            ]);
            
            // Definisikan items yang diperiksa berdasarkan fungsi edit
            $items = [
                1 => 'Kompressor',
                2 => 'Kabel',
                3 => 'NFB',
                4 => 'Motor',
                5 => 'Water Cooler in',
                6 => 'Water Cooler Out',
                7 => 'Temperatur Output Udara',
            ];
            
            // Ambil data existing dari ketiga tabel
            $existingTable1Data = DehumMatrasResultsTable1::where('check_id', $dehumMatrasCheck->id)->get()->keyBy('checked_items');
            $existingTable2Data = DehumMatrasResultsTable2::where('check_id', $dehumMatrasCheck->id)->get()->keyBy('checked_items');
            $existingTable3Data = DehumMatrasResultsTable3::where('check_id', $dehumMatrasCheck->id)->get()->keyBy('checked_items');
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Update atau buat record untuk tabel 1 (hari 1-11)
                $table1Record = $existingTable1Data->get($itemName);
                $resultData1 = [];
                
                for ($j = 1; $j <= 11; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        // Jika ada data di request, gunakan nilai tersebut
                        $resultData1["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        // Jika tidak ada data di request, gunakan null
                        $resultData1["tanggal{$j}"] = null;
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
                    $resultData1['check_id'] = $dehumMatrasCheck->id;
                    $resultData1['checked_items'] = $itemName;
                    DehumMatrasResultsTable1::create($resultData1);
                }
                
                // Update atau buat record untuk tabel 2 (hari 12-22)
                $table2Record = $existingTable2Data->get($itemName);
                $resultData2 = [];
                
                for ($j = 12; $j <= 22; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        // Jika ada data di request, gunakan nilai tersebut
                        $resultData2["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        // Jika tidak ada data di request, gunakan null
                        $resultData2["tanggal{$j}"] = null;
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
                    $resultData2['check_id'] = $dehumMatrasCheck->id;
                    $resultData2['checked_items'] = $itemName;
                    DehumMatrasResultsTable2::create($resultData2);
                }
                
                // Update atau buat record untuk tabel 3 (hari 23-31)
                $table3Record = $existingTable3Data->get($itemName);
                $resultData3 = [];
                
                for ($j = 23; $j <= 31; $j++) {
                    $checkKey = "check_{$j}";
                    $keteranganKey = "keterangan_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        // Jika ada data di request, gunakan nilai tersebut
                        $resultData3["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        // Jika tidak ada data di request, gunakan null
                        $resultData3["tanggal{$j}"] = null;
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
                    $resultData3['check_id'] = $dehumMatrasCheck->id;
                    $resultData3['checked_items'] = $itemName;
                    DehumMatrasResultsTable3::create($resultData3);
                }
            }
            
            // Ambil data checker_id dan approver_id yang sudah ada
            $existingDetails = DehumMatrasDetail::where('tanggal_check_id', $dehumMatrasCheck->id)
                ->get()
                ->keyBy('tanggal');
            // Proses informasi checker_id dan approver_id untuk semua hari (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $checkerIdKey = "checker_id_{$i}";
                
                // Hanya proses checker_id, abaikan approver_id
                if ($request->has($checkerIdKey) && !empty($request->$checkerIdKey)) {
                    $detailData = [
                        'checker_id' => $request->$checkerIdKey
                    ];
                    
                    $existingDetail = $existingDetails->get($i);
                    if ($existingDetail) {
                        $existingDetail->update($detailData);
                    } else {
                        $detailData['tanggal_check_id'] = $dehumMatrasCheck->id;
                        $detailData['tanggal'] = $i;
                        DehumMatrasDetail::create($detailData);
                    }
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('dehum-matras.index')
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
        
        // Model DehumMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new DehumMatrasCheck)->resolveRouteBinding($hashid);
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = DehumMatrasResultsTable1::where('check_id', $check->id)->get();
        $resultsTable2 = DehumMatrasResultsTable2::where('check_id', $check->id)->get();
        $resultsTable3 = DehumMatrasResultsTable3::where('check_id', $check->id)->get();
        
        // Ambil data detail dengan eager loading untuk checker dan approver
        $detailChecks = DehumMatrasDetail::with(['checker', 'approver'])
            ->where('tanggal_check_id', $check->id)
            ->get();
        
        // Siapkan data untuk view dalam format yang sesuai dengan helper function
        $results = collect();
        
        // Define the checked items for Dehum Matras
        $items = [
            1 => 'Kompressor',
            2 => 'Kabel',
            3 => 'NFB',
            4 => 'Motor',
            5 => 'Water Cooler in',
            6 => 'Water Cooler Out',
            7 => 'Temperatur Output Udara',
        ];
        
        // Proses data dari tabel 1 (tanggal 1-11)
        foreach ($resultsTable1 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        // Cari data checker dan approver untuk tanggal ini
                        $detail = $detailChecks->where('tanggal', $j)->first();
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $detail ? $detail->checker->username ?? null : null,
                            'approved_by' => $detail ? $detail->approver->username ?? null : null
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
                    
                    if (isset($row->$tanggalField)) {
                        // Cari data checker dan approver untuk tanggal ini
                        $detail = $detailChecks->where('tanggal', $j)->first();
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $detail ? $detail->checker->username ?? null : null,
                            'approved_by' => $detail ? $detail->approver->username ?? null : null
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
                    
                    if (isset($row->$tanggalField)) {
                        // Cari data checker dan approver untuk tanggal ini
                        $detail = $detailChecks->where('tanggal', $j)->first();
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $detail ? $detail->checker->username ?? null : null,
                            'approved_by' => $detail ? $detail->approver->username ?? null : null
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checker dan approver untuk tanggal yang hanya memiliki checker/approver tanpa item
        foreach ($detailChecks as $detail) {
            if (!$results->where('tanggal', $detail->tanggal)->count()) {
                $results->push([
                    'tanggal' => $detail->tanggal,
                    'checked_by' => $detail->checker ? $detail->checker->username : null,
                    'approved_by' => $detail->approver ? $detail->approver->username : null
                ]);
            }
        }
        
        return view('dehum-matras.show', compact('check', 'results', 'user', 'currentGuard'));
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
            'approved_by_*' => 'sometimes|string',
            'approve_num_*' => 'sometimes|integer|between:1,31',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Model DehumMatrasCheck akan otomatis resolve hashid menjadi model instance
            // karena menggunakan trait Hashidable
            $check = (new DehumMatrasCheck)->resolveRouteBinding($hashid);
            
            // Proses informasi penanggung jawab hanya untuk tanggal yang dipilih
            foreach ($request->all() as $key => $value) {
                // Hanya proses jika key adalah approved_by_ dan value tidak kosong
                if (strpos($key, 'approved_by_') === 0 && !empty($value)) {
                    // Ambil nomor tanggal dari key (misal: approved_by_5 akan mengambil 5)
                    $tanggal = (int)str_replace('approved_by_', '', $key);
                    
                    // Pastikan approve_num untuk tanggal ini juga ada dan sesuai
                    $approveNumKey = 'approve_num_' . $tanggal;
                    if ($request->has($approveNumKey) && $request->$approveNumKey == $tanggal) {
                        // Cari detail yang sudah ada untuk tanggal ini
                        $detail = DehumMatrasDetail::where('tanggal_check_id', $check->id)
                            ->where('tanggal', $tanggal)
                            ->first();
                        
                        if ($detail) {
                            // Update jika detail sudah ada dan belum memiliki approver_id
                            if (empty($detail->approver_id)) {
                                $detail->update([
                                    'approver_id' => $user->id
                                ]);
                            }
                        } else {
                            // Buat baru jika tidak ada detail
                            DehumMatrasDetail::create([
                                'tanggal_check_id' => $check->id,
                                'tanggal' => $tanggal,
                                'checker_id' => null,
                                'approver_id' => $user->id
                            ]);
                        }
                    }
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('dehum-matras.index')
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
        
        // Gunakan trait untuk mendapatkan model berdasarkan hashid
        $dehumMatras = app(DehumMatrasCheck::class)->resolveRouteBinding($hashid);
        $id = $dehumMatras->id; // Simpan ID untuk kompatibilitas dengan kode yang ada
        
        // Ambil data form terkait
        $form = Form::findOrFail(3); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = DehumMatrasResultsTable1::where('check_id', $id)->get();
        $resultsTable2 = DehumMatrasResultsTable2::where('check_id', $id)->get();
        $resultsTable3 = DehumMatrasResultsTable3::where('check_id', $id)->get();
        
        // Ambil data detail dengan join untuk mendapatkan username dari model Checker dan Approver
        $detailChecks = DehumMatrasDetail::with(['checker', 'approver'])
            ->where('tanggal_check_id', $id)
            ->get();
        
        // Siapkan data untuk view dalam format yang sesuai
        $results = collect();
        
        // Buat array untuk menyimpan data checker dan approver berdasarkan tanggal
        $checkerData = [];
        $approverData = [];
        
        // Proses data checker dan approver dulu agar tersedia untuk digunakan kemudian
        foreach ($detailChecks as $detail) {
            // Simpan username dari relasi checker dan approver
            $checkerData[$detail->tanggal] = $detail->checker ? $detail->checker->username : null;
            $approverData[$detail->tanggal] = $detail->approver ? $detail->approver->username : null;
        }
        
        // Define the checked items for Dehum Matras
        $items = [
            1 => 'Kompressor',
            2 => 'Kabel',
            3 => 'NFB',
            4 => 'Motor',
            5 => 'Water Cooler in',
            6 => 'Water Cooler Out',
            7 => 'Temperatur Output Udara',
        ];
        
        // Proses data dari tabel 1 (tanggal 1-11)
        foreach ($resultsTable1 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        // Ambil username dari array yang sudah disiapkan
                        $checkedBy = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approvedBy = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $checkedBy,
                            'approved_by' => $approvedBy
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
                    
                    if (isset($row->$tanggalField)) {
                        // Ambil username dari array yang sudah disiapkan
                        $checkedBy = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approvedBy = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $checkedBy,
                            'approved_by' => $approvedBy
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
                    
                    if (isset($row->$tanggalField)) {
                        // Ambil username dari array yang sudah disiapkan
                        $checkedBy = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approvedBy = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $checkedBy,
                            'approved_by' => $approvedBy
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checker dan approver untuk tanggal yang hanya memiliki checker/approver tanpa item
        foreach ($detailChecks as $detail) {
            if (!$results->where('tanggal', $detail->tanggal)->count()) {
                $results->push([
                    'tanggal' => $detail->tanggal,
                    'checked_by' => $detail->checker ? $detail->checker->username : null,
                    'approved_by' => $detail->approver ? $detail->approver->username : null
                ]);
            }
        }
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('dehum-matras.review_pdf', compact('dehumMatras', 'results', 'form', 'formattedTanggalEfektif', 'items', 'user', 'currentGuard'));
        
        // Return view untuk preview
        return $view;
    }

    public function downloadPdf($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Gunakan trait untuk mendapatkan model berdasarkan hashid
        $dehumMatras = app(DehumMatrasCheck::class)->resolveRouteBinding($hashid);
        $id = $dehumMatras->id; // Simpan ID untuk kompatibilitas dengan kode yang ada
        
        // Ambil data form terkait
        $form = Form::findOrFail(3); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil data hasil dari ketiga tabel
        $resultsTable1 = DehumMatrasResultsTable1::where('check_id', $id)->get();
        $resultsTable2 = DehumMatrasResultsTable2::where('check_id', $id)->get();
        $resultsTable3 = DehumMatrasResultsTable3::where('check_id', $id)->get();
        
        // Ambil data detail dengan join untuk mendapatkan username dari model Checker dan Approver
        $detailChecks = DehumMatrasDetail::with(['checker', 'approver'])
            ->where('tanggal_check_id', $id)
            ->get();
        
        // Siapkan data untuk view dalam format yang sesuai
        $results = collect();
        
        // Buat array untuk menyimpan data checker dan approver berdasarkan tanggal
        $checkerData = [];
        $approverData = [];
        
        // Proses data checker dan approver dulu agar tersedia untuk digunakan kemudian
        foreach ($detailChecks as $detail) {
            // Simpan username dari relasi checker dan approver
            $checkerData[$detail->tanggal] = $detail->checker ? $detail->checker->username : null;
            $approverData[$detail->tanggal] = $detail->approver ? $detail->approver->username : null;
        }
        
        // Define the checked items for Dehum Matras
        $items = [
            1 => 'Kompressor',
            2 => 'Kabel',
            3 => 'NFB',
            4 => 'Motor',
            5 => 'Water Cooler in',
            6 => 'Water Cooler Out',
            7 => 'Temperatur Output Udara',
        ];
        
        // Proses data dari tabel 1 (tanggal 1-11)
        foreach ($resultsTable1 as $row) {
            $itemId = array_search($row->checked_items, $items);
            
            if ($itemId) {
                for ($j = 1; $j <= 11; $j++) {
                    $tanggalField = "tanggal{$j}";
                    
                    if (isset($row->$tanggalField)) {
                        // Ambil username dari array yang sudah disiapkan
                        $checkedBy = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approvedBy = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $checkedBy,
                            'approved_by' => $approvedBy
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
                    
                    if (isset($row->$tanggalField)) {
                        // Ambil username dari array yang sudah disiapkan
                        $checkedBy = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approvedBy = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $checkedBy,
                            'approved_by' => $approvedBy
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
                    
                    if (isset($row->$tanggalField)) {
                        // Ambil username dari array yang sudah disiapkan
                        $checkedBy = isset($checkerData[$j]) ? $checkerData[$j] : null;
                        $approvedBy = isset($approverData[$j]) ? $approverData[$j] : null;
                        
                        $results->push([
                            'tanggal' => $j,
                            'item_id' => $itemId,
                            'result' => $row->$tanggalField,
                            'checked_by' => $checkedBy,
                            'approved_by' => $approvedBy
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan data checker dan approver untuk tanggal yang hanya memiliki checker/approver tanpa item
        foreach ($detailChecks as $detail) {
            if (!$results->where('tanggal', $detail->tanggal)->count()) {
                $results->push([
                    'tanggal' => $detail->tanggal,
                    'checked_by' => $detail->checker ? $detail->checker->username : null,
                    'approved_by' => $detail->approver ? $detail->approver->username : null
                ]);
            }
        }
        
        // Generate filename untuk PDF
        $nomor = $dehumMatras->nomer_dehum_matras ?? 'unknown';
        $shift = $dehumMatras->shift ?? 'unknown';
        
        $carbonBulan = Carbon::parse($dehumMatras->bulan);
        $namaBulan = $carbonBulan->translatedFormat('F_Y'); 

        $filename = "Dehum_matras_nomer_{$nomor}_shift_{$shift}_bulan_{$namaBulan}.pdf";
        
        // Render view sebagai HTML menggunakan compact yang sama dengan reviewPdf
        $html = view('dehum-matras.review_pdf', compact('dehumMatras', 'results', 'form', 'formattedTanggalEfektif', 'items', 'user', 'currentGuard'))->render();
        
        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Atur ukuran dan orientasi halaman
        $dompdf->setPaper('A4', 'portrait'); // Diperbaiki dari 'potrait' ke 'portrait'
        
        // Render PDF (mengubah HTML menjadi PDF)
        $dompdf->render();
        
        // Download file PDF
        return $dompdf->stream($filename, [
            'Attachment' => false, // Set true untuk download, false untuk preview di browser
        ]);
    }
}
