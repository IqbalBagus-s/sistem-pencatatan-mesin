<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\VacumCleanerCheck;
use App\Models\VacumCleanerResultsTable1;
use App\Models\VacumCleanerResultsTable2;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use App\Traits\WithAuthentication;

class VacumCleanerController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        $query = VacumCleanerCheck::with([
            'checkerMinggu2', 
            'checkerMinggu4', 
            'approverMinggu2', 
            'approverMinggu4'
        ]);

        // Filter berdasarkan nama checker atau approver (username, gunakan relasi yang ada saja)
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('checkerMinggu2', function ($qc) use ($search) {
                    $qc->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('checkerMinggu4', function ($qc) use ($search) {
                    $qc->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('approverMinggu2', function ($qa) use ($search) {
                    $qa->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('approverMinggu4', function ($qa) use ($search) {
                    $qa->where('username', 'LIKE', $search);
                });
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
            // Dapatkan semua checker unik berdasarkan username dari relasi
            $checkers = collect();
            if ($check->checkerMinggu2) {
                $checkers->push($check->checkerMinggu2->username);
            }
            if ($check->checkerMinggu4) {
                $checkers->push($check->checkerMinggu4->username);
            }
            
            $check->allCheckers = $checkers->filter()->unique()->values()->toArray();
                
            // Hitung jumlah hari dalam bulan
            $year = substr($check->bulan, 0, 4);
            $month = substr($check->bulan, 5, 2);
            $check->daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            
            // Menggunakan method helper dari model untuk mendapatkan jumlah minggu yang disetujui
            $check->approvedDatesCount = $check->getApprovedWeeksCount();
            
            // ===== LOGIKA STATUS PERSETUJUAN MENGGUNAKAN ID LANGSUNG =====
            // Cek apakah approver_minggu2_id dan approver_minggu4_id ada dan tidak kosong
            $approver_minggu2_filled = !empty($check->approver_minggu2_id);
            $approver_minggu4_filled = !empty($check->approver_minggu4_id);
            
            // Tentukan status berdasarkan kondisi
            if ($approver_minggu2_filled && $approver_minggu4_filled) {
                // Keduanya terisi = Disetujui
                $check->isFullyApproved = true;
                $check->isPartiallyApproved = false;
            } elseif ($approver_minggu2_filled || $approver_minggu4_filled) {
                // Salah satu terisi = Disetujui Sebagian
                $check->isFullyApproved = false;
                $check->isPartiallyApproved = true;
            } else {
                // Keduanya kosong = Belum Disetujui
                $check->isFullyApproved = false;
                $check->isPartiallyApproved = false;
            }
            
            // Alternatif: Anda juga bisa menggunakan method isApproved() dari model
            // $check->isFullyApproved = $check->isApproved();
        }

        return view('vacuum_cleaner.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        return view('vacuum_cleaner.create', compact('user', 'currentGuard'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        $customMessages = [
            'nomer_vacuum_cleaner.required' => 'Silakan pilih nomer vacuum cleaner terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!',
            'checked_by_1.integer' => 'ID Checker Minggu 2 harus berupa angka!',
            'checked_by_2.integer' => 'ID Checker Minggu 4 harus berupa angka!',
        ];
        
        // Validasi input dengan validasi tambahan untuk checker ID
        $validated = $request->validate([
            'nomer_vacuum_cleaner' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
            'checked_by_1' => 'nullable|integer|exists:checkers,id', // Validasi ID checker
            'checked_by_2' => 'nullable|integer|exists:checkers,id', // Validasi ID checker
        ], $customMessages);

        // Debug: Cek data yang diterima dari form
        Log::info('Data dari form vacuum cleaner:', $request->all());
        
        // Tambahkan validasi khusus untuk memastikan checker ID adalah integer
        if ($request->has('checked_by_1') && !empty($request->checked_by_1)) {
            if (!is_numeric($request->checked_by_1)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "ID Checker Minggu 2 tidak valid: {$request->checked_by_1}. Harus berupa angka!");
            }
        }
        
        if ($request->has('checked_by_2') && !empty($request->checked_by_2)) {
            if (!is_numeric($request->checked_by_2)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "ID Checker Minggu 4 tidak valid: {$request->checked_by_2}. Harus berupa angka!");
            }
        }

        // Periksa apakah data sudah ada
        $existingRecord = VacumCleanerCheck::where('nomer_vacum_cleaner', $request->nomer_vacuum_cleaner)
            ->where('bulan', $request->bulan)
            ->first();

        if ($existingRecord) {
            $nomerVacuum = $request->nomer_vacuum_cleaner;
            $bulan = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
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
                'status' => 'belum_disetujui', // Set default status
            ];
            
            // Set checker dan tanggal untuk minggu ke-2
            if ($request->has('check_num_1') && $request->check_num_1 == '1') {
                $data['checker_minggu2_id'] = (int) $request->checked_by_1;
                // Parsing tanggal dengan Carbon
                if (!empty($request->check_date_1)) {
                    try {
                        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $request->check_date_1)) {
                            $data['tanggal_dibuat_minggu2'] = Carbon::createFromFormat('d-m-Y', $request->check_date_1)->format('Y-m-d');
                        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->check_date_1)) {
                            $data['tanggal_dibuat_minggu2'] = $request->check_date_1;
                        } else {
                            $data['tanggal_dibuat_minggu2'] = Carbon::parse($request->check_date_1)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        Log::warning('Gagal parsing tanggal untuk minggu 2: ' . $request->check_date_1 . '. Error: ' . $e->getMessage());
                        $data['tanggal_dibuat_minggu2'] = Carbon::now()->format('Y-m-d');
                    }
                } else {
                    $data['tanggal_dibuat_minggu2'] = Carbon::now()->format('Y-m-d');
                }
            }
            
            // Set checker dan tanggal untuk minggu ke-4
            if ($request->has('check_num_2') && $request->check_num_2 == '2') {
                $data['checker_minggu4_id'] = (int) $request->checked_by_2;
                // Parsing tanggal dengan Carbon
                if (!empty($request->check_date_2)) {
                    try {
                        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $request->check_date_2)) {
                            $data['tanggal_dibuat_minggu4'] = Carbon::createFromFormat('d-m-Y', $request->check_date_2)->format('Y-m-d');
                        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->check_date_2)) {
                            $data['tanggal_dibuat_minggu4'] = $request->check_date_2;
                        } else {
                            $data['tanggal_dibuat_minggu4'] = Carbon::parse($request->check_date_2)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        Log::warning('Gagal parsing tanggal untuk minggu 4: ' . $request->check_date_2 . '. Error: ' . $e->getMessage());
                        $data['tanggal_dibuat_minggu4'] = Carbon::now()->format('Y-m-d');
                    }
                } else {
                    $data['tanggal_dibuat_minggu4'] = Carbon::now()->format('Y-m-d');
                }
            }
            
            // Debug: Log data sebelum insert
            Log::info('Data yang akan disimpan ke database:', $data);
            
            // Buat record VacumCleanerCheck
            $vacuumCleanerCheck = VacumCleanerCheck::create($data);
            
            Log::info('Record vacuum cleaner check dibuat dengan ID: ' . $vacuumCleanerCheck->id);
            
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
            
            $itemsProcessed = [];
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Data untuk tabel minggu 2
                $resultData1 = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                    'minggu2' => $request->check_1[$itemId] ?? '-',
                    'keterangan_minggu2' => $request->keterangan_1[$itemId] ?? null,
                ];
                
                // Data untuk tabel minggu 4
                $resultData2 = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                    'minggu4' => $request->check_2[$itemId] ?? '-',
                    'keterangan_minggu4' => $request->keterangan_2[$itemId] ?? null,
                ];
                
                // Buat record hasil pemeriksaan
                $table1Result = VacumCleanerResultsTable1::create($resultData1);
                $table2Result = VacumCleanerResultsTable2::create($resultData2);
                
                Log::info("Item #{$itemId} ({$itemName}) berhasil disimpan");
                
                // Simpan detail untuk activity log
                $itemsProcessed[] = [
                    'item' => $itemName,
                    'minggu2' => $resultData1['minggu2'],
                    'minggu4' => $resultData2['minggu4'],
                    'keterangan_minggu2' => $resultData1['keterangan_minggu2'],
                    'keterangan_minggu4' => $resultData2['keterangan_minggu4'],
                ];
            }

            // LOG AKTIVITAS
            $bulanFormatted = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            $weeklyData = [];
            
            // Minggu 2 - Ambil username dari ID untuk log
            if (!empty($data['checker_minggu2_id'])) {
                $checkerUser = \App\Models\Checker::find($data['checker_minggu2_id']);
                $checkerMinggu2 = $checkerUser ? $checkerUser->username : 'User tidak ditemukan';
                
                $weeklyData['minggu_2'] = [
                    'checker' => $checkerMinggu2,
                    'checker_id' => $data['checker_minggu2_id'],
                    'tanggal' => !empty($data['tanggal_dibuat_minggu2']) ? 
                        Carbon::parse($data['tanggal_dibuat_minggu2'])->locale('id')->isoFormat('D MMMM YYYY') : null
                ];
            }
            
            // Minggu 4 - Ambil username dari ID untuk log
            if (!empty($data['checker_minggu4_id'])) {
                $checkerUser = \App\Models\Checker::find($data['checker_minggu4_id']);
                $checkerMinggu4 = $checkerUser ? $checkerUser->username : 'User tidak ditemukan';
                
                $weeklyData['minggu_4'] = [
                    'checker' => $checkerMinggu4,
                    'checker_id' => $data['checker_minggu4_id'],
                    'tanggal' => !empty($data['tanggal_dibuat_minggu4']) ? 
                        Carbon::parse($data['tanggal_dibuat_minggu4'])->locale('id')->isoFormat('D MMMM YYYY') : null
                ];
            }
            
            // Buat activity log
            $checkerString = [];
            foreach ($weeklyData as $minggu => $data_weekly) {
                if ($data_weekly['checker']) {
                    $mingguLabel = ucfirst(str_replace('_', ' ', $minggu));
                    $checkerInfo = $mingguLabel . ': ' . $data_weekly['checker'];
                    if ($data_weekly['tanggal']) {
                        $checkerInfo .= ' (' . $data_weekly['tanggal'] . ')';
                    }
                    $checkerString[] = $checkerInfo;
                }
            }
            $checkerDescription = !empty($checkerString) ? implode(', ', $checkerString) : 'Tidak ada checker yang ditetapkan';
            
            Activity::logActivity(
                'checker',
                $user->id,
                $user->username,
                'created',
                'Checker ' . $user->username . ' membuat pemeriksaan Vacuum Cleaner Nomor ' . $request->nomer_vacuum_cleaner . ' untuk bulan ' . $bulanFormatted,
                'vacuum_cleaner_check',
                $vacuumCleanerCheck->id,
                [
                    'nomer_vacuum_cleaner' => $request->nomer_vacuum_cleaner,
                    'bulan' => $request->bulan,
                    'bulan_formatted' => $bulanFormatted,
                    'weekly_data' => $weeklyData,
                    'total_items' => count($items),
                    'items_processed' => $itemsProcessed,
                    'total_weeks_filled' => count($weeklyData),
                    'status' => $vacuumCleanerCheck->status,
                    'note' => 'Vacuum cleaner hanya memiliki pemeriksaan pada minggu 2 dan minggu 4'
                ]
            );
            
            // Commit transaksi
            DB::commit();
            
            Log::info('Transaksi vacuum cleaner berhasil disimpan dengan ID: ' . $vacuumCleanerCheck->id);
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('success', 'Data berhasil disimpan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saat menyimpan data vacuum cleaner: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
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
        try {
            // Gunakan trait Hashidable untuk resolve hashid ke model instance
            $check = (new VacumCleanerCheck)->resolveRouteBinding($hashid);
            
            // Retrieve the related items from both tables
            $resultsTable1 = VacumCleanerResultsTable1::where('check_id', $check->id)->get();
            $resultsTable2 = VacumCleanerResultsTable2::where('check_id', $check->id)->get();
            
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
            
            // Ambil username checker dan tanggal untuk minggu 2 dan 4
            $check->checker_minggu2 = $check->checkerMinggu2 ? $check->checkerMinggu2->username : null;
            $check->checker_minggu4 = $check->checkerMinggu4 ? $check->checkerMinggu4->username : null;
            $check->approver_minggu2 = $check->approverMinggu2 ? $check->approverMinggu2->username : null;
            $check->approver_minggu4 = $check->approverMinggu4 ? $check->approverMinggu4->username : null;
            $tanggal_minggu2 = $check->tanggal_dibuat_minggu2;
            $tanggal_minggu4 = $check->tanggal_dibuat_minggu4;
            
            // Get all needed data ready for the view
            $data = [
                'check' => $check,
                'results' => $results,
                'groupedResults' => $groupedResults,
                'itemsMap' => $itemsMap,
                'check_num_1' => $check_num_1,
                'check_num_2' => $check_num_2,
                'tanggal_minggu2' => $tanggal_minggu2,
                'tanggal_minggu4' => $tanggal_minggu4,
                'user' => $user,
                'currentGuard' => $currentGuard
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

    public function update(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Validasi input
        $validated = $request->validate([
            'nomer_vacuum_cleaner' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Gunakan trait Hashidable untuk resolve hashid ke model instance
        $vacuumCheck = (new VacumCleanerCheck)->resolveRouteBinding($hashid);

        // Cek apakah ada perubahan pada data utama (nomer_vacuum_cleaner, bulan)
        if ($vacuumCheck->nomer_vacum_cleaner != $request->nomer_vacuum_cleaner || 
            $vacuumCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = VacumCleanerCheck::where('nomer_vacum_cleaner', $request->nomer_vacuum_cleaner)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $vacuumCheck->id) // Kecualikan record saat ini
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
                $existingTable1Data = VacumCleanerResultsTable1::where('check_id', $vacuumCheck->id)->get()->keyBy('checked_items');
                
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
                            'check_id' => $vacuumCheck->id,
                            'checked_items' => $itemName,
                            'minggu2' => $result,
                            'keterangan_minggu2' => $keterangan
                        ]);
                    }
                }
            }
            
            // Update data untuk minggu 4 (table 2)
            if ($request->has('check_2')) {
                $existingTable2Data = VacumCleanerResultsTable2::where('check_id', $vacuumCheck->id)->get()->keyBy('checked_items');
                
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
                            'check_id' => $vacuumCheck->id,
                            'checked_items' => $itemName,
                            'minggu4' => $result,
                            'keterangan_minggu4' => $keterangan
                        ]);
                    }
                }
            }
            
            // Update data checker untuk minggu ke-2
            if ($request->has('check_num_1') && !empty($request->check_num_1)) {
                $tanggal = null;
                // Parsing tanggal dengan Carbon
                if (!empty($request->check_date_1)) {
                    try {
                        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $request->check_date_1)) {
                            $tanggal = Carbon::createFromFormat('d-m-Y', $request->check_date_1)->format('Y-m-d');
                        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->check_date_1)) {
                            $tanggal = $request->check_date_1;
                        } else {
                            $tanggal = Carbon::parse($request->check_date_1)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        Log::warning('Gagal parsing tanggal untuk minggu 2: ' . $request->check_date_1 . '. Error: ' . $e->getMessage());
                        $tanggal = Carbon::now()->format('Y-m-d');
                    }
                }
                // Pastikan checker_minggu2_id adalah ID
                $checkerId = null;
                if (!empty($request->checked_by_1)) {
                    if (is_numeric($request->checked_by_1)) {
                        $checkerId = (int) $request->checked_by_1;
                    } else {
                        $checker = \App\Models\Checker::where('username', $request->checked_by_1)->first();
                        if ($checker) {
                            $checkerId = $checker->id;
                        } else {
                            throw new \Exception("Checker dengan username '{$request->checked_by_1}' tidak ditemukan");
                        }
                    }
                }
                $vacuumCheck->update([
                    'checker_minggu2_id' => $checkerId,
                    'tanggal_dibuat_minggu2' => $tanggal
                ]);
            }
            
            // Update data checker untuk minggu ke-4
            if ($request->has('check_num_2') && !empty($request->check_num_2)) {
                $tanggal = null;
                // Parsing tanggal dengan Carbon
                if (!empty($request->check_date_2)) {
                    try {
                        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $request->check_date_2)) {
                            $tanggal = Carbon::createFromFormat('d-m-Y', $request->check_date_2)->format('Y-m-d');
                        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $request->check_date_2)) {
                            $tanggal = $request->check_date_2;
                        } else {
                            $tanggal = Carbon::parse($request->check_date_2)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        Log::warning('Gagal parsing tanggal untuk minggu 4: ' . $request->check_date_2 . '. Error: ' . $e->getMessage());
                        $tanggal = Carbon::now()->format('Y-m-d');
                    }
                }
                // Pastikan checker_minggu4_id adalah ID
                $checkerId = null;
                if (!empty($request->checked_by_2)) {
                    if (is_numeric($request->checked_by_2)) {
                        $checkerId = (int) $request->checked_by_2;
                    } else {
                        $checker = \App\Models\Checker::where('username', $request->checked_by_2)->first();
                        if ($checker) {
                            $checkerId = $checker->id;
                        } else {
                            throw new \Exception("Checker dengan username '{$request->checked_by_2}' tidak ditemukan");
                        }
                    }
                }
                $vacuumCheck->update([
                    'checker_minggu4_id' => $checkerId,
                    'tanggal_dibuat_minggu4' => $tanggal
                ]);
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('success', 'Data berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            // Log error untuk debugging
            Log::error('Error saat memperbarui data vacuum cleaner: ' . $e->getMessage());
            Log::error('Request data: ' . json_encode($request->all()));
            Log::error($e->getTraceAsString());
            
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
        try {
            // Gunakan trait Hashidable untuk resolve hashid ke model instance
            $check = (new VacumCleanerCheck)->resolveRouteBinding($hashid);
            
            // Ambil data hasil dari kedua tabel
            $resultsTable1 = VacumCleanerResultsTable1::where('check_id', $check->id)->get();
            $resultsTable2 = VacumCleanerResultsTable2::where('check_id', $check->id)->get();
            
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
            
            // Ambil username checker dan tanggal untuk minggu 2 dan 4
            $check->checker_minggu2 = $check->checkerMinggu2 ? $check->checkerMinggu2->username : null;
            $check->checker_minggu4 = $check->checkerMinggu4 ? $check->checkerMinggu4->username : null;
            $check->approver_minggu2 = $check->approverMinggu2 ? $check->approverMinggu2->username : null;
            $check->approver_minggu4 = $check->approverMinggu4 ? $check->approverMinggu4->username : null;
            $tanggal_minggu2 = $check->tanggal_dibuat_minggu2;
            $tanggal_minggu4 = $check->tanggal_dibuat_minggu4;
            
            // Siapkan semua data yang dibutuhkan untuk view
            $data = [
                'check' => $check,
                'results' => $results,
                'groupedResults' => $groupedResults,
                'itemsMap' => $itemsMap,
                'check_num_1' => $check_num_1,
                'check_num_2' => $check_num_2,
                'tanggal_minggu2' => $tanggal_minggu2,
                'tanggal_minggu4' => $tanggal_minggu4,
                'user' => $user,
                'currentGuard' => $currentGuard
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

    public function approve(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser(['approver']);
        if (!is_object($user)) return $user;
        if (!$this->isAuthenticatedAs('approver')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
        }
        
        // Validasi input
        $request->validate([
            'approve_minggu2' => 'sometimes|in:2',
            'approve_minggu4' => 'sometimes|in:4',
            'approved_by_minggu2' => 'sometimes|string',
            'approved_by_minggu4' => 'sometimes|string',
        ]);

        try {
            // Gunakan trait Hashidable untuk resolve hashid ke model instance
            $check = (new VacumCleanerCheck)->resolveRouteBinding($hashid);
            $updated = false;
            $messages = [];
            
            // Cek kondisi untuk minggu 2
            $shouldApproveMinggu2 = $request->filled('approve_minggu2') && 
                                $request->approve_minggu2 == '2' && 
                                $request->filled('approved_by_minggu2');
            
            // Cek kondisi untuk minggu 4                       
            $shouldApproveMinggu4 = $request->filled('approve_minggu4') && 
                                $request->approve_minggu4 == '4' && 
                                $request->filled('approved_by_minggu4');
            
            // Update approver minggu ke-2
            if ($shouldApproveMinggu2) {
                if (is_null($check->approver_minggu2_id)) {
                    $check->approver_minggu2_id = $user->id;
                    $updated = true;
                    $messages[] = 'Minggu 2';
                } else {
                    $messages[] = 'Minggu 2 (sudah disetujui sebelumnya)';
                }
            }
            
            // Update approver minggu ke-4
            if ($shouldApproveMinggu4) {
                if (is_null($check->approver_minggu4_id)) {
                    $check->approver_minggu4_id = $user->id;
                    $updated = true;
                    $messages[] = 'Minggu 4';
                } else {
                    $messages[] = 'Minggu 4 (sudah disetujui sebelumnya)';
                }
            }
            
            // Simpan perubahan jika ada update
            if ($updated) {
                $check->save();
                
                $successMessage = 'Data penanggung jawab berhasil disimpan untuk: ' . implode(' dan ', $messages);
                return redirect()->route('vacuum-cleaner.index')
                    ->with('success', $successMessage);
            } else {
                if (!empty($messages)) {
                    $warningMessage = 'Status untuk: ' . implode(' dan ', $messages);
                    return redirect()->back()
                        ->with('warning', $warningMessage);
                } else {
                    return redirect()->back()
                        ->with('info', 'Tidak ada perubahan data penanggung jawab.');
                }
            }
                    
        } catch (\Exception $e) {
            Log::error('Error in approve function: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reviewPdf($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        try {
            // Gunakan trait Hashidable untuk resolve hashid ke model instance
            $check = (new VacumCleanerCheck)->resolveRouteBinding($hashid);
            
            // Ambil data form terkait (sesuaikan nomor form untuk vacuum cleaner)
            $form = Form::findOrFail(1); 
            
            // Format tanggal efektif
            $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
            
            // Ambil data hasil dari kedua tabel
            $resultsTable1 = VacumCleanerResultsTable1::where('check_id', $check->id)->get();
            $resultsTable2 = VacumCleanerResultsTable2::where('check_id', $check->id)->get();
            
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
                        'checked_by' => $check->getCheckerName(2), // Menggunakan method helper
                        'approved_by' => $check->getApproverName(2) // Menggunakan method helper
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
                        'checked_by' => $check->getCheckerName(4), // Menggunakan method helper
                        'approved_by' => $check->getApproverName(4) // Menggunakan method helper
                    ]);
                }
            }
            
            // Kelompokkan hasil berdasarkan minggu untuk akses yang lebih mudah di view
            $groupedResults = $results->groupBy('minggu');
            
            // Periksa minggu mana yang memiliki checker (menggunakan method helper)
            $check_num_1 = $check->isWeekChecked(2) ? 1 : null;
            $check_num_2 = $check->isWeekChecked(4) ? 2 : null;
            
            // Ambil username checker dan approver menggunakan relasi
            $checker_minggu2 = $check->getCheckerName(2);
            $checker_minggu4 = $check->getCheckerName(4);
            $approver_minggu2 = $check->getApproverName(2);
            $approver_minggu4 = $check->getApproverName(4);
            
            // Ambil tanggal untuk minggu 2 dan 4
            $tanggal_minggu2 = $check->tanggal_dibuat_minggu2;
            $tanggal_minggu4 = $check->tanggal_dibuat_minggu4;
            
            // Siapkan semua data yang dibutuhkan untuk view
            $data = [
                'check' => $check,
                'results' => $results,
                'groupedResults' => $groupedResults,
                'itemsMap' => $itemsMap,
                'check_num_1' => $check_num_1,
                'check_num_2' => $check_num_2,
                'checker_minggu2' => $checker_minggu2,
                'checker_minggu4' => $checker_minggu4,
                'approver_minggu2' => $approver_minggu2,
                'approver_minggu4' => $approver_minggu4,
                'tanggal_minggu2' => $tanggal_minggu2,
                'tanggal_minggu4' => $tanggal_minggu4,
                'form' => $form,
                'formattedTanggalEfektif' => $formattedTanggalEfektif,
                'user' => $user,
                'currentGuard' => $currentGuard
            ];
            
            return view('vacuum_cleaner.review_pdf', $data);
            
        } catch (\Exception $e) {
            // Catat detail error untuk debugging
            Log::error('Error saat menampilkan review PDF vacuum cleaner: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('error', 'Terjadi kesalahan saat menampilkan review PDF: ' . $e->getMessage());
        }
    }

    public function downloadPdf($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        try {
            // Gunakan trait Hashidable untuk resolve hashid ke model instance
            $check = (new VacumCleanerCheck)->resolveRouteBinding($hashid);
            
            // Ambil data form terkait (sesuaikan nomor form untuk vacuum cleaner)
            $form = Form::findOrFail(1); 
            
            // Format tanggal efektif
            $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
            
            // Ambil data hasil dari kedua tabel
            $resultsTable1 = VacumCleanerResultsTable1::where('check_id', $check->id)->get();
            $resultsTable2 = VacumCleanerResultsTable2::where('check_id', $check->id)->get();
            
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
                        'checked_by' => $check->getCheckerName(2),
                        'approved_by' => $check->getApproverName(2)
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
                        'checked_by' => $check->getCheckerName(4),
                        'approved_by' => $check->getApproverName(4)
                    ]);
                }
            }
            
            // Kelompokkan hasil berdasarkan minggu untuk akses yang lebih mudah di view
            $groupedResults = $results->groupBy('minggu');
            
            // Periksa minggu mana yang memiliki checker
            $check_num_1 = $check->isWeekChecked(2) ? 1 : null;
            $check_num_2 = $check->isWeekChecked(4) ? 2 : null;
            
            // Ambil username checker dan approver menggunakan method helper
            $checker_minggu2 = $check->getCheckerName(2);
            $checker_minggu4 = $check->getCheckerName(4);
            $approver_minggu2 = $check->getApproverName(2);
            $approver_minggu4 = $check->getApproverName(4);
            
            // Ambil tanggal untuk minggu 2 dan 4
            $tanggal_minggu2 = $check->tanggal_dibuat_minggu2;
            $tanggal_minggu4 = $check->tanggal_dibuat_minggu4;
            
            // Siapkan data untuk template PDF yang kompatibel dengan format lama
            // Siapkan semua field check dan keterangan untuk empat minggu
            for ($j = 1; $j <= 4; $j++) {
                ${'check_' . $j} = [];
                ${'keterangan_' . $j} = [];
                
                foreach ($itemsMap as $i => $item) {
                    if ($j == 1 || $j == 3) {
                        // Minggu 1 dan 3 kosong (tidak ada data)
                        ${'check_' . $j}[$i] = '';
                        ${'keterangan_' . $j}[$i] = '';
                    } elseif ($j == 2) {
                        // Minggu 2 - ambil dari groupedResults
                        $minggu2Data = $groupedResults->get(2);
                        $itemData = $minggu2Data ? $minggu2Data->firstWhere('item_id', $i) : null;
                        ${'check_' . $j}[$i] = $itemData ? $itemData['result'] : '';
                        ${'keterangan_' . $j}[$i] = $itemData ? $itemData['keterangan'] : '';
                    } elseif ($j == 4) {
                        // Minggu 4 - ambil dari groupedResults
                        $minggu4Data = $groupedResults->get(4);
                        $itemData = $minggu4Data ? $minggu4Data->firstWhere('item_id', $i) : null;
                        ${'check_' . $j}[$i] = $itemData ? $itemData['result'] : '';
                        ${'keterangan_' . $j}[$i] = $itemData ? $itemData['keterangan'] : '';
                    }
                }
                
                // Tambahkan array ke check object untuk kompatibilitas dengan view lama
                $check->{'check_' . $j} = ${'check_' . $j};
                $check->{'keterangan_' . $j} = ${'keterangan_' . $j};
            }
            
            // Format tanggal dari bulan field untuk mendapatkan bulan dan tahun
            $tanggal = new \DateTime($check->bulan);
            $bulan = $tanggal->format('F');
            $tahun = $tanggal->format('Y');
            
            // Ubah nama bulan ke Bahasa Indonesia
            $bulanIndonesia = [
                'January' => 'Januari',
                'February' => 'Februari',
                'March' => 'Maret',
                'April' => 'April',
                'May' => 'Mei',
                'June' => 'Juni',
                'July' => 'Juli',
                'August' => 'Agustus',
                'September' => 'September',
                'October' => 'Oktober',
                'November' => 'November',
                'December' => 'Desember'
            ];
            
            $bulanFormatted = $bulanIndonesia[$bulan] ?? $bulan;
            
            // Generate nama file PDF
            $filename = 'VacuumCleaner_nomer_' . $check->nomer_vacum_cleaner . '_bulan_' . $bulanFormatted . '_' . $tahun . '.pdf';
            
            // Render view sebagai HTML dengan data yang lengkap
            $html = view('vacuum_cleaner.review_pdf', [
                'check' => $check,
                'vacuumCheck' => $check, // Untuk kompatibilitas dengan template lama
                'results' => $results,
                'groupedResults' => $groupedResults,
                'itemsMap' => $itemsMap,
                'items' => $itemsMap, // Untuk kompatibilitas dengan template lama
                'check_num_1' => $check_num_1,
                'check_num_2' => $check_num_2,
                'checker_minggu2' => $checker_minggu2,
                'checker_minggu4' => $checker_minggu4,
                'approver_minggu2' => $approver_minggu2,
                'approver_minggu4' => $approver_minggu4,
                'tanggal_minggu2' => $tanggal_minggu2,
                'tanggal_minggu4' => $tanggal_minggu4,
                'form' => $form,
                'formattedTanggalEfektif' => $formattedTanggalEfektif,
                'user' => $user,
                'currentGuard' => $currentGuard
            ])->render();
            
            // Inisialisasi Dompdf
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            
            // Atur ukuran dan orientasi halaman
            $dompdf->setPaper('A4', 'portrait');
            
            // Render PDF (mengubah HTML menjadi PDF)
            $dompdf->render();
            
            // Download file PDF
            return $dompdf->stream($filename, [
                'Attachment' => false, // Set true untuk download otomatis
            ]);
            
        } catch (\Exception $e) {
            // Catat detail error untuk debugging
            Log::error('Error saat download PDF vacuum cleaner: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('vacuum-cleaner.index')
                ->with('error', 'Terjadi kesalahan saat download PDF: ' . $e->getMessage());
        }
    }
}
