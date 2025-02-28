<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AirDryerCheck;
use App\Models\AirDryerResult;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Tambahkan Carbon untuk manipulasi tanggal

class AirDryerController extends Controller
{
    public function index()
    {
        $checks = AirDryerCheck::where('checked_by', Auth::user()->username)->get();
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
    
}
