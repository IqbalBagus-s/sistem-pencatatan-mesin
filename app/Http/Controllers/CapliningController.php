<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            'nomer_caplining' => 'required|integer',
            'tanggal_check1' => 'nullable|date_format:Y-m-d',
            'tanggal_check2' => 'nullable|date_format:Y-m-d',
            'tanggal_check3' => 'nullable|date_format:Y-m-d',
            'tanggal_check4' => 'nullable|date_format:Y-m-d',
            'tanggal_check5' => 'nullable|date_format:Y-m-d',
            'checked_by' => 'required|string',
            'approved_by' => 'required|string',
        ]);

        // Debug: Cek data yang diterima dari form
        Log::info('Data dari form caplining:', $request->all());

        // Periksa apakah data sudah ada
        $existingRecord = CapliningCheck::where('nomer_caplining', $request->nomer_caplining)
            ->first();
        
        if ($existingRecord) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data dengan nomor caplining tersebut sudah ada!');
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Data utama untuk tabel caplining checks
            $data = [
                'nomer_caplining' => $request->nomer_caplining,
                'tanggal_check1' => $request->tanggal_check1,
                'tanggal_check2' => $request->tanggal_check2,
                'tanggal_check3' => $request->tanggal_check3,
                'tanggal_check4' => $request->tanggal_check4,
                'tanggal_check5' => $request->tanggal_check5,
                'checked_by' => $request->checked_by,
                'approved_by' => $request->approved_by,
            ];
            
            // Buat record CapliningCheck
            $capliningCheck = CapliningCheck::create($data);
            
            // Log untuk memastikan record berhasil dibuat
            Log::info('Record caplining check dibuat dengan ID: ' . $capliningCheck->id);
            
            // Ambil ID dari record yang baru dibuat
            $checkId = $capliningCheck->id;
            
            // Definisikan item yang diperiksa
            $items = [
                1 => 'Mesin Pouring System',
                2 => 'Die & Punch Cylinder',
                3 => 'Tabang Silicon',
                4 => 'Conveyor',
                5 => 'Motor',
                6 => 'Sensor',
                7 => 'Selang',
                8 => 'Kabel',
                // Tambahkan item lain sesuai kebutuhan
            ];
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Data untuk hasil pemeriksaan
                $resultData = [
                    'check_id' => $checkId,
                    'checked_items' => $itemName,
                ];
                
                // Set nilai check untuk setiap periode pemeriksaan (1-5)
                for ($i = 1; $i <= 5; $i++) {
                    $checkField = "check{$i}";
                    $keteranganField = "keterangan{$i}";
                    
                    // Set nilai check
                    if (isset($request->{$checkField}[$itemId])) {
                        $resultData[$checkField] = $request->{$checkField}[$itemId];
                    } else {
                        $resultData[$checkField] = '-';
                    }
                    
                    // Set keterangan
                    if (isset($request->{$keteranganField}[$itemId])) {
                        $resultData[$keteranganField] = $request->{$keteranganField}[$itemId];
                    } else {
                        $resultData[$keteranganField] = null;
                    }
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
}
