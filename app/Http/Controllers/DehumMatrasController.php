<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DehumMatrasCheck;
use App\Models\DehumMatrasDetail;
use App\Models\DehumMatrasResultsTable1;
use App\Models\DehumMatrasResultsTable2;
use App\Models\DehumMatrasResultsTable3;


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
}
