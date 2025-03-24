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

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('checked_by_minggu1', 'LIKE', $search)
                ->orWhere('checked_by_minggu2', 'LIKE', $search)
                ->orWhere('checked_by_minggu3', 'LIKE', $search)
                ->orWhere('checked_by_minggu4', 'LIKE', $search);
            });
        }

        // Filter berdasarkan nomor hopper
        if ($request->filled('search_hopper')) {
            $query->where('nomer_hopper', $request->search_hopper); // Menggunakan filter exact match
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            try {
                $bulan = date('m', strtotime($request->bulan));
                $tahun = date('Y', strtotime($request->bulan));
                $query->where('bulan', $tahun . '-' . $bulan); // Sesuaikan dengan format penyimpanan di DB
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Format bulan tidak valid.');
            }
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('hopper.index', compact('checks'));
    }

    public function create()
    {
        return view('hopper.create');
    }

    public function store(Request $request)
    {
        // Cek apakah tanggal sudah ada di database
        $existingDate = HopperCheck::where('bulan', $request->bulan)->exists();
        
        if ($existingDate) {
            return back()->withErrors(['bulan' => 'bulan ini sudah digunakan! Pilih tanggal lain.']);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        $check = HopperCheck::create([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'checked_by' => Auth::user()->username,
            'approved_by' => null,
            'keterangan' => $request->keterangan,
        ]);

        for ($i = 1; $i <= 32; $i++) {
            HopperResult::create([
                'check_id' => $check->id,
                'no_mesin' => "CH{$i}",
                'Temperatur_Compressor' => $request->input("temperatur_1.{$i}") ?: null,
                'Temperatur_Kabel' => $request->input("temperatur_2.{$i}") ?: null,
                'Temperatur_Mcb' => $request->input("temperatur_3.{$i}") ?: null,
                'Temperatur_Air' => $request->input("temperatur_4.{$i}") ?: null,
                'Temperatur_Pompa' => $request->input("temperatur_5.{$i}") ?: null,
                'Evaporator' => $request->input("evaporator.{$i}") ?: null,
                'Fan_Evaporator' => $request->input("fan_evaporator.{$i}") ?: null,
                'Freon' => $request->input("freon.{$i}") ?: null,
                'Air' => $request->input("air.{$i}") ?: null,
            ]);
        }

        return redirect()->route('water-chiller.index')->with('success', 'Data berhasil disimpan');
    }
}
