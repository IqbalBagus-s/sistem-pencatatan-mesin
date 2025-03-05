<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AirDryerCheck;
use App\Models\AirDryerResult;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF

class AirDryerController extends Controller
{
    public function index(Request $request)
    {
        $query = AirDryerCheck::orderBy('tanggal', 'desc');

        // Filter berdasarkan peran user (Checker hanya bisa melihat data sendiri)
        if (Auth::user() instanceof \App\Models\Checker) {
            $query->where('checked_by', Auth::user()->username);
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
            $query->where('checked_by', 'LIKE', '%' . $request->search . '%');
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('air_dryer.index', compact('checks'));
    }


    public function create()
    {
        return view('air_dryer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string|max:20',
        ]);
    
        $check = AirDryerCheck::create([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'checked_by' => Auth::user()->username,
            'approved_by' => null,
        ]);

        // Simpan data per mesin
        foreach ($request->nomor_mesin as $index => $nomor_mesin) {
            AirDryerResult::create([
                'check_id' => $check->id,
                'nomor_mesin' => $nomor_mesin,
                'temperatur_kompresor' => $request->temperatur_kompresor[$index] ?? null,
                'temperatur_kabel' => $request->temperatur_kabel[$index] ?? null,
                'temperatur_mcb' => $request->temperatur_mcb[$index] ?? null,
                'temperatur_angin_in' => $request->temperatur_angin_in[$index] ?? null,
                'temperatur_angin_out' => $request->temperatur_angin_out[$index] ?? null,
                'evaporator' => $request->evaporator[$index] ?? null,
                'fan_evaporator' => $request->fan_evaporator[$index] ?? null,
                'auto_drain' => $request->auto_drain[$index] ?? null,
                'keterangan' => $request->keterangan[$index] ?? null,
            ]);
        }

        return redirect()->route('air-dryer.index')->with('success', 'Data berhasil disimpan!');
    }

    public function edit($check_id)
    {
        $check = AirDryerCheck::findOrFail($check_id);
        $results = AirDryerResult::where('check_id', $check_id)->get();
        return view('air_dryer.edit', compact('check', 'results'));
    }

    public function update(Request $request, $check_id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string|max:20',
        ]);

        // Update AirDryerCheck
        $check = AirDryerCheck::findOrFail($check_id);
        $check->update([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
        ]);

        // Update AirDryerResult
        foreach ($request->nomor_mesin as $index => $nomor_mesin) {
            $result = AirDryerResult::where('check_id', $check_id)
                ->where('nomor_mesin', $nomor_mesin)
                ->first();

            if ($result) {
                $result->update([
                    'temperatur_kompresor' => $request->temperatur_kompresor[$index] ?? null,
                    'temperatur_kabel' => $request->temperatur_kabel[$index] ?? null,
                    'temperatur_mcb' => $request->temperatur_mcb[$index] ?? null,
                    'temperatur_angin_in' => $request->temperatur_angin_in[$index] ?? null,
                    'temperatur_angin_out' => $request->temperatur_angin_out[$index] ?? null,
                    'evaporator' => $request->evaporator[$index] ?? null,
                    'fan_evaporator' => $request->fan_evaporator[$index] ?? null,
                    'auto_drain' => $request->auto_drain[$index] ?? null,
                    'keterangan' => $request->keterangan[$index] ?? null,
                ]);
            }
        }

        return redirect()->route('air-dryer.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function show($check_id)
    {
        $check = AirDryerCheck::findOrFail($check_id);
        $results = AirDryerResult::where('check_id', $check_id)->get();
        
        return view('air_dryer.show', compact('check', 'results'));
    }

    public function approve(Request $request, $check_id)
    {
        $check = AirDryerCheck::findOrFail($check_id);
        
        // Update approved_by field dengan username approver yang login
        $check->update([
            'approved_by' => Auth::user()->username
        ]);
        
        return redirect()->route('air-dryer.index')
            ->with('success', 'Data berhasil disetujui!');
    }

    public function downloadPdf($id)
    {
        // Ambil data dari database berdasarkan ID
        $check = AirDryerCheck::findOrFail($id);
        $results = AirDryerResult::where('check_id', $id)->get();

        // Load view untuk PDF dengan ukuran halaman yang sesuai
        $pdf = Pdf::loadView('air_dryer.pdf', compact('check', 'results'))
            ->setPaper('a4', 'landscape'); // Set ukuran kertas A4 landscape

        // Mengembalikan file PDF untuk di-download
        return $pdf->download('air_dryer_' . $id . '.pdf');
    }



}
