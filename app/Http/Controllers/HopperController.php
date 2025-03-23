<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HopperCheck;
use App\Models\HopperResult;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF

class HopperController extends Controller
{
    public function index(Request $request)
    {
        $query = HopperCheck::query();

        // Filter berdasarkan nama checker (jika ada)
        if ($request->filled('search_checker')) {
            $query->where('checked_by', 'LIKE', '%' . $request->search_checker . '%');
        }

        // Filter berdasarkan nomor hopper (jika ada)
        if ($request->filled('search_hopper')) {
            $query->where('nomer_hopper', 'LIKE', '%' . $request->search_hopper . '%');
        }

        // Filter berdasarkan bulan (jika ada)
        if ($request->filled('bulan')) {
            $bulan = date('m', strtotime($request->bulan));
            $tahun = date('Y', strtotime($request->bulan));
            $query->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun);
        }

        // Ambil data dengan pagination
        $checks = $query->orderBy('tanggal', 'desc')->paginate(10);

        return view('hopper.index', compact('checks'));
    }
}
