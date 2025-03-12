<?php

namespace App\Http\Controllers;
use App\Models\CompressorCheck;
use App\Models\CompressorResult;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF


use Illuminate\Http\Request;

class CompressorController extends Controller
{
    public function index(Request $request)
    {
        $query = CompressorCheck::orderBy('tanggal', 'desc');

        // Filter berdasarkan peran user (Checker hanya bisa melihat data sendiri)
        if (Auth::user() instanceof \App\Models\Checker) {
            $query->where('checked_by_shift1', Auth::user()->username)
                  ->orWhere('checked_by_shift2', Auth::user()->username);
        }

        // Filter berdasarkan bulan jika ada
        if ($request->filled('bulan')) {
            $bulan = date('m', strtotime($request->bulan));
            $tahun = date('Y', strtotime($request->bulan));
            $query->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', $tahun);
        }

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('checked_by_shift1', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('checked_by_shift2', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('compressor.index', compact('checks'));
    }
}
