<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterChillerCheck;
use App\Models\WaterChillerResult;

class WaterChillerController extends Controller
{
    public function index(Request $request)
    {
        $query = WaterChillerCheck::query();

        // Filtering berdasarkan pencarian nama checker
        if ($request->has('search')) {
            $query->where('checked_by', 'like', '%' . $request->search . '%');
        }

        // Filtering berdasarkan bulan
        if ($request->has('bulan')) {
            $query->whereMonth('tanggal', date('m', strtotime($request->bulan)))
                  ->whereYear('tanggal', date('Y', strtotime($request->bulan)));
        }

        $checks = $query->paginate(10);

        return view('water_chiller.index', compact('checks'));
    }

    public function create()
    {
        return view('water_chiller.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'checked_by' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $check = WaterChillerCheck::create($request->all());

        return redirect()->route('water-chiller.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function show($id)
    {
        $check = WaterChillerCheck::with('results')->findOrFail($id);
        return view('water_chiller.show', compact('check'));
    }

    public function edit($id)
    {
        $check = WaterChillerCheck::findOrFail($id);
        return view('water_chiller.edit', compact('check'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'checked_by' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $check = WaterChillerCheck::findOrFail($id);
        $check->update($request->all());

        return redirect()->route('water-chiller.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($id)
    {
        WaterChillerCheck::destroy($id);
        return redirect()->route('water-chiller.index')->with('success', 'Data berhasil dihapus.');
    }
}
