<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DehumMatrasCheck;
use App\Models\DehumMatrasDetail;
use App\Models\DehumMatrasResultsTable1;
use App\Models\DehumMatrasResultsTable2;
use App\Models\DehumMatrasResultsTable3;
use Illuminate\Support\Facades\DB;



class DehumMatrasController extends Controller
{
    public function index(Request $request)
    {
        $query = DehumMatrasCheck::query();

        // Filter berdasarkan checked_by atau approved_by jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('detail', function ($q) use ($search) {
                $q->where('checked_by', 'LIKE', $search)
                ->orWhere('approved_by', 'LIKE', $search);
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

        // Ambil data dengan paginasi
        $checks = $query->with('detail')->paginate(10)->appends($request->query());
        
        // Load informasi tambahan untuk setiap check
        foreach ($checks as $check) {
            // Get all unique checkers
            $check->allCheckers = DehumMatrasDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('checked_by')
                ->pluck('checked_by')
                ->unique()
                ->toArray();
                
            // Get year and month from bulan field
            $year = substr($check->bulan, 0, 4);
            $month = substr($check->bulan, 5, 2);
            
            // Calculate days in month
            $check->daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
            
            // Count checked dates
            $check->filledDatesCount = DehumMatrasDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('checked_by')
                ->count();
            
            // Count approved dates
            $check->approvedDatesCount = DehumMatrasDetail::where('tanggal_check_id', $check->id)
                ->whereNotNull('approved_by')
                ->count();
                
            // Hitung persentase kelengkapan hasil pengecekan
            if ($check->daysInMonth > 0) {
                $check->completionPercentage = round(($check->filledDatesCount / $check->daysInMonth) * 100, 2);
                $check->approvalPercentage = round(($check->approvedDatesCount / $check->daysInMonth) * 100, 2);
            } else {
                $check->completionPercentage = 0;
                $check->approvalPercentage = 0;
            }
        }

        return view('dehum-matras.index', compact('checks'));
    }

    public function create()
    {
        return view('dehum-matras.create');
    }
    
    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'nomer_dehum_matras' => 'required|integer|between:1,23',
            'shift' => 'required|integer|between:1,3',
            'bulan' => 'required|date_format:Y-m',
        ]);
    
        // Check for duplicate record
        $existingRecord = DehumMatrasCheck::where('nomer_dehum_matras', $request->nomer_dehum_matras)
            ->where('shift', $request->shift)
            ->where('bulan', $request->bulan)
            ->first();
        
        if ($existingRecord) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data tersebut sudah ada!');
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
                        $resultData1["tanggal{$j}"] = '-'; // Default value
                    }
                }
                
                // Process checks for Table 2 (days 12-22)
                for ($j = 12; $j <= 22; $j++) {
                    $checkKey = "check_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData2["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData2["tanggal{$j}"] = '-'; // Default value
                    }
                }
                
                // Process checks for Table 3 (days 23-31)
                for ($j = 23; $j <= 31; $j++) {
                    $checkKey = "check_{$j}";
                    
                    if (isset($request->$checkKey) && isset($request->$checkKey[$itemId])) {
                        $resultData3["tanggal{$j}"] = $request->$checkKey[$itemId];
                    } else {
                        $resultData3["tanggal{$j}"] = '-'; // Default value
                    }
                }
                
                // Create the result records for all tables
                DehumMatrasResultsTable1::create($resultData1);
                DehumMatrasResultsTable2::create($resultData2);
                DehumMatrasResultsTable3::create($resultData3);
            }
            
            // Process checked_by information for all days (1-31)
            for ($i = 1; $i <= 31; $i++) {
                $checkedByKey = "checked_by_{$i}";
                
                if ($request->has($checkedByKey) && !empty($request->$checkedByKey)) {
                    DehumMatrasDetail::create([
                        'tanggal_check_id' => $checkId,
                        'tanggal' => $i, // Using the column number as the day
                        'checked_by' => $request->$checkedByKey,
                        'approved_by' => null, // Approval would be handled separately
                    ]);
                }
            }
            
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
}
