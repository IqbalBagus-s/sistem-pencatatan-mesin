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

        // Filter berdasarkan nama checker atau approver
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('checked_by', 'LIKE', $search)
                ->orWhere('approved_by', 'LIKE', $search);
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
        ],$customMessages);

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
                
                // Simpan checked_by jika ada
                if ($request->has("checked_by$i")) {
                    $data["checked_by$i"] = $request->{"checked_by$i"};
                } else {
                    $data["checked_by$i"] = null;
                }
                
                // Untuk approved_by, ini mungkin diisi secara terpisah
                if ($request->has("approved_by$i")) {
                    $data["approved_by$i"] = $request->{"approved_by$i"};
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
            
            Activity::logActivity(
                'checker',                                              // user_type
                $user->id,                                       // user_id
                $user->username,                                 // user_name
                'created',                                              // action
                'Checker ' . $user->username . ' membuat pemeriksaan Caplining Nomor ' . $request->nomer_caplining . ' untuk tanggal: ' . $tanggalString,  // description
                'caplining_check',                                      // target_type
                $capliningCheck->id,                                    // target_id
                [
                    'nomer_caplining' => $request->nomer_caplining,
                    'tanggal_checks' => $tanggalFormatted,
                    'total_items' => count($items),
                    'items_checked' => array_values($items),
                    'jumlah_tanggal_diinput' => count($tanggalList),
                    'status' => $capliningCheck->status ?? 'belum_disetujui'
                ]                                                       // details (JSON)
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

    public function edit($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        // Ambil data utama caplining check
        $check = CapliningCheck::findOrFail($id);
        
        // Ambil data hasil pemeriksaan
        $results = CapliningResult::where('check_id', $id)->get();
        
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
                    
                    if ($check->$tanggalField) {
                        $formattedData->push([
                            'check_number' => $i,
                            'item_id' => $itemId,
                            'item_name' => $itemName,
                            'tanggal' => $formatDisplayTanggal($check->$tanggalField),
                            'tanggal_raw' => $check->$tanggalField,
                            'result' => $itemResult->$checkField,
                            'keterangan' => $itemResult->$keteranganField,
                            'checked_by' => $check->{"checked_by$i"},
                            'approved_by' => $check->{"approved_by$i"} ?? ''
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan informasi untuk tanggal yang mungkin belum memiliki item spesifik
        for ($i = 1; $i <= 5; $i++) {
            $tanggalField = "tanggal_check$i";
            $checkedByField = "checked_by$i";
            $approvedByField = "approved_by$i";
            
            if ($check->$tanggalField && !$formattedData->where('check_number', $i)->count()) {
                $formattedData->push([
                    'check_number' => $i,
                    'tanggal' => $formatDisplayTanggal($check->$tanggalField),
                    'tanggal_raw' => $check->$tanggalField,
                    'checked_by' => $check->$checkedByField,
                    'approved_by' => $check->$approvedByField ?? ''
                ]);
            }
        }
        
        // Group data berdasarkan nomor check untuk memudahkan penggunaan di view
        $groupedData = $formattedData->groupBy('check_number');
        
        // Log untuk debugging
        Log::info('Data caplining untuk edit:', [
            'check_id' => $id,
            'nomer_caplining' => $check->nomer_caplining,
            'total_items' => $formattedData->count()
        ]);
        
        return view('caplining.edit', compact('check', 'groupedData', 'items', 'user', 'currentGuard'));
    }

    public function update(Request $request, $id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        // Validasi input
        $validated = $request->validate([
            'nomer_caplining' => 'required|integer|between:1,20',
        ]);

        // Cari data caplining yang akan diupdate
        $capCheck = CapliningCheck::findOrFail($id);

        // Fungsi format tanggal internal (harus sama seperti di fungsi store)
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

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Kumpulkan tanggal-tanggal pemeriksaan dan pelaksana check
            $tanggalData = [];
            $checkedByData = [];
            $tanggalChecks = [];
            
            // Perbaikan pada bagian pengumpulan data tanggal (sekitar baris 40-70)
            for ($i = 1; $i <= 5; $i++) {
                // Prioritaskan tanggal dari form input
                if ($request->has("tanggal_$i") && !empty($request->{"tanggal_$i"})) {
                    $formattedDate = $formatTanggalForDB($request->{"tanggal_$i"});
                    $tanggalData["tanggal_check$i"] = $formattedDate;
                    $checkedByData["checked_by$i"] = $request->{"checked_by$i"} ?? null;
                    if ($formattedDate) {
                        $tanggalChecks[] = $formattedDate;
                    }
                    
                    // Log untuk debugging
                    Log::info("Tanggal $i: " . $request->{"tanggal_$i"} . " -> " . $tanggalData["tanggal_check$i"]);
                } elseif ($request->has("tanggal_raw_$i") && !empty($request->{"tanggal_raw_$i"})) {
                    // Untuk tanggal yang tidak diubah (dari date picker)
                    $tanggalData["tanggal_check$i"] = $request->{"tanggal_raw_$i"};
                    $checkedByData["checked_by$i"] = $request->{"checked_by$i"} ?? null;
                    $tanggalChecks[] = $request->{"tanggal_raw_$i"};
                } else {
                    // PERBAIKAN: Tetap simpan data meskipun tanggal kosong
                    // Pertahankan data yang sudah ada di database atau set null
                    $existingDate = $capCheck->{"tanggal_check$i"};
                    $tanggalData["tanggal_check$i"] = $existingDate;
                    $checkedByData["checked_by$i"] = $request->{"checked_by$i"} ?? $capCheck->{"checked_by$i"};
                    
                    // Hanya masukkan ke array validasi jika ada tanggal
                    if ($existingDate) {
                        $tanggalChecks[] = $existingDate;
                    }
                }
            }

            // Validasi tanggal - cek apakah tanggal sudah ada di database untuk nomer_caplining yang sama
            $existingDates = [];
            if (!empty($tanggalChecks)) {
                // Query untuk mencari tanggal yang sudah ada di caplining lain
                $existingRecords = CapliningCheck::where('nomer_caplining', $request->nomer_caplining)
                    ->where('id', '!=', $id) // Kecualikan record yang sedang diedit
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
                    
                    // Membuat pesan error yang lebih eksplisit, seperti di fungsi store
                    $errorMsg = 'Data di tanggal ' . implode(', ', array_unique($existingDates)) . ' telah ada';
                    
                    // Rollback transaksi
                    DB::rollBack();
                    
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['tanggal_duplicate' => $errorMsg])
                        ->with('error', $errorMsg);
                }
            }

            // Update data utama CapliningCheck
            $capCheck->update(array_merge([
                'nomer_caplining' => $request->nomer_caplining,
            ], $tanggalData, $checkedByData));

            // Definisikan items yang diperiksa
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

            // Ambil data hasil pemeriksaan yang sudah ada
            $existingResults = CapliningResult::where('check_id', $id)
                ->get()
                ->keyBy('checked_items');

            // Proses setiap item untuk setiap kolom check (1-5)
            foreach ($items as $itemId => $itemName) {
                // Ambil atau buat record untuk item ini
                $itemRecord = $existingResults->get($itemName);
                
                if (!$itemRecord) {
                    $itemRecord = new CapliningResult([
                        'check_id' => $id,
                        'checked_items' => $itemName
                    ]);
                }

                // Flag untuk menentukan apakah record perlu disimpan
                $needsSave = false;

                // Update data untuk setiap kolom check (1-5)
                for ($i = 1; $i <= 5; $i++) {
                    $checkField = "check$i";
                    $keteranganField = "keterangan$i";

                    // Update hasil check jika ada input
                    if (isset($request->{"check_$i"}) && isset($request->{"check_$i"}[$itemId])) {
                        $newCheckValue = $request->{"check_$i"}[$itemId];
                        if ($itemRecord->$checkField !== $newCheckValue) {
                            $itemRecord->$checkField = $newCheckValue;
                            $needsSave = true;
                        }
                    }

                    // Update keterangan - PERBAIKAN: Proses semua input keterangan termasuk yang kosong
                    if (isset($request->{"keterangan_$i"}) && array_key_exists($itemId, $request->{"keterangan_$i"})) {
                        $newKeteranganValue = $request->{"keterangan_$i"}[$itemId] ?? ''; // Gunakan null coalescing untuk handle null
                        
                        // Trim whitespace untuk konsistensi
                        $newKeteranganValue = trim($newKeteranganValue);
                        
                        // Bandingkan dengan nilai lama, simpan jika berbeda (termasuk dari value ke kosong)
                        $oldKeteranganValue = trim($itemRecord->$keteranganField ?? '');
                        
                        if ($oldKeteranganValue !== $newKeteranganValue) {
                            $itemRecord->$keteranganField = $newKeteranganValue;
                            $needsSave = true;
                            
                            // Log untuk debugging
                            Log::info("Keterangan updated for item $itemId column $i: '$oldKeteranganValue' -> '$newKeteranganValue'");
                        }
                    }
                }

                // Simpan perubahan hanya jika ada perubahan
                if ($needsSave || !$itemRecord->exists) {
                    $itemRecord->save();
                }
            }

            // Commit transaksi
            DB::commit();

            return redirect()->route('caplining.index')
                ->with('success', 'Data mesin caplining berhasil diperbarui!');

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            // Log error detail untuk debugging
            Log::error('Error saat memperbarui data caplining: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

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
        // Ambil data utama caplining check
        $check = CapliningCheck::findOrFail($id);
        
        // Ambil data hasil pemeriksaan
        $resultsData = CapliningResult::where('check_id', $id)->get();
        
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
                        $results->push([
                            'tanggal_check' => $i,
                            'item_id' => $itemId,
                            'result' => $itemResult->$checkField ?? null,
                            'keterangan' => $itemResult->$keteranganField ?? '',
                            'checked_by' => $check->{"checked_by$i"},
                            'approved_by' => $check->{"approved_by$i"} ?? '',
                            'tanggal' => $formatDisplayTanggal($check->$tanggalField)
                        ]);
                    }
                }
            }
        }
        
        // Tambahkan informasi untuk tanggal yang mungkin belum memiliki item spesifik
        for ($i = 1; $i <= 5; $i++) {
            $tanggalField = "tanggal_check$i";
            $checkedByField = "checked_by$i";
            $approvedByField = "approved_by$i";
            
            if ($check->$tanggalField && !$results->where('tanggal_check', $i)->where('checked_by', '!=', null)->count()) {
                $results->push([
                    'tanggal_check' => $i,
                    'tanggal' => $formatDisplayTanggal($check->$tanggalField),
                    'checked_by' => $check->$checkedByField,
                    'approved_by' => $check->$approvedByField ?? ''
                ]);
            }
        }
        
        // Log untuk debugging
        Log::info('Data caplining untuk detail view:', [
            'check_id' => $id,
            'nomer_caplining' => $check->nomer_caplining,
            'total_items' => $results->count()
        ]);
        
        return view('caplining.show', compact('check', 'results', 'items', 'user', 'currentGuard'));
    }

    public function approve(Request $request, $id)
    {
        $user = $this->ensureAuthenticatedUser(['approver']);
        if (!is_object($user)) return $user;
        if (!$this->isAuthenticatedAs('approver')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
        }
        // Find the caplining check record
        $check = CapliningCheck::findOrFail($id);
        
        // Initialize data array for update
        $updateData = [];
        
        // Loop through potential approval fields
        for ($i = 1; $i <= 5; $i++) {
            // Check if this check number was submitted
            if ($request->has("approve_num_{$i}") && $request->filled("approved_by_{$i}")) {
                // Get the approved by value
                $approvedBy = $request->input("approved_by_{$i}");
                
                // Add to update data
                $updateData["approved_by{$i}"] = $approvedBy;
            }
        }
        
        // Update the record if there are any changes
        if (!empty($updateData)) {
            $check->update($updateData);
            
            return redirect()->route('caplining.index')
                ->with('success', 'Persetujuan berhasil disimpan.');
        }
        
        return redirect()->route('caplining.index')
            ->with('info', 'Tidak ada perubahan yang disimpan.');
    }

    public function reviewPdf($id) 
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        // Ambil data pemeriksaan caplining berdasarkan ID
        $capliningCheck = CapliningCheck::findOrFail($id);
        
        // Ambil data form terkait (sesuaikan nomor form untuk caplining)
        $form = Form::where('nomor_form', 'APTEK/016/REV.01')->firstOrFail(); // Sesuaikan nomor form
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk caplining
        $capliningResults = CapliningResult::where('check_id', $id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF (sama seperti di fungsi show)
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
        
        // Definisikan mapping untuk menangani kemungkinan variasi nama
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
        
        // Siapkan semua field check dan keterangan untuk lima check (sesuai dengan struktur caplining)
        for ($j = 1; $j <= 5; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per check
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari capliningResults menggunakan pendekatan yang lebih fleksibel
            foreach ($items as $i => $item) {
                $result = null;
                // Coba semua kemungkinan nama untuk item ini
                foreach ($itemMapping[$i] as $possibleName) {
                    $result = $capliningResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break; // Jika ditemukan, hentikan pencarian
                }
                
                ${'check_' . $j}[$i] = $result ? $result->{'check' . $j} : '';
                ${'keterangan_' . $j}[$i] = $result ? $result->{'keterangan' . $j} : '';
            }
            
            // Tambahkan array ke capliningCheck object
            $capliningCheck->{'check_' . $j} = ${'check_' . $j};
            $capliningCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Log untuk debugging
        Log::info('Data caplining untuk review PDF:', [
            'check_id' => $id,
            'nomer_caplining' => $capliningCheck->nomer_caplining,
            'total_items' => count($items)
        ]);
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('caplining.review_pdf', [
            'capliningCheck' => $capliningCheck,
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
        // Ambil data pemeriksaan caplining berdasarkan ID
        $capliningCheck = CapliningCheck::findOrFail($id);
        
        // Ambil data form terkait (sesuaikan nomor form untuk caplining)
        $form = Form::where('nomor_form', 'APTEK/016/REV.01')->firstOrFail(); // Sesuaikan nomor form
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk caplining
        $capliningResults = CapliningResult::where('check_id', $id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF (sama seperti di fungsi show)
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
        
        // Definisikan mapping untuk menangani kemungkinan variasi nama
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
        
        // Siapkan semua field check dan keterangan untuk lima check (sesuai dengan struktur caplining)
        for ($j = 1; $j <= 5; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per check
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari capliningResults menggunakan pendekatan yang lebih fleksibel
            foreach ($items as $i => $item) {
                $result = null;
                // Coba semua kemungkinan nama untuk item ini
                foreach ($itemMapping[$i] as $possibleName) {
                    $result = $capliningResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break; // Jika ditemukan, hentikan pencarian
                }
                
                ${'check_' . $j}[$i] = $result ? $result->{'check' . $j} : '';
                ${'keterangan_' . $j}[$i] = $result ? $result->{'keterangan' . $j} : '';
            }
            
            // Tambahkan array ke capliningCheck object
            $capliningCheck->{'check_' . $j} = ${'check_' . $j};
            $capliningCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Dapatkan rentang tanggal menggunakan fungsi yang sudah ada
        $tanggalRange = $this->getFormattedTanggalRange($capliningCheck);
        
        // Generate nama file PDF dengan nomor caplining dan rentang tanggal
        $filename = 'Caplining_nomer_' . $capliningCheck->nomer_caplining;
        
        // Tambahkan rentang tanggal ke filename jika tersedia
        if ($tanggalRange) {
            // Bersihkan karakter yang tidak valid untuk nama file
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