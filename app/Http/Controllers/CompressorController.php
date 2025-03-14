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
        // Validasi data yang diterima dari form
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'checked_by_shift1' => 'nullable|string',
            'checked_by_shift2' => 'nullable|string',
            'kompressor_on_kl' => 'nullable|string',
            'kompressor_on_kh' => 'nullable|string',
            'mesin_on' => 'nullable|string',
            'mesin_off' => 'nullable|string',
            'temperatur_shift1' => 'nullable|string',
            'temperatur_shift2' => 'nullable|string',
            'humidity_shift1' => 'nullable|string',
            'humidity_shift2' => 'nullable|string',
        ]);

        // Simpan data ke tabel compressor_checks
        $compressorCheck = CompressorCheck::create([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'checked_by_shift1' => $request->checked_by_shift1,
            'checked_by_shift2' => $request->checked_by_shift2,
            'kompressor_on_kl' => $request->kompressor_on_kl,
            'kompressor_on_kh' => $request->kompressor_on_kh,
            'mesin_on' => $request->mesin_on,
            'mesin_off' => $request->mesin_off,
            'temperatur_shift1' => $request->temperatur_shift1,
            'temperatur_shift2' => $request->temperatur_shift2,
            'humidity_shift1' => $request->humidity_shift1,
            'humidity_shift2' => $request->humidity_shift2,
        ]);

        // Simpan data hasil pemeriksaan Low Kompressor ke tabel compressor_results
        $lowCheckedItems = [
            "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
            "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
            "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
            "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
        ];

        // Untuk Low Kompressor
        foreach ($lowCheckedItems as $index => $item) {
            CompressorResult::create([
                'check_id' => $compressorCheck->id,
                'checked_items' => $item,
                'kl_10I' => $request->input("kl_KL_10I")[$index] ?? null,
                'kl_10II' => $request->input("kl_KL_10II")[$index] ?? null,
                'kl_5I' => $request->input("kl_KL_5I")[$index] ?? null,
                'kl_5II' => $request->input("kl_KL_5II")[$index] ?? null,
                'kl_6I' => $request->input("kl_KL_6I")[$index] ?? null,
                'kl_6II' => $request->input("kl_KL_6II")[$index] ?? null,
                'kl_7I' => $request->input("kl_KL_7I")[$index] ?? null,
                'kl_7II' => $request->input("kl_KL_7II")[$index] ?? null,
                'kl_8I' => $request->input("kl_KL_8I")[$index] ?? null,
                'kl_8II' => $request->input("kl_KL_8II")[$index] ?? null,
                'kl_9I' => $request->input("kl_KL_9I")[$index] ?? null,
                'kl_9II' => $request->input("kl_KL_9II")[$index] ?? null
            ]);
        }

        // Simpan data hasil pemeriksaan High Kompressor ke tabel compressor_results
        $highCheckedItems = [
            "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
            "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
            "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
            "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
        ];

        // Untuk High Kompressor
        foreach ($highCheckedItems as $index => $item) {
            CompressorResult::create([
                'check_id' => $compressorCheck->id,
                'checked_items' => $item,
                'kh_7I' => $request->input("kh_KH_7I")[$index] ?? null,
                'kh_7II' => $request->input("kh_KH_7II")[$index] ?? null,
                'kh_8I' => $request->input("kh_KH_8I")[$index] ?? null,
                'kh_8II' => $request->input("kh_KH_8II")[$index] ?? null,
                'kh_9I' => $request->input("kh_KH_9I")[$index] ?? null,
                'kh_9II' => $request->input("kh_KH_9II")[$index] ?? null,
                'kh_10I' => $request->input("kh_KH_10I")[$index] ?? null,
                'kh_10II' => $request->input("kh_KH_10II")[$index] ?? null,
                'kh_11I' => $request->input("kh_KH_11I")[$index] ?? null,
                'kh_11II' => $request->input("kh_KH_11II")[$index] ?? null
            ]);
        }

        // Uncomment kode ini untuk debugging
        // dd([
        //     'sample_kl' => $request->input("kl_KL_10I"),
        //     'sample_kh' => $request->input("kh_KH_7I"),
        //     'all_request' => $request->all()
        // ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('compressor.index')->with('success', 'Data berhasil disimpan!');
    }
}

