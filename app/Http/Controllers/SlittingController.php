<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlittingCheck;
use App\Models\SlittingResult;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF
use App\Traits\WithAuthentication;


class SlittingController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        $query = SlittingCheck::query();

        // Eager load relasi untuk menghindari N+1 query problem
        $query->with([
            'checkerMinggu1', 'checkerMinggu2', 'checkerMinggu3', 'checkerMinggu4',
            'approverMinggu1', 'approverMinggu2', 'approverMinggu3', 'approverMinggu4'
        ]);

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('checkerMinggu1', function ($subQ) use ($search) {
                    $subQ->where('username', 'LIKE', $search);
                })
                ->orWhereHas('checkerMinggu2', function ($subQ) use ($search) {
                    $subQ->where('username', 'LIKE', $search);
                })
                ->orWhereHas('checkerMinggu3', function ($subQ) use ($search) {
                    $subQ->where('username', 'LIKE', $search);
                })
                ->orWhereHas('checkerMinggu4', function ($subQ) use ($search) {
                    $subQ->where('username', 'LIKE', $search);
                });
            });
        }

        // Filter berdasarkan nama approver jika ada (opsional tambahan)
        if ($request->filled('search_approver')) {
            $searchApprover = '%' . $request->search_approver . '%';
            $query->where(function ($q) use ($searchApprover) {
                $q->whereHas('approverMinggu1', function ($subQ) use ($searchApprover) {
                    $subQ->where('username', 'LIKE', $searchApprover);
                })
                ->orWhereHas('approverMinggu2', function ($subQ) use ($searchApprover) {
                    $subQ->where('username', 'LIKE', $searchApprover);
                })
                ->orWhereHas('approverMinggu3', function ($subQ) use ($searchApprover) {
                    $subQ->where('username', 'LIKE', $searchApprover);
                })
                ->orWhereHas('approverMinggu4', function ($subQ) use ($searchApprover) {
                    $subQ->where('username', 'LIKE', $searchApprover);
                });
            });
        }

        // Filter berdasarkan nomor slitting
        if ($request->filled('search_slitting')) {
            $query->where('nomer_slitting', $request->search_slitting); // Menggunakan filter exact match
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            try {
                $bulan = date('m', strtotime($request->bulan));
                $tahun = date('Y', strtotime($request->bulan));
                $query->where('bulan', $tahun . '-' . $bulan); // Sesuaikan dengan format penyimpanan di DB
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Format bulan tidak valid.');
            }
        }

        // Filter berdasarkan status (opsional tambahan)
        if ($request->filled('status')) {
            if ($request->status === 'disetujui') {
                $query->disetujui();
            } elseif ($request->status === 'belum_disetujui') {
                $query->belumDisetujui();
            }
        }

        $query->orderBy('created_at', 'desc');

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('slitting.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        return view('slitting.create', compact('user', 'currentGuard'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        $customMessages = [
            'nomer_slitting.required' => 'Silakan pilih nomer slitting terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!'
        ];
        
        // Validate the request
        $validatedData = $request->validate([
            'nomer_slitting' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
            
            // Updated validation for checker fields - now using checker IDs
            'checker_minggu1_id' => 'nullable|exists:checkers,id',
            'checker_minggu2_id' => 'nullable|exists:checkers,id',
            'checker_minggu3_id' => 'nullable|exists:checkers,id',
            'checker_minggu4_id' => 'nullable|exists:checkers,id',
            
            // Validation for checked items and checks
            'checked_items' => 'required|array',
            'check_1' => 'required|array',
            'keterangan_1' => 'nullable|array',
            'check_2' => 'nullable|array',
            'keterangan_2' => 'nullable|array',
            'check_3' => 'nullable|array',
            'keterangan_3' => 'nullable|array',
            'check_4' => 'nullable|array',
            'keterangan_4' => 'nullable|array',
        ], $customMessages);
    
        // Check for existing record with the same nomer_slitting and bulan
        $existingRecord = SlittingCheck::where('nomer_slitting', $request->input('nomer_slitting'))
            ->where('bulan', $request->input('bulan'))
            ->first();
    
        if ($existingRecord) {
            // Ambil nilai yang duplikat
            $nomerSlitting = $request->input('nomer_slitting');
            $bulan = Carbon::parse($request->input('bulan') . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Buat pesan error dengan informasi spesifik
            $pesanError = "Data sudah ada untuk Slitting nomor {$nomerSlitting} pada bulan {$bulan}, silahkan buat ulang";
            
            // Redirect dengan pesan error yang detail
            return redirect()->back()->with('error', $pesanError)
                            ->withInput();
        }
    
        try {
            // Start a database transaction
            DB::beginTransaction();
    
            // Create SlittingCheck record with updated field names
            $slittingCheck = SlittingCheck::create([
                'nomer_slitting' => $request->input('nomer_slitting'),
                'bulan' => $request->input('bulan'),
                
                // Store checker IDs instead of usernames
                'checker_minggu1_id' => $request->input('checker_minggu1_id'),
                'checker_minggu2_id' => $request->input('checker_minggu2_id'),
                'checker_minggu3_id' => $request->input('checker_minggu3_id'),
                'checker_minggu4_id' => $request->input('checker_minggu4_id'),
            ]);
    
            // Log untuk debugging
            Log::info('Checked Items:', $request->input('checked_items'));
            Log::info('Check 1:', $request->input('check_1'));
            
            // SOLUSI MASALAH: Mendefinisikan items sama seperti di view
            $items = [
                1 => 'Conveyor',
                2 => 'Motor Conveyor',
                3 => 'Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Inverter',
                6 => 'Vibrator',
                7 => 'Motor Vibrator',
                8 => 'Motor Blower',
                9 => 'Selang angin',
                10 => 'Flow Control',
                11 => 'Sensor',
                12 => 'Limit Switch',
                13 => 'Pisau Cutting',
                14 => 'Motor Cutting',
                15 => 'Elemen',
                16 => 'Regulator',
                17 => 'Air Filter',
            ];
            
            // Array untuk menyimpan detail items yang diproses (untuk activity log)
            $itemsProcessed = [];
            
            // Proses setiap item sesuai dengan key di $items, bukan indeks dari array checked_items
            foreach ($items as $key => $item) {
                // Mengakses nilai dari form menggunakan key yang sama dengan key di array $items
                $minggu1 = isset($request->input('check_1')[$key]) ? $request->input('check_1')[$key] : null;
                $keterangan1 = isset($request->input('keterangan_1')[$key]) ? $request->input('keterangan_1')[$key] : null;
                
                $minggu2 = isset($request->input('check_2')[$key]) ? $request->input('check_2')[$key] : null;
                $keterangan2 = isset($request->input('keterangan_2')[$key]) ? $request->input('keterangan_2')[$key] : null;
                
                $minggu3 = isset($request->input('check_3')[$key]) ? $request->input('check_3')[$key] : null;
                $keterangan3 = isset($request->input('keterangan_3')[$key]) ? $request->input('keterangan_3')[$key] : null;
                
                $minggu4 = isset($request->input('check_4')[$key]) ? $request->input('check_4')[$key] : null;
                $keterangan4 = isset($request->input('keterangan_4')[$key]) ? $request->input('keterangan_4')[$key] : null;
                
                // Log untuk debugging
                Log::info("Menyimpan item {$key}: {$item} dengan nilai minggu1: {$minggu1}");
                
                $resultData = [
                    'check_id' => $slittingCheck->id,
                    'checked_items' => $item, // Gunakan nilai item dari array $items
                    
                    // Week 1 data
                    'minggu1' => $minggu1, 
                    'keterangan_minggu1' => $keterangan1,
                    
                    // Week 2 data
                    'minggu2' => $minggu2,
                    'keterangan_minggu2' => $keterangan2,
                    
                    // Week 3 data
                    'minggu3' => $minggu3,
                    'keterangan_minggu3' => $keterangan3,
                    
                    // Week 4 data
                    'minggu4' => $minggu4,
                    'keterangan_minggu4' => $keterangan4,
                ];
                
                SlittingResult::create($resultData);
                
                // Simpan detail untuk activity log
                $itemsProcessed[] = [
                    'item' => $item,
                    'minggu1' => $minggu1,
                    'minggu2' => $minggu2,
                    'minggu3' => $minggu3,
                    'minggu4' => $minggu4,
                    'keterangan_minggu1' => $keterangan1,
                    'keterangan_minggu2' => $keterangan2,
                    'keterangan_minggu3' => $keterangan3,
                    'keterangan_minggu4' => $keterangan4,
                ];
            }
    
            // LOG AKTIVITAS - Updated to work with checker IDs
            $bulanFormatted = Carbon::parse($request->input('bulan') . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Kumpulkan checker dan tanggal untuk setiap minggu dengan mengambil data checker dari relasi
            $weeklyData = [];
            for ($i = 1; $i <= 4; $i++) {
                $checkerId = $request->input("checker_minggu{$i}_id");
                $checkerName = null;
                
                if ($checkerId) {
                    // Get checker name from ID using the relation
                    $checker = $slittingCheck->{"checkerMinggu{$i}"};
                    $checkerName = $checker ? $checker->username : null;
                }
                
                if ($checkerId && $checkerName) {
                    $weeklyData["minggu_{$i}"] = [
                        'checker_id' => $checkerId,
                        'checker' => $checkerName,
                        'tanggal' => now()->locale('id')->isoFormat('D MMMM YYYY')
                    ];
                }
            }
            
            // Buat string deskripsi untuk checker dan tanggal
            $checkerString = [];
            foreach ($weeklyData as $minggu => $data) {
                if ($data['checker']) {
                    $mingguLabel = ucfirst(str_replace('_', ' ', $minggu));
                    $checkerInfo = $mingguLabel . ': ' . $data['checker'];
                    if ($data['tanggal']) {
                        $checkerInfo .= ' (' . $data['tanggal'] . ')';
                    }
                    $checkerString[] = $checkerInfo;
                }
            }
            $checkerDescription = !empty($checkerString) ? implode(', ', $checkerString) : 'Tidak ada checker yang ditetapkan';
            
            Activity::logActivity(
                'checker',                                              // user_type
                $user->id,                                       // user_id
                $user->username,                                 // user_name
                'created',                                              // action
                'Checker ' . $user->username . ' membuat pemeriksaan Slitting Nomor ' . $request->input('nomer_slitting') . ' untuk bulan ' . $bulanFormatted,  // description
                'slitting_check',                                       // target_type
                $slittingCheck->id,                                     // target_id
                [
                    'nomer_slitting' => $request->input('nomer_slitting'),
                    'bulan' => $request->input('bulan'),
                    'bulan_formatted' => $bulanFormatted,
                    'weekly_data' => $weeklyData,
                    'total_items' => count($items),
                    'items_processed' => $itemsProcessed,
                    'total_weeks_filled' => count($weeklyData),
                    'status' => $slittingCheck->status ?? 'belum_disetujui'
                ]                                                       // details (JSON)
            );
    
            // Commit the transaction
            DB::commit();
    
            // Log untuk debugging
            Log::info('Transaksi slitting berhasil disimpan dengan ID: ' . $slittingCheck->id);
    
            // Redirect with success message
            return redirect()->route('slitting.index')->with('success', 'Data pemeriksaan Slitting berhasil disimpan.');
            
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
    
            // Log the error for debugging
            Log::error('Slitting store error: ' . $e->getMessage());
            Log::error('Error detail: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
    
            // Redirect back with error message
            return redirect()->back()->with('error', 'Gagal menyimpan data pemeriksaan Slitting: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function edit($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Ambil data utama slitting check dengan relasi checker dan approver
        $check = SlittingCheck::with([
            'checkerMinggu1', 
            'checkerMinggu2', 
            'checkerMinggu3', 
            'checkerMinggu4',
            'approverMinggu1', 
            'approverMinggu2', 
            'approverMinggu3', 
            'approverMinggu4'
        ])->findOrFail($id);
        
        // Ambil data hasil pemeriksaan
        $results = SlittingResult::where('check_id', $id)->get();
        
        // Definisikan items sama seperti di fungsi store
        $items = [
            1 => 'Conveyor',
            2 => 'Motor Conveyor',
            3 => 'Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Inverter',
            6 => 'Vibrator',
            7 => 'Motor Vibrator',
            8 => 'Motor Blower',
            9 => 'Selang angin',
            10 => 'Flow Control',
            11 => 'Sensor',
            12 => 'Limit Switch',
            13 => 'Pisau Cutting',
            14 => 'Motor Cutting',
            15 => 'Elemen',
            16 => 'Regulator',
            17 => 'Air Filter',
        ];
        
        // Format data untuk view
        $formattedResults = [];
        
        // PERBAIKAN 1: Konsistensi nama field checker
        // Pastikan menggunakan format yang sama dengan database dan view
        $checkerData = [
            'checked_by_1' => $check->checkerMinggu1 ? $check->checkerMinggu1->username : '',
            'check_num_1' => !empty($check->checker_minggu1_id) ? 1 : '', // Ubah dari boolean ke number/string
            'checked_by_2' => $check->checkerMinggu2 ? $check->checkerMinggu2->username : '',
            'check_num_2' => !empty($check->checker_minggu2_id) ? 2 : '', // Ubah dari boolean ke number/string
            'checked_by_3' => $check->checkerMinggu3 ? $check->checkerMinggu3->username : '',
            'check_num_3' => !empty($check->checker_minggu3_id) ? 3 : '', // Ubah dari boolean ke number/string
            'checked_by_4' => $check->checkerMinggu4 ? $check->checkerMinggu4->username : '',
            'check_num_4' => !empty($check->checker_minggu4_id) ? 4 : '', // Ubah dari boolean ke number/string
            'approved_by_1' => $check->approverMinggu1 ? $check->approverMinggu1->username : '',
            'approved_by_2' => $check->approverMinggu2 ? $check->approverMinggu2->username : '',
            'approved_by_3' => $check->approverMinggu3 ? $check->approverMinggu3->username : '',
            'approved_by_4' => $check->approverMinggu4 ? $check->approverMinggu4->username : ''
        ];
        
        // Format data hasil pemeriksaan berdasarkan item
        foreach ($items as $key => $item) {
            $result = $results->where('checked_items', $item)->first();
            
            if ($result) {
                $formattedResults[$key] = [
                    'item' => $item,
                    'minggu1' => $result->minggu1 ?? 'V', // PERBAIKAN 2: Default value jika null
                    'keterangan_minggu1' => $result->keterangan_minggu1 ?? '',
                    'minggu2' => $result->minggu2 ?? 'V',
                    'keterangan_minggu2' => $result->keterangan_minggu2 ?? '',
                    'minggu3' => $result->minggu3 ?? 'V',
                    'keterangan_minggu3' => $result->keterangan_minggu3 ?? '',
                    'minggu4' => $result->minggu4 ?? 'V',
                    'keterangan_minggu4' => $result->keterangan_minggu4 ?? '',
                ];
            } else {
                // Jika tidak ada data untuk item ini, siapkan struktur dengan default values
                $formattedResults[$key] = [
                    'item' => $item,
                    'minggu1' => 'V', // PERBAIKAN 3: Default ke 'V' bukan null
                    'keterangan_minggu1' => '',
                    'minggu2' => 'V',
                    'keterangan_minggu2' => '',
                    'minggu3' => 'V',
                    'keterangan_minggu3' => '',
                    'minggu4' => 'V',
                    'keterangan_minggu4' => '',
                ];
            }
        }
        
        // PERBAIKAN 4: Pastikan method isWeekApproved() ada di model atau gunakan alternatif
        // Jika method isWeekApproved() tidak ada, gunakan pengecekan langsung
        $approvalStatus = [
            'minggu1' => !empty($check->approver_minggu1_id) && $check->approver_minggu1_id != '-',
            'minggu2' => !empty($check->approver_minggu2_id) && $check->approver_minggu2_id != '-',
            'minggu3' => !empty($check->approver_minggu3_id) && $check->approver_minggu3_id != '-',
            'minggu4' => !empty($check->approver_minggu4_id) && $check->approver_minggu4_id != '-'
        ];
        
        // ALTERNATIF: Jika method isWeekApproved() sudah ada dan bekerja dengan baik
        // $approvalStatus = [
        //     'minggu1' => $check->isWeekApproved(1),
        //     'minggu2' => $check->isWeekApproved(2),
        //     'minggu3' => $check->isWeekApproved(3),
        //     'minggu4' => $check->isWeekApproved(4)
        // ];
        
        return view('slitting.edit', compact('check', 'formattedResults', 'items', 'checkerData', 'approvalStatus', 'user', 'currentGuard'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Validasi input
        $validated = $request->validate([
            'nomer_slitting' => 'required|integer|between:1,10',
            'bulan' => 'required|date_format:Y-m',
        ]);
    
        // Cari data slitting yang akan diupdate
        $slittingCheck = SlittingCheck::findOrFail($id);
    
        // Cek apakah ada perubahan pada data utama (nomer_slitting, bulan)
        if ($slittingCheck->nomer_slitting != $request->nomer_slitting || 
            $slittingCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = SlittingCheck::where('nomer_slitting', $request->nomer_slitting)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $id) // Kecualikan record saat ini
                ->first();
            
            if ($existingRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data dengan nomor slitting dan bulan yang sama sudah ada!');
            }
        }
    
        // Mulai transaksi database
        DB::beginTransaction();
    
        try {
            // Siapkan data untuk update checker berdasarkan guard
            $checkerData = [];
            
            // Proses setiap minggu untuk checker
            for ($week = 1; $week <= 4; $week++) {
                $checkedBy = $request->input("checked_by_{$week}");
                $checkNum = $request->input("check_num_{$week}");
                
                if ($checkedBy && $checkNum) {
                    // Cari checker berdasarkan username dan guard
                    $checker = null;
                    if ($currentGuard === 'checker') {
                        $checker = \App\Models\Checker::where('username', $checkedBy)->first();
                    } elseif ($currentGuard === 'approver') {
                        $checker = \App\Models\Approver::where('username', $checkedBy)->first();
                    }
                    
                    if ($checker) {
                        $checkerData["checker_minggu{$week}_id"] = $checker->id;
                    } else {
                        // Jika checker tidak ditemukan, set null
                        $checkerData["checker_minggu{$week}_id"] = null;
                    }
                } else {
                    // Jika tidak ada checker dipilih, set null
                    $checkerData["checker_minggu{$week}_id"] = null;
                }
            }
    
            // Update data SlittingCheck dengan data baru dan checker yang sudah diproses
            $updateData = array_merge([
                'nomer_slitting' => $request->nomer_slitting,
                'bulan' => $request->bulan,
            ], $checkerData);
            
            $slittingCheck->update($updateData);
            
            // Definisikan items yang diperiksa
            $items = [
                1 => 'Conveyor',
                2 => 'Motor Conveyor',
                3 => 'Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Inverter',
                6 => 'Vibrator',
                7 => 'Motor Vibrator',
                8 => 'Motor Blower',
                9 => 'Selang angin',
                10 => 'Flow Control',
                11 => 'Sensor', 
                12 => 'Limit Switch',
                13 => 'Pisau Cutting',
                14 => 'Motor Cutting',
                15 => 'Elemen',
                16 => 'Regulator',
                17 => 'Air Filter',
            ];
            
            // Ambil data existing dari tabel SlittingResult
            $existingResults = SlittingResult::where('check_id', $id)->get()->keyBy('checked_items');
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Persiapkan data untuk update atau create
                $resultData = [
                    'minggu1' => $request->input("check_1.{$itemId}", '-'),
                    'keterangan_minggu1' => $request->input("keterangan_1.{$itemId}"),
                    'minggu2' => $request->input("check_2.{$itemId}", '-'),
                    'keterangan_minggu2' => $request->input("keterangan_2.{$itemId}"),
                    'minggu3' => $request->input("check_3.{$itemId}", '-'),
                    'keterangan_minggu3' => $request->input("keterangan_3.{$itemId}"),
                    'minggu4' => $request->input("check_4.{$itemId}", '-'),
                    'keterangan_minggu4' => $request->input("keterangan_4.{$itemId}"),
                ];
                
                // Cek apakah record sudah ada
                $existingResult = $existingResults->get($itemName);
                
                if ($existingResult) {
                    // Update hanya jika minggu tersebut belum disetujui
                    $updateFields = [];
                    
                    for ($week = 1; $week <= 4; $week++) {
                        if (!$slittingCheck->isWeekApproved($week)) {
                            $updateFields["minggu{$week}"] = $resultData["minggu{$week}"];
                            $updateFields["keterangan_minggu{$week}"] = $resultData["keterangan_minggu{$week}"];
                        }
                    }
                    
                    if (!empty($updateFields)) {
                        $existingResult->update($updateFields);
                    }
                } else {
                    // Buat record baru jika belum ada
                    $resultData['check_id'] = $id;
                    $resultData['checked_items'] = $itemName;
                    SlittingResult::create($resultData);
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('slitting.index')
                ->with('success', 'Data berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Fetch the slitting check with its results and relations
        $check = SlittingCheck::with([
            'checkerMinggu1',
            'checkerMinggu2', 
            'checkerMinggu3',
            'checkerMinggu4',
            'approverMinggu1',
            'approverMinggu2',
            'approverMinggu3',
            'approverMinggu4'
        ])->findOrFail($id);
        
        // Get all results related to this check
        $results = SlittingResult::where('check_id', $id)->get();
        
        // Define items like in the edit function
        $items = [
            1 => 'Conveyor',
            2 => 'Motor Conveyor',
            3 => 'Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Inverter',
            6 => 'Vibrator',
            7 => 'Motor Vibrator',
            8 => 'Motor Blower',
            9 => 'Selang angin',
            10 => 'Flow Control',
            11 => 'Sensor',
            12 => 'Limit Switch',
            13 => 'Pisau Cutting',
            14 => 'Motor Cutting',
            15 => 'Elemen',
            16 => 'Regulator',
            17 => 'Air Filter',
        ];
        
        // Format data untuk tampilan
        $formattedResults = [];
        
        // Format hasil inspeksi berdasarkan item
        foreach ($items as $key => $item) {
            // First try to find by item text
            $result = $results->where('checked_items', $item)->first();
            
            // If not found, try to find by item number in different formats
            if (!$result) {
                $result = $results->first(function($record) use ($key) {
                    // Try to match item_id if it exists
                    if (isset($record->item_id) && $record->item_id == $key) {
                        return true;
                    }
                    
                    // Try to match "1:Conveyor" format
                    if (strpos($record->checked_items, $key.':') === 0) {
                        return true;
                    }
                    
                    // Try to match just the number
                    if ($record->checked_items == (string)$key) {
                        return true;
                    }
                    
                    return false;
                });
            }
            
            if ($result) {
                $formattedResults[$key] = [
                    'minggu1' => $result->minggu1 ?? '-',
                    'keterangan_minggu1' => $result->keterangan_minggu1 ?? '',
                    'minggu2' => $result->minggu2 ?? '-',
                    'keterangan_minggu2' => $result->keterangan_minggu2 ?? '',
                    'minggu3' => $result->minggu3 ?? '-',
                    'keterangan_minggu3' => $result->keterangan_minggu3 ?? '',
                    'minggu4' => $result->minggu4 ?? '-',
                    'keterangan_minggu4' => $result->keterangan_minggu4 ?? '',
                ];
            } else {
                // If no data for this item, prepare empty structure with defaults
                $formattedResults[$key] = [
                    'minggu1' => '-',
                    'keterangan_minggu1' => '',
                    'minggu2' => '-',
                    'keterangan_minggu2' => '',
                    'minggu3' => '-',
                    'keterangan_minggu3' => '',
                    'minggu4' => '-',
                    'keterangan_minggu4' => '',
                ];
            }
        }
        
        // Siapkan data checker dan approver untuk setiap minggu
        $checkers = [];
        $approvers = [];
        
        for ($i = 1; $i <= 4; $i++) {
            // Get checker data
            $checkerRelation = 'checkerMinggu' . $i;
            $checkers[$i] = [
                'id' => $check->{'checker_minggu' . $i . '_id'},
                'name' => $check->$checkerRelation ? $check->$checkerRelation->username : null,
                'has_data' => !empty($check->{'checker_minggu' . $i . '_id'})
            ];
            
            // Get approver data
            $approverRelation = 'approverMinggu' . $i;
            $approvers[$i] = [
                'id' => $check->{'approver_minggu' . $i . '_id'},
                'name' => $check->$approverRelation ? $check->$approverRelation->username : null,
                'has_data' => !empty($check->{'approver_minggu' . $i . '_id'})
            ];
        }
        
        return view('slitting.show', compact('check', 'formattedResults', 'checkers', 'approvers', 'user', 'currentGuard'));
    }

    public function approve(Request $request, $id)
    {
        $user = $this->ensureAuthenticatedUser(['approver']);
        if (!is_object($user)) return $user;
        if (!$this->isAuthenticatedAs('approver')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
        }
        
        $check = SlittingCheck::findOrFail($id);
        
        // Process approvals for each week
        for ($week = 1; $week <= 4; $week++) {
            $approverKey = "approved_by_minggu{$week}";
            $approverIdField = "approver_minggu{$week}_id"; // Field yang benar di model
            
            if ($request->has($approverKey) && !empty($request->input($approverKey))) {
                // Check if this week is not already approved
                if (empty($check->$approverIdField)) {
                    // Set the approver_minggu_id field dengan ID user yang sedang login
                    $check->$approverIdField = $user->id;
                }
            }
        }
        
        // Save will automatically trigger the status update logic in the model
        $check->save();
        
        return redirect()
            ->route('slitting.index')
            ->with('success', 'Persetujuan berhasil disimpan');
    }
   
    public function reviewPdf($id) 
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        // Ambil data pemeriksaan slitting berdasarkan ID
        $slittingCheck = SlittingCheck::findOrFail($id);
        
        // Ambil data form terkait (sesuaikan nomor form untuk slitting)
        $form = Form::where('nomor_form', 'APTEK/014/REV.00')->firstOrFail(); // Ganti XXX dengan nomor form yang sesuai
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil semua hasil pemeriksaan terkait check ini
        $results = SlittingResult::where('check_id', $id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Conveyor',
            2 => 'Motor Conveyor',
            3 => 'Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Inverter',
            6 => 'Vibrator',
            7 => 'Motor Vibrator',
            8 => 'Motor Blower',
            9 => 'Selang angin',
            10 => 'Flow Control',
            11 => 'Sensor',
            12 => 'Limit Switch',
            13 => 'Pisau Cutting',
            14 => 'Motor Cutting',
            15 => 'Elemen',
            16 => 'Regulator',
            17 => 'Air Filter',
        ];
        
        // Siapkan semua field check dan keterangan untuk empat minggu
        for ($j = 1; $j <= 4; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per minggu
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari results
            foreach ($items as $i => $item) {
                // Cari hasil berdasarkan item menggunakan logika yang sama seperti di fungsi show
                $result = $results->where('checked_items', $item)->first();
                
                // Jika tidak ditemukan, coba cari dengan format lain
                if (!$result) {
                    $result = $results->first(function($record) use ($i) {
                        // Coba cocokkan item_id jika ada
                        if (isset($record->item_id) && $record->item_id == $i) {
                            return true;
                        }
                        
                        // Coba cocokkan format "1:Conveyor"
                        if (strpos($record->checked_items, $i.':') === 0) {
                            return true;
                        }
                        
                        // Coba cocokkan hanya angka
                        if ($record->checked_items == (string)$i) {
                            return true;
                        }
                        
                        return false;
                    });
                }
                
                ${'check_' . $j}[$i] = optional($result)->{'minggu' . $j} ?? '';
                ${'keterangan_' . $j}[$i] = optional($result)->{'keterangan_minggu' . $j} ?? '';
            }
            
            // Tambahkan array ke slittingCheck object
            $slittingCheck->{'check_' . $j} = ${'check_' . $j};
            $slittingCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('slitting.review_pdf', [
            'slittingCheck' => $slittingCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items,
            'user' => $user,
            'currentGuard' => $currentGuard
        ]);
        
        // Return view untuk preview
        return $view;
    }
    
    public function downloadPdf($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        // Ambil data pemeriksaan slitting berdasarkan ID
        $slittingCheck = SlittingCheck::findOrFail($id);
        
        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/014/REV.00')->firstOrFail();
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil semua hasil pemeriksaan terkait check ini
        $results = SlittingResult::where('check_id', $id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Conveyor',
            2 => 'Motor Conveyor',
            3 => 'Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Inverter',
            6 => 'Vibrator',
            7 => 'Motor Vibrator',
            8 => 'Motor Blower',
            9 => 'Selang angin',
            10 => 'Flow Control',
            11 => 'Sensor',
            12 => 'Limit Switch',
            13 => 'Pisau Cutting',
            14 => 'Motor Cutting',
            15 => 'Elemen',
            16 => 'Regulator',
            17 => 'Air Filter',
        ];
        
        // Siapkan semua field check dan keterangan untuk empat minggu
        for ($j = 1; $j <= 4; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per minggu
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari results
            foreach ($items as $i => $item) {
                // Cari hasil berdasarkan item menggunakan logika yang sama seperti di fungsi show
                $result = $results->where('checked_items', $item)->first();
                
                // Jika tidak ditemukan, coba cari dengan format lain
                if (!$result) {
                    $result = $results->first(function($record) use ($i) {
                        // Coba cocokkan item_id jika ada
                        if (isset($record->item_id) && $record->item_id == $i) {
                            return true;
                        }
                        
                        // Coba cocokkan format "1:Conveyor"
                        if (strpos($record->checked_items, $i.':') === 0) {
                            return true;
                        }
                        
                        // Coba cocokkan hanya angka
                        if ($record->checked_items == (string)$i) {
                            return true;
                        }
                        
                        return false;
                    });
                }
                
                ${'check_' . $j}[$i] = optional($result)->{'minggu' . $j} ?? '';
                ${'keterangan_' . $j}[$i] = optional($result)->{'keterangan_minggu' . $j} ?? '';
            }
            
            // Tambahkan array ke slittingCheck object
            $slittingCheck->{'check_' . $j} = ${'check_' . $j};
            $slittingCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Format tanggal dari model SlittingCheck untuk mendapatkan bulan dan tahun
        $tanggal = new \DateTime($slittingCheck->tanggal);
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
        
        // Ganti nama bulan dalam bahasa Inggris dengan nama bulan dalam Bahasa Indonesia
        $bulanFormatted = $bulanIndonesia[$bulan] ?? $bulan;
        
        // Generate nama file PDF dengan format Slitting_nomer_1_bulan_Mei_2025
        $filename = 'Slitting_nomer_' . $slittingCheck->nomer_slitting . '_bulan_' . $bulanFormatted . '_' . $tahun . '.pdf';
        
        // Render view sebagai HTML
        $html = view('slitting.review_pdf', [
            'slittingCheck' => $slittingCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items,
            'user' => $user,
            'currentGuard' => $currentGuard
        ])->render();
        
        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Atur ukuran dan orientasi halaman
        $dompdf->setPaper('A4', 'potrait');
        
        // Render PDF (mengubah HTML menjadi PDF)
        $dompdf->render();
        
        // Download file PDF
        return $dompdf->stream($filename, [
            'Attachment' => false, // Set true untuk download otomatis
        ]);
    }
}
