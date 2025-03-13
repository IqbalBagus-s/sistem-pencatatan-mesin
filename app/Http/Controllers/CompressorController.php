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

    public function create()
    {
        return view('compressor.create'); 
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string|max:20',
            'checked_by_shift1' => 'nullable|string|max:255',
            'checked_by_shift2' => 'nullable|string|max:255',
            'approved_by_shift1' => 'nullable|string|max:255',
            'approved_by_shift2' => 'nullable|string|max:255',
            'kompressor_on_kl' => 'nullable|integer',
            'kompressor_on_kh' => 'nullable|integer',
            'mesin_on' => 'nullable|integer',
            'mesin_off' => 'nullable|integer',
            'temperatur_shift1' => 'nullable|numeric',
            'temperatur_shift2' => 'nullable|numeric',
            'humidity_shift1' => 'nullable|numeric',
            'humidity_shift2' => 'nullable|numeric',
        ]);

        // Simpan data ke database
        $check = CompressorCheck::create([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'checked_by_shift1' => $request->checked_by_shift1,
            'checked_by_shift2' => $request->checked_by_shift2,
            'approved_by_shift1' => null, // Belum disetujui saat pertama kali dibuat
            'approved_by_shift2' => null, // Belum disetujui saat pertama kali dibuat
            'kompressor_on_kl' => $request->kompressor_on_kl,
            'kompressor_on_kh' => $request->kompressor_on_kh,
            'mesin_on' => $request->mesin_on,
            'mesin_off' => $request->mesin_off,
            'temperatur_shift1' => $request->temperatur_shift1,
            'tempetatur_shift2' => $request->tempertatur_shift2,
            'humidity_shift1' => $request->humidity_shift1,
            'humidity_shift2' => $request->humidity_shift2,
        ]);

        return redirect()->route('compressor.index')->with('success', 'Data berhasil ditambahkan!');
    }

}
