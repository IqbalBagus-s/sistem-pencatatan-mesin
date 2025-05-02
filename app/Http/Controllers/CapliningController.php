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
    
        // Ambil data dengan paginasi dan eager load hasil pemeriksaan
        $checks = $query->with('results')->paginate(10)->appends($request->query());
        
        // Load semua data tambahan untuk setiap check
        foreach ($checks as $check) {
            // Dapatkan informasi checker dan approver
            $check->allCheckers = collect([$check->checked_by])
                ->filter()
                ->unique()
                ->values()
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
            
            // Status approval
            $check->isApproved = !empty($check->approved_by);
        }
    
        return view('caplining.index', compact('checks'));
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
    
        // Mulai transaksi database
        DB::beginTransaction();
    
        try {
            // Data utama untuk tabel caplining_checks
            $data = [
                'nomer_caplining' => $request->nomer_caplining,
            ];
            
            // Set tanggal dan user data untuk masing-masing kolom check
            for ($i = 1; $i <= 5; $i++) {
                // Simpan tanggal yang dipilih
                if ($request->has("tanggal_$i") && !empty($request->{"tanggal_$i"})) {
                    $data["tanggal_check$i"] = $this->formatTanggalForDB($request->{"tanggal_$i"});
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
    
    private function formatTanggalForDB($tanggal)
    {
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
    }
}
