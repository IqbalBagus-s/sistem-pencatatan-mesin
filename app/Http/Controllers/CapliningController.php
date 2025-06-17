<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\CapliningCheck;
use App\Models\CapliningResult;
use App\Models\Form;
use App\Models\Activity;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF
use App\Traits\WithAuthentication;

class CapliningController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $query = CapliningCheck::query();

        // Filter berdasarkan nama checker atau approver (username, bukan checked_by/approved_by)
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->orWhereHas('checker1', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('checker2', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('checker3', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('checker4', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('checker5', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                // Jika ingin bisa cari nama approver juga, tambahkan baris berikut:
                $q->orWhereHas('approver1', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('approver2', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('approver3', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('approver4', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
                $q->orWhereHas('approver5', function($sub) use ($search) {
                    $sub->where('username', 'LIKE', $search);
                });
            });
        }

        // Filter berdasarkan nomor caplining
        if ($request->filled('search_caplining')) {
            $query->where('nomer_caplining', $request->search_caplining);
        }
        
        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            try {
                $tanggal = $request->tanggal;
                $query->where(function($q) use ($tanggal) {
                    $q->whereDate('tanggal_check1', $tanggal)
                    ->orWhereDate('tanggal_check2', $tanggal)
                    ->orWhereDate('tanggal_check3', $tanggal)
                    ->orWhereDate('tanggal_check4', $tanggal)
                    ->orWhereDate('tanggal_check5', $tanggal);
                });
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Format tanggal tidak valid.');
            }
        }
        
        // Mengurutkan data berdasarkan created_at atau updated_at terbaru (descending)
        $query->orderBy('created_at', 'desc');
        
        // Ambil data dengan paginasi dan eager load hasil pemeriksaan
        $checks = $query->with('results')->paginate(10)->appends($request->query());
        
        // Load semua data tambahan untuk setiap check
        foreach ($checks as $check) {
            // Dapatkan informasi checker dari semua kolom checked_by
            $check->allCheckers = collect([
                $check->checked_by1,
                $check->checked_by2,
                $check->checked_by3,
                $check->checked_by4,
                $check->checked_by5
            ])
                ->filter() // menghapus nilai null/empty
                ->unique() // menghapus duplikat 
                ->values() // re-index array
                ->toArray();
                
            // Hitung jumlah item yang sudah dicheck
            $checkedItems = 0;
            foreach ($check->results as $result) {
                // Hitung berapa check yang sudah diisi
                $checksCompleted = 0;
                for ($i = 1; $i <= 5; $i++) {
                    if (!empty($result->{"check{$i}"})) {
                        $checksCompleted++;
                    }
                }
                
                if ($checksCompleted > 0) {
                    $checkedItems++;
                }
            }
            
            $check->checkedItemsCount = $checkedItems;
            
            // PERBAIKAN: Verifikasi status approval berdasarkan approved_by1-5
            $approvedFields = collect([
                $check->approved_by1,
                $check->approved_by2,
                $check->approved_by3,
                $check->approved_by4,
                $check->approved_by5
            ])->filter(); // menghapus nilai null/empty
            
            $totalApproved = $approvedFields->count();
            
            // Tentukan status approval berdasarkan jumlah field approved_by yang terisi
            if ($totalApproved === 0) {
                $check->approvalStatus = 'not_approved'; // Belum ada approval sama sekali
                $check->isApproved = false;
            } elseif ($totalApproved < 5) {
                $check->approvalStatus = 'partially_approved'; // Ada approval tapi tidak lengkap
                $check->isApproved = false;
            } else {
                $check->approvalStatus = 'fully_approved'; // Semua approved_by1-5 sudah terisi
                $check->isApproved = true;
            }
            
            // Kumpulkan daftar semua approver untuk penggunaan lain jika diperlukan
            $check->allApprovers = $approvedFields->values()->toArray();
            
            // Menghitung dan menyimpan rentang tanggal
            $tanggalFormatted = $this->getFormattedTanggalRange($check);
            $check->hasTanggal = !is_null($tanggalFormatted);
            $check->tanggalFormatted = $tanggalFormatted;
        }

        return view('caplining.index', compact('checks', 'user', 'currentGuard'));
    }

    private function getFormattedTanggalRange($check)
    {
        // Fields yang berisi tanggal yang ingin kita cek
        $tanggalFields = [
            'tanggal_check1', 'tanggal_check2', 'tanggal_check3', 
            'tanggal_check4', 'tanggal_check5'
        ];
        
        // Kumpulkan tanggal yang valid
        $validDates = [];
        
        foreach ($tanggalFields as $field) {
            // Periksa jika field ini ada pada model
            if (isset($check->$field) && !empty($check->$field)) {
                try {
                    // Coba untuk parse tanggal
                    $date = \Carbon\Carbon::parse($check->$field);
                    $validDates[] = $date;
                } catch (\Exception $e) {
                    // Lewati jika parsing gagal
                    continue;
                }
            }
        }
        
        // Jika tidak ada tanggal yang valid, kembalikan null
        if (empty($validDates)) {
            return null;
        }
        
        // Jika hanya ada satu tanggal, kembalikan tanggal tersebut
        if (count($validDates) === 1) {
            return $validDates[0]->locale('id')->isoFormat('D MMMM Y');
        }
        
        // Urutkan tanggal
        usort($validDates, function($a, $b) {
            return $a->timestamp - $b->timestamp;
        });
        
        // Ambil tanggal terkecil dan terbesar
        $earliestDate = $validDates[0];
        $latestDate = $validDates[count($validDates) - 1];
        
        // Set locale untuk format Bahasa Indonesia
        $earliestDate->locale('id');
        $latestDate->locale('id');
        
        // Kembalikan rentang tanggal
        return $earliestDate->isoFormat('D MMMM Y') . ' - ' . $latestDate->isoFormat('D MMMM Y');
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        return view('caplining.create', compact('user', 'currentGuard'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Custom error messages
        $customMessages = [
            'nomer_caplining.required' => 'Silakan pilih nomor caplining terlebih dahulu!',
        ];
        
        // Validasi input
        $validated = $request->validate([
            'nomer_caplining' => 'required|integer|between:1,6',
        ], $customMessages);

        // Debug: Cek data yang diterima dari form
        Log::info('Data dari form caplining:', $request->all());

        // Fungsi format tanggal internal
        $formatTanggalForDB = function($tanggal) {
            if (empty($tanggal)) {
                return null;
            }
            
            // Format yang diterima dari form: "DD Mmm YYYY" (contoh: "15 Mei 2025")
            $bulanMap = [
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'Mei' => '05', 
                'Jun' => '06', 'Jul' => '07', 'Ags' => '08', 'Sep' => '09', 'Okt' => '10', 
                'Nov' => '11', 'Des' => '12'
            ];
            
            $parts = explode(' ', $tanggal);
            if (count($parts) === 3) {
                $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $month = $bulanMap[$parts[1]] ?? '01';
                $year = $parts[2];
                
                return "$year-$month-$day";
            }
            
            return null;
        };

        // Array untuk menyimpan tanggal yang diinput
        $tanggalChecks = [];
        
        // Kumpulkan semua tanggal yang diinput untuk validasi
        for ($i = 1; $i <= 5; $i++) {
            if ($request->has("tanggal_$i") && !empty($request->{"tanggal_$i"})) {
                $formattedDate = $formatTanggalForDB($request->{"tanggal_$i"});
                if ($formattedDate) {
                    $tanggalChecks[] = $formattedDate;
                }
            }
        }
        
        // Validasi tanggal - cek apakah tanggal sudah ada di database untuk nomer_caplining yang sama
        $existingDates = [];
        if (!empty($tanggalChecks)) {
            // Query untuk mencari tanggal yang sudah ada
            $existingRecords = CapliningCheck::where('nomer_caplining', $request->nomer_caplining)
                ->where(function($query) use ($tanggalChecks) {
                    foreach ($tanggalChecks as $date) {
                        $query->orWhere('tanggal_check1', $date)
                            ->orWhere('tanggal_check2', $date)
                            ->orWhere('tanggal_check3', $date)
                            ->orWhere('tanggal_check4', $date)
                            ->orWhere('tanggal_check5', $date);
                    }
                })
                ->get();
            
            // Jika ada tanggal yang sudah ada, kumpulkan untuk pesan error
            if ($existingRecords->count() > 0) {
                foreach ($existingRecords as $record) {
                    for ($i = 1; $i <= 5; $i++) {
                        $dateField = "tanggal_check$i";
                        if ($record->$dateField && in_array($record->$dateField, $tanggalChecks)) {
                            // Format tanggal kembali ke format yang dimengerti user
                            $date = \Carbon\Carbon::parse($record->$dateField);
                            $bulanSingkat = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 
                                6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 
                                11 => 'Nov', 12 => 'Des'
                            ];
                            $formattedDate = $date->format('d') . ' ' . $bulanSingkat[$date->format('n')] . ' ' . $date->format('Y');
                            $existingDates[] = $formattedDate;
                        }
                    }
                }
            }
            
            // Jika ada tanggal yang sudah ada, kembalikan error
            if (!empty($existingDates)) {
                // Log informasi detail tentang error
                Log::info('Tanggal duplikat terdeteksi: ', $existingDates);
                
                // Membuat pesan error yang lebih eksplisit
                $errorMsg = 'Data di tanggal ' . implode(', ', array_unique($existingDates)) . ' telah ada';
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tanggal_duplicate' => $errorMsg])
                    ->with('error', $errorMsg);
            }
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Data utama untuk tabel caplining_checks
            $data = [
                'nomer_caplining' => $request->nomer_caplining,
            ];
            
            // Array untuk menyimpan tanggal yang diformat untuk activity log
            $tanggalFormatted = [];
            
            // Set tanggal dan user data untuk masing-masing kolom check
            for ($i = 1; $i <= 5; $i++) {
                // Simpan tanggal yang dipilih
                if ($request->has("tanggal_$i") && !empty($request->{"tanggal_$i"})) {
                    $formattedDate = $formatTanggalForDB($request->{"tanggal_$i"});
                    $data["tanggal_check$i"] = $formattedDate;
                    
                    // Simpan tanggal yang diformat untuk activity log
                    $tanggalFormatted["tanggal_check$i"] = $request->{"tanggal_$i"};
                    
                    Log::info("Tanggal $i: " . $request->{"tanggal_$i"} . " -> " . $data["tanggal_check$i"]);
                } else {
                    $data["tanggal_check$i"] = null;
                }
                
                // Simpan checker_id jika ada (menggunakan ID bukan username)
                if ($request->has("checker_id$i") && !empty($request->{"checker_id$i"})) {
                    $data["checker_id$i"] = $request->{"checker_id$i"};
                    Log::info("Checker ID $i: " . $data["checker_id$i"]);
                } else {
                    $data["checker_id$i"] = null;
                }
                
                // Untuk approver_id, ini mungkin diisi secara terpisah
                if ($request->has("approver_id$i") && !empty($request->{"approver_id$i"})) {
                    $data["approver_id$i"] = $request->{"approver_id$i"};
                } else {
                    $data["approver_id$i"] = null;
                }
            }
            
            // Buat record CapliningCheck
            $capliningCheck = CapliningCheck::create($data);
            
            // Log untuk memastikan record berhasil dibuat
            Log::info('Record caplining check dibuat dengan ID: ' . $capliningCheck->id);
            
            // Ambil ID dari record yang baru dibuat
            $checkId = $capliningCheck->id;
            
            // Definisikan item yang diperiksa
            $items = [
                1 => 'Kelistrikan',
                2 => 'MCB',
                3 => 'PLC',
                4 => 'Power Supply',
                5 => 'Relay',
                6 => 'Selenoid',
                7 => 'Selang Angin',
                8 => 'Regulator',
                9 => 'Pir',
                10 => 'Motor',
                11 => 'Vanbelt',
                12 => 'Conveyor',
                13 => 'Motor Conveyor',
                14 => 'Vibrator',
                15 => 'Motor Vibrator',
                16 => 'Gear Box',
                17 => 'Rantai',
                18 => 'Stang Penggerak',
                19 => 'Suction Pad',
                20 => 'Sensor',
            ];
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Data untuk tabel hasil
                $resultData = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                // Set nilai check untuk setiap kolom pemeriksaan (1-5)
                for ($i = 1; $i <= 5; $i++) {
                    // Simpan data check dan keterangan terlepas dari status tanggal
                    // Cek apakah ada data check untuk item ini
                    $checkKey = "check_$i";
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData["check$i"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData["check$i"] = '-';
                    }
                    
                    // Cek apakah ada keterangan untuk item ini
                    $keteranganKey = "keterangan_$i";
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData["keterangan$i"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData["keterangan$i"] = null;
                    }
                    
                    // Debug log
                    Log::info("Item #$itemId ($itemName) - Check$i: " . $resultData["check$i"] . 
                            ", Keterangan$i: " . $resultData["keterangan$i"]);
                }
                
                // Buat record hasil pemeriksaan
                $result = CapliningResult::create($resultData);
                
                // Log untuk memastikan record hasil berhasil dibuat
                Log::info("Item #{$itemId} ({$itemName}) berhasil disimpan dengan ID: " . $result->id);
            }
            
            // LOG AKTIVITAS - Tambahkan setelah data berhasil disimpan
            $tanggalList = array_filter($tanggalFormatted); // Hapus yang null
            $tanggalString = !empty($tanggalList) ? implode(', ', $tanggalList) : 'Tidak ada tanggal';
            
            // Ambil informasi checker yang melakukan pemeriksaan untuk log
            $checkerIds = [];
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($data["checker_id$i"])) {
                    $checkerIds[] = $data["checker_id$i"];
                }
            }
            $uniqueCheckerIds = array_unique($checkerIds);
            
            Activity::logActivity(
                'checker',                                              // user_type
                $user->id,                                             // user_id
                $user->username,                                       // user_name
                'created',                                             // action
                'Checker ' . $user->username . ' membuat pemeriksaan Caplining Nomor ' . $request->nomer_caplining . ' untuk tanggal: ' . $tanggalString,  // description
                'caplining_check',                                     // target_type
                $capliningCheck->id,                                   // target_id
                [
                    'nomer_caplining' => $request->nomer_caplining,
                    'tanggal_checks' => $tanggalFormatted,
                    'checker_ids' => $uniqueCheckerIds,
                    'total_items' => count($items),
                    'items_checked' => array_values($items),
                    'jumlah_tanggal_diinput' => count($tanggalList),
                    'status' => $capliningCheck->status ?? 'belum_disetujui'
                ]                                                      // details (JSON)
            );
            
            // Commit transaksi
            DB::commit();
            
            Log::info('Transaksi caplining berhasil disimpan');
            
            return redirect()->route('caplining.index')
                ->with('success', 'Data berhasil disimpan!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            // Log error detail untuk debugging
            Log::error('Error saat menyimpan data caplining: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
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
        
        // Model CapliningCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new CapliningCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $check->load([
            'checker1', 'checker2', 'checker3', 'checker4', 'checker5',
            'approver1', 'approver2', 'approver3', 'approver4', 'approver5'
        ]);
        
        // Ambil data hasil pemeriksaan
        $results = CapliningResult::where('check_id', $check->id)->get();
        
        // Siapkan data untuk view dalam format yang mudah digunakan
        $formattedData = collect();
        
        // Definisikan item yang diperiksa (sama seperti di fungsi store)
        $items = [
            1 => 'Kelistrikan',
            2 => 'MCB',
            3 => 'PLC',
            4 => 'Power Supply',
            5 => 'Relay',
            6 => 'Selenoid',
            7 => 'Selang Angin',
            8 => 'Regulator',
            9 => 'Pir',
            10 => 'Motor',
            11 => 'Vanbelt',
            12 => 'Conveyor',
            13 => 'Motor Conveyor',
            14 => 'Vibrator',
            15 => 'Motor Vibrator',
            16 => 'Gear Box',
            17 => 'Rantai',
            18 => 'Stang Penggerak',
            19 => 'Suction Pad',
            20 => 'Sensor',
        ];
        
        // Format tanggal untuk display
        $formatDisplayTanggal = function($dbDate) {
            if (empty($dbDate)) {
                return null;
            }
            
            $date = \Carbon\Carbon::parse($dbDate);
            $bulanSingkat = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 
                6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 
                11 => 'Nov', 12 => 'Des'
            ];
            
            return $date->format('d') . ' ' . $bulanSingkat[$date->format('n')] . ' ' . $date->format('Y');
        };
        
        // Proses data hasil pemeriksaan untuk setiap item
        foreach ($items as $itemId => $itemName) {
            $itemResult = $results->where('checked_items', $itemName)->first();
            
            if ($itemResult) {
                // Untuk setiap kolom check (1-5)
                for ($i = 1; $i <= 5; $i++) {
                    $checkField = "check$i";
                    $keteranganField = "keterangan$i";
                    
                    // Hanya tambahkan ke formattedData jika ada tanggal check untuk kolom ini
                    $tanggalField = "tanggal_check$i";
                    $checkerIdField = "checker_id$i";
                    $approverIdField = "approver_id$i";
                    
                    if ($check->$tanggalField) {
                        // Ambil username checker dari relasi
                        $checkerUsername = null;
                        $checkerRelation = "checker$i";
                        if ($check->$checkerRelation) {
                            $checkerUsername = $check->$checkerRelation->username;
                        }
                        
                        // Ambil username approver dari relasi
                        $approverUsername = null;
                        $approverRelation = "approver$i";
                        if ($check->$approverRelation) {
                            $approverUsername = $check->$approverRelation->username;
                        }
                        
                        $formattedData->push([
                            'check_number' => $i,
                            'item_id' => $itemId,
                            'item_name' => $itemName,
                            'tanggal' => $formatDisplayTanggal($check->$tanggalField),
                            'tanggal_raw' => $check->$tanggalField,
                            'result' => $itemResult->$checkField,
                            'keterangan' => $itemResult->$keteranganField,
                            'checker_id' => $check->$checkerIdField,
                            'checked_by' => $checkerUsername,
                            'approver_id' => $check->$approverIdField,
                            'approved_by' => $approverUsername
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan informasi untuk tanggal yang mungkin belum memiliki item spesifik
        for ($i = 1; $i <= 5; $i++) {
            $tanggalField = "tanggal_check$i";
            $checkerIdField = "checker_id$i";
            $approverIdField = "approver_id$i";
            
            if ($check->$tanggalField && !$formattedData->where('check_number', $i)->count()) {
                // Ambil username checker dari relasi
                $checkerUsername = null;
                $checkerRelation = "checker$i";
                if ($check->$checkerRelation) {
                    $checkerUsername = $check->$checkerRelation->username;
                }
                
                // Ambil username approver dari relasi
                $approverUsername = null;
                $approverRelation = "approver$i";
                if ($check->$approverRelation) {
                    $approverUsername = $check->$approverRelation->username;
                }
                
                $formattedData->push([
                    'check_number' => $i,
                    'tanggal' => $formatDisplayTanggal($check->$tanggalField),
                    'tanggal_raw' => $check->$tanggalField,
                    'checker_id' => $check->$checkerIdField,
                    'checked_by' => $checkerUsername,
                    'approver_id' => $check->$approverIdField,
                    'approved_by' => $approverUsername
                ]);
            }
        }
        
        // Group data berdasarkan nomor check untuk memudahkan penggunaan di view
        $groupedData = $formattedData->groupBy('check_number');
        
        // Log untuk debugging
        Log::info('Data caplining untuk edit:', [
            'check_id' => $check->id,
            'nomer_caplining' => $check->nomer_caplining,
            'total_items' => $formattedData->count(),
            'checkers' => $check->getAllCheckers(),
            'approvers' => $check->getAllApprovers()
        ]);
        
        return view('caplining.edit', compact('check', 'groupedData', 'items', 'user', 'currentGuard'));
    }

    public function update(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Custom error messages
        $customMessages = [
            'nomer_caplining.required' => 'Silakan pilih nomor caplining terlebih dahulu!',
        ];
        
        // Validasi input
        $validated = $request->validate([
            'nomer_caplining' => 'required|integer|between:1,6',
        ], $customMessages);

        // Debug: Cek data yang diterima dari form
        Log::info('Data dari form caplining update:', $request->all());

        // Model CapliningCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $capliningCheck = (new CapliningCheck)->resolveRouteBinding($hashid);

        // Fungsi format tanggal internal
        $formatTanggalForDB = function($tanggal) {
            if (empty($tanggal)) {
                return null;
            }
            
            // Format yang diterima dari form: "DD Mmm YYYY" (contoh: "15 Mei 2025")
            $bulanMap = [
                'Jan' => '01', 'Feb' => '02', 'Mar' => '03', 'Apr' => '04', 'Mei' => '05', 
                'Jun' => '06', 'Jul' => '07', 'Ags' => '08', 'Sep' => '09', 'Okt' => '10', 
                'Nov' => '11', 'Des' => '12'
            ];
            
            $parts = explode(' ', $tanggal);
            if (count($parts) === 3) {
                $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $month = $bulanMap[$parts[1]] ?? '01';
                $year = $parts[2];
                
                return "$year-$month-$day";
            }
            
            return null;
        };

        // Array untuk menyimpan tanggal yang diinput
        $tanggalChecks = [];
        
        // Kumpulkan semua tanggal yang diinput untuk validasi
        for ($i = 1; $i <= 5; $i++) {
            if ($request->has("tanggal_$i") && !empty($request->{"tanggal_$i"})) {
                $formattedDate = $formatTanggalForDB($request->{"tanggal_$i"});
                if ($formattedDate) {
                    $tanggalChecks[] = $formattedDate;
                }
            }
        }
        
        // Validasi 1: Cek apakah ada tanggal duplikat dalam input yang sama (internal record)
        $duplicatesInInput = array_diff_assoc($tanggalChecks, array_unique($tanggalChecks));
        if (!empty($duplicatesInInput)) {
            // Format tanggal duplikat kembali ke format yang dimengerti user
            $duplicateDatesFormatted = [];
            foreach (array_unique($duplicatesInInput) as $duplicateDate) {
                $date = \Carbon\Carbon::parse($duplicateDate);
                $bulanSingkat = [
                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 
                    6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 
                    11 => 'Nov', 12 => 'Des'
                ];
                $formattedDate = $date->format('d') . ' ' . $bulanSingkat[$date->format('n')] . ' ' . $date->format('Y');
                $duplicateDatesFormatted[] = $formattedDate;
            }
            
            Log::info('Tanggal duplikat dalam input yang sama: ', $duplicateDatesFormatted);
            
            $errorMsg = 'Tidak boleh ada tanggal yang sama dalam satu record. Tanggal duplikat: ' . implode(', ', $duplicateDatesFormatted);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['tanggal_duplicate_internal' => $errorMsg])
                ->with('error', $errorMsg);
        }
        
        // Validasi 2: Cek apakah tanggal sudah ada di database untuk nomer_caplining yang sama (exclude current record)
        $existingDates = [];
        if (!empty($tanggalChecks)) {
            // Query untuk mencari tanggal yang sudah ada (exclude current record)
            $existingRecords = CapliningCheck::where('nomer_caplining', $request->nomer_caplining)
                ->where('id', '!=', $capliningCheck->id) // exclude current record
                ->where(function($query) use ($tanggalChecks) {
                    foreach ($tanggalChecks as $date) {
                        $query->orWhere('tanggal_check1', $date)
                            ->orWhere('tanggal_check2', $date)
                            ->orWhere('tanggal_check3', $date)
                            ->orWhere('tanggal_check4', $date)
                            ->orWhere('tanggal_check5', $date);
                    }
                })
                ->get();
            
            // Jika ada tanggal yang sudah ada, kumpulkan untuk pesan error
            if ($existingRecords->count() > 0) {
                foreach ($existingRecords as $record) {
                    for ($i = 1; $i <= 5; $i++) {
                        $dateField = "tanggal_check$i";
                        if ($record->$dateField && in_array($record->$dateField, $tanggalChecks)) {
                            // Format tanggal kembali ke format yang dimengerti user
                            $date = \Carbon\Carbon::parse($record->$dateField);
                            $bulanSingkat = [
                                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 
                                6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 
                                11 => 'Nov', 12 => 'Des'
                            ];
                            $formattedDate = $date->format('d') . ' ' . $bulanSingkat[$date->format('n')] . ' ' . $date->format('Y');
                            $existingDates[] = $formattedDate;
                        }
                    }
                }
            }
            
            // Jika ada tanggal yang sudah ada, kembalikan error
            if (!empty($existingDates)) {
                // Log informasi detail tentang error
                Log::info('Tanggal duplikat terdeteksi pada update: ', $existingDates);
                
                // Membuat pesan error yang lebih eksplisit
                $errorMsg = 'Data di tanggal ' . implode(', ', array_unique($existingDates)) . ' telah ada';
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['tanggal_duplicate' => $errorMsg])
                    ->with('error', $errorMsg);
            }
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Data utama untuk tabel caplining_checks
            $data = [
                'nomer_caplining' => $request->nomer_caplining,
            ];
            
            // Array untuk menyimpan tanggal yang diformat untuk activity log
            $tanggalFormatted = [];
            
            // Set tanggal dan user data untuk masing-masing kolom check
            for ($i = 1; $i <= 5; $i++) {
                // Simpan tanggal yang dipilih
                if ($request->has("tanggal_$i") && !empty($request->{"tanggal_$i"})) {
                    $formattedDate = $formatTanggalForDB($request->{"tanggal_$i"});
                    $data["tanggal_check$i"] = $formattedDate;
                    
                    // Simpan tanggal yang diformat untuk activity log
                    $tanggalFormatted["tanggal_check$i"] = $request->{"tanggal_$i"};
                    
                    Log::info("Update Tanggal $i: " . $request->{"tanggal_$i"} . " -> " . $data["tanggal_check$i"]);
                } else {
                    $data["tanggal_check$i"] = null;
                }
                
                // Simpan checker_id jika ada (menggunakan ID bukan username)
                if ($request->has("checker_id$i") && !empty($request->{"checker_id$i"})) {
                    $data["checker_id$i"] = $request->{"checker_id$i"};
                    Log::info("Update Checker ID $i: " . $data["checker_id$i"]);
                } else {
                    $data["checker_id$i"] = null;
                }
                
                // Untuk approver_id, ini mungkin diisi secara terpisah
                if ($request->has("approver_id$i") && !empty($request->{"approver_id$i"})) {
                    $data["approver_id$i"] = $request->{"approver_id$i"};
                } else {
                    $data["approver_id$i"] = null;
                }
            }
            
            // Update record CapliningCheck
            $capliningCheck->update($data);
            
            // Log untuk memastikan record berhasil diupdate
            Log::info('Record caplining check diupdate dengan ID: ' . $capliningCheck->id);
            
            // Ambil ID dari record yang diupdate
            $checkId = $capliningCheck->id;
            
            // Hapus hasil pemeriksaan lama
            CapliningResult::where('check_id', $checkId)->delete();
            
            // Definisikan item yang diperiksa
            $items = [
                1 => 'Kelistrikan',
                2 => 'MCB',
                3 => 'PLC',
                4 => 'Power Supply',
                5 => 'Relay',
                6 => 'Selenoid',
                7 => 'Selang Angin',
                8 => 'Regulator',
                9 => 'Pir',
                10 => 'Motor',
                11 => 'Vanbelt',
                12 => 'Conveyor',
                13 => 'Motor Conveyor',
                14 => 'Vibrator',
                15 => 'Motor Vibrator',
                16 => 'Gear Box',
                17 => 'Rantai',
                18 => 'Stang Penggerak',
                19 => 'Suction Pad',
                20 => 'Sensor',
            ];
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Data untuk tabel hasil
                $resultData = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                // Set nilai check untuk setiap kolom pemeriksaan (1-5)
                for ($i = 1; $i <= 5; $i++) {
                    // Simpan data check dan keterangan terlepas dari status tanggal
                    // Cek apakah ada data check untuk item ini
                    $checkKey = "check_$i";
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData["check$i"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData["check$i"] = '-';
                    }
                    
                    // Cek apakah ada keterangan untuk item ini
                    $keteranganKey = "keterangan_$i";
                    if (isset($request->$keteranganKey) && isset($request->$keteranganKey[$itemId])) {
                        $resultData["keterangan$i"] = $request->$keteranganKey[$itemId];
                    } else {
                        $resultData["keterangan$i"] = null;
                    }
                    
                    // Debug log
                    Log::info("Update Item #$itemId ($itemName) - Check$i: " . $resultData["check$i"] . 
                            ", Keterangan$i: " . $resultData["keterangan$i"]);
                }
                
                // Buat record hasil pemeriksaan baru
                $result = CapliningResult::create($resultData);
                
                // Log untuk memastikan record hasil berhasil dibuat
                Log::info("Update Item #{$itemId} ({$itemName}) berhasil disimpan dengan ID: " . $result->id);
            }
            
            // Commit transaksi
            DB::commit();
            
            Log::info('Transaksi caplining update berhasil disimpan');
            
            return redirect()->route('caplining.index')
                ->with('success', 'Data berhasil diupdate!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            // Log error detail untuk debugging
            Log::error('Error saat mengupdate data caplining: ' . $e->getMessage());
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
        
        // Model CapliningCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new CapliningCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $check->load([
            'checker1', 'checker2', 'checker3', 'checker4', 'checker5',
            'approver1', 'approver2', 'approver3', 'approver4', 'approver5'
        ]);
        
        // Ambil data hasil pemeriksaan
        $resultsData = CapliningResult::where('check_id', $check->id)->get();
        
        // Siapkan data untuk view dalam format yang sesuai dengan template show
        $results = collect();
        
        // Definisikan item yang diperiksa (sama seperti di view)
        $items = [
            1 => 'Kelistrikan',
            2 => 'MCB',
            3 => 'PLC',
            4 => 'Power Supply',
            5 => 'Relay',
            6 => 'Selenoid',
            7 => 'Selang Angin',
            8 => 'Regulator',
            9 => 'Pir',
            10 => 'Motor',
            11 => 'Vanbelt',
            12 => 'Conveyor',
            13 => 'Motor Conveyor',
            14 => 'Vibrator',
            15 => 'Motor Vibrator',
            16 => 'Gear Box',
            17 => 'Rantai',
            18 => 'Stang Penggerak',
            19 => 'Suction Pad',
            20 => 'Sensor',
        ];
        
        // Format tanggal untuk display
        $formatDisplayTanggal = function($dbDate) {
            if (empty($dbDate)) {
                return null;
            }
            
            $date = \Carbon\Carbon::parse($dbDate);
            $bulanSingkat = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 
                6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 
                11 => 'Nov', 12 => 'Des'
            ];
            
            return $date->format('d') . ' ' . $bulanSingkat[$date->format('n')] . ' ' . $date->format('Y');
        };
        
        // Proses data hasil pemeriksaan untuk setiap item
        foreach ($items as $itemId => $itemName) {
            $itemResult = $resultsData->where('checked_items', $itemName)->first();
            
            if ($itemResult) {
                // Untuk setiap kolom check (1-5)
                for ($i = 1; $i <= 5; $i++) {
                    $checkField = "check$i";
                    $keteranganField = "keterangan$i";
                    $tanggalField = "tanggal_check$i";
                    
                    if ($check->$tanggalField) {
                        // Ambil username checker dari relasi
                        $checkerUsername = null;
                        $checkerRelation = "checker$i";
                        if ($check->$checkerRelation) {
                            $checkerUsername = $check->$checkerRelation->username;
                        }
                        
                        // Ambil username approver dari relasi
                        $approverUsername = null;
                        $approverRelation = "approver$i";
                        if ($check->$approverRelation) {
                            $approverUsername = $check->$approverRelation->username;
                        }
                        
                        $results->push([
                            'tanggal_check' => $i,
                            'item_id' => $itemId,
                            'result' => $itemResult->$checkField,
                            'keterangan' => $itemResult->$keteranganField,
                            'checked_by' => $checkerUsername,
                            'approved_by' => $approverUsername ?? '',
                            'tanggal' => $formatDisplayTanggal($check->$tanggalField)
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan informasi untuk tanggal yang mungkin belum memiliki item spesifik
        // tetapi sudah memiliki checker atau approver
        for ($i = 1; $i <= 5; $i++) {
            $tanggalField = "tanggal_check$i";
            
            if ($check->$tanggalField && !$results->where('tanggal_check', $i)->count()) {
                // Ambil username checker dari relasi
                $checkerUsername = null;
                $checkerRelation = "checker$i";
                if ($check->$checkerRelation) {
                    $checkerUsername = $check->$checkerRelation->username;
                }
                
                // Ambil username approver dari relasi
                $approverUsername = null;
                $approverRelation = "approver$i";
                if ($check->$approverRelation) {
                    $approverUsername = $check->$approverRelation->username;
                }
                
                $results->push([
                    'tanggal_check' => $i,
                    'tanggal' => $formatDisplayTanggal($check->$tanggalField),
                    'checked_by' => $checkerUsername,
                    'approved_by' => $approverUsername ?? ''
                ]);
            }
        }
        
        // Log untuk debugging
        Log::info('Data caplining untuk detail view:', [
            'check_id' => $check->id,
            'hashid' => $check->hashid, // Tambahkan hashid untuk debugging
            'nomer_caplining' => $check->nomer_caplining,
            'total_items' => $results->count(),
            'checkers' => $check->getAllCheckers(),
            'approvers' => $check->getAllApprovers()
        ]);
        
        return view('caplining.show', compact('check', 'results', 'items', 'user', 'currentGuard'));
    }

    public function approve(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        
        // Model CapliningCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $capliningCheck = (new CapliningCheck)->resolveRouteBinding($hashid);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Data untuk update approver
            $updateData = [];
            $approvedColumns = [];
            
            // Proses untuk setiap kolom approver (1-5)
            for ($i = 1; $i <= 5; $i++) {
                $approverIdField = "approver_id$i";
                
                // Jika ada data approver_id yang dikirim dari form
                if ($request->has($approverIdField) && !empty($request->$approverIdField)) {
                    $updateData[$approverIdField] = $request->$approverIdField;
                    $approvedColumns[] = $i;
                    
                    Log::info("Approver ID $i: " . $request->$approverIdField . " untuk user: " . $user->username . " pada record hashid: " . $hashid);
                }
            }
            
            // Update data jika ada approver yang dipilih
            if (!empty($updateData)) {
                $capliningCheck->update($updateData);
                
                DB::commit();
                
                Log::info('Persetujuan caplining berhasil disimpan:', [
                    'hashid' => $hashid,
                    'record_id' => $capliningCheck->id,
                    'approved_columns' => $approvedColumns,
                    'user' => $user->username
                ]);
                
                return redirect()->route('caplining.index')
                    ->with('success', 'Persetujuan berhasil disimpan untuk kolom check: ' . implode(', ', $approvedColumns));
            } else {
                DB::rollBack();
                
                return redirect()->back()
                    ->with('warning', 'Tidak ada persetujuan yang dipilih.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error saat menyimpan persetujuan caplining: ' . $e->getMessage());
            Log::error('Hashid: ' . $hashid);
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan persetujuan: ' . $e->getMessage());
        }
    }

    public function reviewPdf($hashid) 
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model CapliningCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $capliningCheck = (new CapliningCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $capliningCheck->load([
            'checker1', 'checker2', 'checker3', 'checker4', 'checker5',
            'approver1', 'approver2', 'approver3', 'approver4', 'approver5'
        ]);
        
        // DEBUG: Log untuk memastikan relasi berhasil dimuat
        Log::info('Debug Relasi Checker/Approver:', [
            'hashid' => $hashid,
            'check_id' => $capliningCheck->id,
            'checker1_loaded' => $capliningCheck->checker1 ? 'Yes' : 'No',
            'checker1_name' => $capliningCheck->checker1 ? $capliningCheck->checker1->nama : 'NULL',
            'checker1_id' => $capliningCheck->checker_id1,
            'approver1_loaded' => $capliningCheck->approver1 ? 'Yes' : 'No',
            'approver1_name' => $capliningCheck->approver1 ? $capliningCheck->approver1->nama : 'NULL',
            'approver1_id' => $capliningCheck->approver_id1,
        ]);
        
        // Ambil data form terkait
        $form = Form::findOrFail(11); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk caplining
        $capliningResults = CapliningResult::where('check_id', $capliningCheck->id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Kelistrikan',
            2 => 'MCB',
            3 => 'PLC',
            4 => 'Power Supply',
            5 => 'Relay',
            6 => 'Selenoid',
            7 => 'Selang Angin',
            8 => 'Regulator',
            9 => 'Pir',
            10 => 'Motor',
            11 => 'Vanbelt',
            12 => 'Conveyor',
            13 => 'Motor Conveyor',
            14 => 'Vibrator',
            15 => 'Motor Vibrator',
            16 => 'Gear Box',
            17 => 'Rantai',
            18 => 'Stang Penggerak',
            19 => 'Suction Pad',
            20 => 'Sensor',
        ];
        
        // Mapping untuk variasi nama
        $itemMapping = [
            1 => ['Kelistrikan'],
            2 => ['MCB'],
            3 => ['PLC'],
            4 => ['Power Supply'],
            5 => ['Relay'],
            6 => ['Selenoid'],
            7 => ['Selang Angin'],
            8 => ['Regulator'],
            9 => ['Pir'],
            10 => ['Motor'],
            11 => ['Vanbelt'],
            12 => ['Conveyor'],
            13 => ['Motor Conveyor'],
            14 => ['Vibrator'],
            15 => ['Motor Vibrator'],
            16 => ['Gear Box'],
            17 => ['Rantai'],
            18 => ['Stang Penggerak'],
            19 => ['Suction Pad'],
            20 => ['Sensor'],
        ];
        
        // Siapkan data check dan keterangan untuk setiap check (1-5)
        for ($j = 1; $j <= 5; $j++) {
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari capliningResults
            foreach ($items as $i => $item) {
                $result = null;
                foreach ($itemMapping[$i] as $possibleName) {
                    $result = $capliningResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break;
                }
                
                ${'check_' . $j}[$i] = $result ? $result->{'check' . $j} : '';
                ${'keterangan_' . $j}[$i] = $result ? $result->{'keterangan' . $j} : '';
            }
            
            // Tambahkan array ke capliningCheck object
            $capliningCheck->{'check_' . $j} = ${'check_' . $j};
            $capliningCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Siapkan nama checker dan approver menggunakan relasi dari model
        $checkerNames = [];
        $approverNames = [];
        
        for ($j = 1; $j <= 5; $j++) {
            // Untuk Checker - gunakan relasi yang sudah ada
            $checker = $capliningCheck->{"checker{$j}"};
            $checkerNames[$j] = $checker && $checker->username ? $checker->username : '-';
            
            // Untuk Approver - gunakan relasi yang sudah ada
            $approver = $capliningCheck->{"approver{$j}"};
            $approverNames[$j] = $approver && $approver->username ? $approver->username : '-';
        }
        
        // Log untuk debugging
        Log::info('Data caplining untuk review PDF:', [
            'hashid' => $hashid,
            'check_id' => $capliningCheck->id,
            'nomer_caplining' => $capliningCheck->nomer_caplining,
            'total_items' => count($items),
            'checkers' => $checkerNames,
            'approvers' => $approverNames,
            'raw_checker_ids' => [
                'checker_id1' => $capliningCheck->checker_id1,
                'checker_id2' => $capliningCheck->checker_id2,
                'checker_id3' => $capliningCheck->checker_id3,
                'checker_id4' => $capliningCheck->checker_id4,
                'checker_id5' => $capliningCheck->checker_id5,
            ],
            'raw_approver_ids' => [
                'approver_id1' => $capliningCheck->approver_id1,
                'approver_id2' => $capliningCheck->approver_id2,
                'approver_id3' => $capliningCheck->approver_id3,
                'approver_id4' => $capliningCheck->approver_id4,
                'approver_id5' => $capliningCheck->approver_id5,
            ]
        ]);
        
        // Render view untuk preview PDF
        $view = view('caplining.review_pdf', [
            'capliningCheck' => $capliningCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items,
            'checkerNames' => $checkerNames,
            'approverNames' => $approverNames,
            'user' => $user,
            'currentGuard' => $currentGuard
        ]);
        
        return $view;
    }

    public function downloadPdf($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model CapliningCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $capliningCheck = (new CapliningCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $capliningCheck->load([
            'checker1', 'checker2', 'checker3', 'checker4', 'checker5',
            'approver1', 'approver2', 'approver3', 'approver4', 'approver5'
        ]);
        
        // DEBUG: Log untuk memastikan relasi berhasil dimuat
        Log::info('Debug Relasi Checker/Approver:', [
            'hashid' => $hashid,
            'check_id' => $capliningCheck->id,
            'checker1_loaded' => $capliningCheck->checker1 ? 'Yes' : 'No',
            'checker1_name' => $capliningCheck->checker1 ? $capliningCheck->checker1->nama : 'NULL',
            'checker1_id' => $capliningCheck->checker_id1,
            'approver1_loaded' => $capliningCheck->approver1 ? 'Yes' : 'No',
            'approver1_name' => $capliningCheck->approver1 ? $capliningCheck->approver1->nama : 'NULL',
            'approver1_id' => $capliningCheck->approver_id1,
        ]);
        
        // Ambil data form terkait
        $form = Form::findOrFail(11); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk caplining
        $capliningResults = CapliningResult::where('check_id', $capliningCheck->id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Kelistrikan',
            2 => 'MCB',
            3 => 'PLC',
            4 => 'Power Supply',
            5 => 'Relay',
            6 => 'Selenoid',
            7 => 'Selang Angin',
            8 => 'Regulator',
            9 => 'Pir',
            10 => 'Motor',
            11 => 'Vanbelt',
            12 => 'Conveyor',
            13 => 'Motor Conveyor',
            14 => 'Vibrator',
            15 => 'Motor Vibrator',
            16 => 'Gear Box',
            17 => 'Rantai',
            18 => 'Stang Penggerak',
            19 => 'Suction Pad',
            20 => 'Sensor',
        ];
        
        // Mapping untuk variasi nama
        $itemMapping = [
            1 => ['Kelistrikan'],
            2 => ['MCB'],
            3 => ['PLC'],
            4 => ['Power Supply'],
            5 => ['Relay'],
            6 => ['Selenoid'],
            7 => ['Selang Angin'],
            8 => ['Regulator'],
            9 => ['Pir'],
            10 => ['Motor'],
            11 => ['Vanbelt'],
            12 => ['Conveyor'],
            13 => ['Motor Conveyor'],
            14 => ['Vibrator'],
            15 => ['Motor Vibrator'],
            16 => ['Gear Box'],
            17 => ['Rantai'],
            18 => ['Stang Penggerak'],
            19 => ['Suction Pad'],
            20 => ['Sensor'],
        ];
        
        // Siapkan data check dan keterangan untuk setiap check (1-5)
        for ($j = 1; $j <= 5; $j++) {
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari capliningResults
            foreach ($items as $i => $item) {
                $result = null;
                foreach ($itemMapping[$i] as $possibleName) {
                    $result = $capliningResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break;
                }
                
                ${'check_' . $j}[$i] = $result ? $result->{'check' . $j} : '';
                ${'keterangan_' . $j}[$i] = $result ? $result->{'keterangan' . $j} : '';
            }
            
            // Tambahkan array ke capliningCheck object
            $capliningCheck->{'check_' . $j} = ${'check_' . $j};
            $capliningCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Siapkan nama checker dan approver menggunakan relasi dari model
        $checkerNames = [];
        $approverNames = [];
        
        for ($j = 1; $j <= 5; $j++) {
            // Untuk Checker - gunakan relasi yang sudah ada dengan username seperti di reviewPdf
            $checker = $capliningCheck->{"checker{$j}"};
            $checkerNames[$j] = $checker && $checker->username ? $checker->username : '-';
            
            // Untuk Approver - gunakan relasi yang sudah ada dengan username seperti di reviewPdf
            $approver = $capliningCheck->{"approver{$j}"};
            $approverNames[$j] = $approver && $approver->username ? $approver->username : '-';
        }
        
        // Log untuk debugging
        Log::info('Data caplining untuk download PDF:', [
            'hashid' => $hashid,
            'check_id' => $capliningCheck->id,
            'nomer_caplining' => $capliningCheck->nomer_caplining,
            'total_items' => count($items),
            'checkers' => $checkerNames,
            'approvers' => $approverNames,
            'raw_checker_ids' => [
                'checker_id1' => $capliningCheck->checker_id1,
                'checker_id2' => $capliningCheck->checker_id2,
                'checker_id3' => $capliningCheck->checker_id3,
                'checker_id4' => $capliningCheck->checker_id4,
                'checker_id5' => $capliningCheck->checker_id5,
            ],
            'raw_approver_ids' => [
                'approver_id1' => $capliningCheck->approver_id1,
                'approver_id2' => $capliningCheck->approver_id2,
                'approver_id3' => $capliningCheck->approver_id3,
                'approver_id4' => $capliningCheck->approver_id4,
                'approver_id5' => $capliningCheck->approver_id5,
            ]
        ]);
        
        // Dapatkan rentang tanggal menggunakan fungsi yang sudah ada
        $tanggalRange = $this->getFormattedTanggalRange($capliningCheck);
        
        // Generate nama file PDF
        $filename = 'Caplining_nomer_' . $capliningCheck->nomer_caplining;
        
        // Tambahkan rentang tanggal ke filename jika tersedia
        if ($tanggalRange) {
            $cleanTanggalRange = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $tanggalRange);
            $filename .= '_' . $cleanTanggalRange;
        }
        
        $filename .= '.pdf';
        
        // Render view sebagai HTML
        $html = view('caplining.review_pdf', [
            'capliningCheck' => $capliningCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items,
            'checkerNames' => $checkerNames,
            'approverNames' => $approverNames,
            'user' => $user,
            'currentGuard' => $currentGuard
        ])->render();
        
        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Atur ukuran dan orientasi halaman
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF
        $dompdf->render();
        
        // Download file PDF
        return $dompdf->stream($filename, [
            'Attachment' => false,
        ]);
    }
}