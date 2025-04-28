<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\VacumCleanerCheck;
use App\Models\VacumCleanerResultsTable1;
use App\Models\VacumCleanerResultsTable2;

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
    
        // Ambil data dengan paginasi
        $checks = $query->paginate(10)->appends($request->query());
        
        // Load semua data tambahan untuk setiap check
        foreach ($checks as $check) {
            // Dapatkan semua checker unik
            $check->allCheckers = collect([$check->checker_minggu1, $check->checker_minggu2])
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
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data tersebut sudah ada!');
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Data utama untuk tabel vacuum cleaner checks
            $data = [
                'nomer_vacum_cleaner' => $request->nomer_vacuum_cleaner,
                'bulan' => $request->bulan,
                'tanggal_dibuat' => now(),
                'checker_minggu1' => null,
                'checker_minggu2' => null,
                'approver_minggu1' => null,
                'approver_minggu2' => null,
            ];
            
            // Set checker berdasarkan form data
            if ($request->has('check_num_1') && $request->check_num_1 == '1') {
                $data['checker_minggu1'] = $request->checked_by_1;
            }
            
            if ($request->has('check_num_2') && $request->check_num_2 == '2') {
                $data['checker_minggu2'] = $request->checked_by_2;
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
}
