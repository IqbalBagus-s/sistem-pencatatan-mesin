<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\CapliningCheck;
use App\Models\CapliningResult;

class CapliningController extends Controller
{
    public function index(Request $request)
    {
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
            
            // Status approval - cek semua kolom approved_by
            $approvers = collect([
                $check->approved_by1,
                $check->approved_by2,
                $check->approved_by3,
                $check->approved_by4,
                $check->approved_by5
            ])->filter()->values()->toArray();
            
            // Hitung jumlah checker yang ada (tidak kosong)
            $totalCheckers = collect([
                $check->checked_by1,
                $check->checked_by2,
                $check->checked_by3,
                $check->checked_by4,
                $check->checked_by5
            ])->filter()->count();
            
            // Tentukan status approval
            if (count($approvers) === 0) {
                $check->approvalStatus = 'not_approved'; // Belum Disetujui
            } elseif (count($approvers) < $totalCheckers) {
                $check->approvalStatus = 'partially_approved'; // Disetujui Sebagian
            } else {
                $check->approvalStatus = 'fully_approved'; // Disetujui
            }
            
            // Simpan juga daftar approver untuk penggunaan lain jika diperlukan
            $check->allApprovers = $approvers;
            
            // Menghitung dan menyimpan rentang tanggal
            $tanggalFormatted = $this->getFormattedTanggalRange($check);
            $check->hasTanggal = !is_null($tanggalFormatted);
            $check->tanggalFormatted = $tanggalFormatted;
        }
    
        return view('caplining.index', compact('checks'));
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
        return view('caplining.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nomer_caplining' => 'required|integer|between:1,6',
        ]);
    
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
    
        // Mulai transaksi database
        DB::beginTransaction();
    
        try {
            // Data utama untuk tabel caplining_checks
            $data = [
                'nomer_caplining' => $request->nomer_caplining,
            ];
            
            // Array untuk menyimpan tanggal yang diinput
            $tanggalChecks = [];
            
            // Set tanggal dan user data untuk masing-masing kolom check
            for ($i = 1; $i <= 5; $i++) {
                // Simpan tanggal yang dipilih
                if ($request->has("tanggal_$i") && !empty($request->{"tanggal_$i"})) {
                    $formattedDate = $formatTanggalForDB($request->{"tanggal_$i"});
                    $data["tanggal_check$i"] = $formattedDate;
                    
                    // Tambahkan ke array tanggal untuk validasi
                    if ($formattedDate) {
                        $tanggalChecks[] = $formattedDate;
                    }
                    
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
                            if (in_array($record->$dateField, $tanggalChecks)) {
                                // Format tanggal kembali ke format yang dimengerti user
                                if ($record->$dateField) {
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
                }
                
                // Jika ada tanggal yang sudah ada, kembalikan error
                if (!empty($existingDates)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['tanggal' => 'Data tersebut sudah ada!'])
                        ->with('error', 'Data tersebut sudah ada!');
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
        
        return view('caplining.edit', compact('check', 'groupedData', 'items'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'nomer_caplining' => 'required|integer|between:1,20',
        ]);
    
        // Cari data caplining yang akan diupdate
        $capCheck = CapliningCheck::findOrFail($id);
    
        // Mulai transaksi database
        DB::beginTransaction();
    
        try {
            // Kumpulkan tanggal-tanggal pemeriksaan dan pelaksana check
            $tanggalData = [];
            $checkedByData = [];
            $tanggalChecks = [];
            
            for ($i = 1; $i <= 5; $i++) {
                // Ambil tanggal dari request jika ada
                if ($request->has("tanggal_raw_$i") && !empty($request->input("tanggal_raw_$i"))) {
                    $tanggalData["tanggal_check$i"] = $request->input("tanggal_raw_$i");
                    $checkedByData["checked_by$i"] = $request->input("checked_by$i");
                    $tanggalChecks[] = $request->input("tanggal_raw_$i");
                } elseif ($request->has("tanggal_check$i")) {
                    // Tanggal yang sudah ada sebelumnya (tidak diubah)
                    $tanggalData["tanggal_check$i"] = $capCheck->{"tanggal_check$i"};
                    $checkedByData["checked_by$i"] = $request->input("checked_by$i");
                    if ($capCheck->{"tanggal_check$i"}) {
                        $tanggalChecks[] = $capCheck->{"tanggal_check$i"};
                    }
                } else {
                    // Tidak ada tanggal
                    $tanggalData["tanggal_check$i"] = null;
                    $checkedByData["checked_by$i"] = null;
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
                            if (in_array($record->$dateField, $tanggalChecks)) {
                                // Format tanggal kembali ke format yang dimengerti user
                                if ($record->$dateField) {
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
                }
                
                // Jika ada tanggal yang sudah ada, kembalikan error
                if (!empty($existingDates)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['tanggal' => 'Data tersebut sudah ada!'])
                        ->with('error', 'Data tersebut sudah ada!');
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
    
                // Update data untuk setiap kolom check (1-5)
                for ($i = 1; $i <= 5; $i++) {
                    $checkField = "check$i";
                    $keteranganField = "keterangan$i";
    
                    // Update hasil check jika ada input
                    if (isset($request->{"check_$i"}) && isset($request->{"check_$i"}[$itemId])) {
                        $itemRecord->$checkField = $request->{"check_$i"}[$itemId];
                    }
    
                    // Update keterangan jika ada input
                    if (isset($request->{"keterangan_$i"}) && isset($request->{"keterangan_$i"}[$itemId])) {
                        $itemRecord->$keteranganField = $request->{"keterangan_$i"}[$itemId];
                    }
                }
    
                // Simpan perubahan
                $itemRecord->save();
            }
    
            // Commit transaksi
            DB::commit();
    
            return redirect()->route('caplining.index')
                ->with('success', 'Data mesin caplining berhasil diperbarui!');
    
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
    
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
