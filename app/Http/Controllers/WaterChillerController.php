<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterChillerCheck;
use App\Models\WaterChillerResult;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF

class WaterChillerController extends Controller
{
    public function index(Request $request)
    {
        $query = WaterChillerCheck::orderBy('tanggal', 'desc');

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
            'hari' => 'required|string|max:20',
        ]);

        $check = WaterChillerCheck::create([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'checked_by' => Auth::user()->username,
            'approved_by' => null,
        ]);

        // Simpan data per mesin
        foreach ($request->checked_items as $index => $checked_item) {
            WaterChillerResult::create([
                'check_id' => $check->id,
                'checked_items' => $checked_item,
                'CH1' => $request->CH1[$index] ?? null,
                'CH2' => $request->CH2[$index] ?? null,
                'CH3' => $request->CH3[$index] ?? null,
                'CH4' => $request->CH4[$index] ?? null,
                'CH5' => $request->CH5[$index] ?? null,
                'CH6' => $request->CH6[$index] ?? null,
                'CH7' => $request->CH7[$index] ?? null,
                'CH8' => $request->CH8[$index] ?? null,
                'CH9' => $request->CH9[$index] ?? null,
                'CH10' => $request->CH10[$index] ?? null,
                'CH11' => $request->CH11[$index] ?? null,
                'CH12' => $request->CH12[$index] ?? null,
                'CH13' => $request->CH13[$index] ?? null,
                'CH14' => $request->CH14[$index] ?? null,
                'CH15' => $request->CH15[$index] ?? null,
                'CH16' => $request->CH16[$index] ?? null,
                'CH17' => $request->CH17[$index] ?? null,
                'CH18' => $request->CH18[$index] ?? null,
                'CH19' => $request->CH19[$index] ?? null,
                'CH20' => $request->CH20[$index] ?? null,
                'CH21' => $request->CH21[$index] ?? null,
                'CH22' => $request->CH22[$index] ?? null,
                'CH23' => $request->CH23[$index] ?? null,
                'CH24' => $request->CH24[$index] ?? null,
                'CH25' => $request->CH25[$index] ?? null,
                'CH26' => $request->CH26[$index] ?? null,
                'CH27' => $request->CH27[$index] ?? null,
                'CH28' => $request->CH28[$index] ?? null,
                'CH29' => $request->CH29[$index] ?? null,
                'CH30' => $request->CH30[$index] ?? null,
                'CH31' => $request->CH31[$index] ?? null,
                'CH32' => $request->CH32[$index] ?? null,
            ]);
        }

        return redirect()->route('water-chiller.index')->with('success', 'Data berhasil disimpan!');
    }

    public function edit($check_id)
    {
        $check = WaterChillerCheck::findOrFail($check_id);
        $results = WaterChillerResult::where('check_id', $check_id)->get();
        return view('water_chiller.edit', compact('check', 'results'));
    }

    public function update(Request $request, $check_id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string|max:20',
        ]);

        // Update WaterChillerCheck
        $check = WaterChillerCheck::findOrFail($check_id);
        $check->update([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
        ]);

        // Update WaterChillerResult
        foreach ($request->checked_items as $index => $checked_item) {
            $result = WaterChillerResult::where('check_id', $check_id)
                ->where('checked_items', $checked_item)
                ->first();

            if ($result) {
                $result->update([
                    'CH1' => $request->CH1[$index] ?? null,
                    'CH2' => $request->CH2[$index] ?? null,
                    'CH3' => $request->CH3[$index] ?? null,
                    'CH4' => $request->CH4[$index] ?? null,
                    'CH5' => $request->CH5[$index] ?? null,
                    'CH6' => $request->CH6[$index] ?? null,
                    'CH7' => $request->CH7[$index] ?? null,
                    'CH8' => $request->CH8[$index] ?? null,
                    'CH9' => $request->CH9[$index] ?? null,
                    'CH10' => $request->CH10[$index] ?? null,
                    'CH11' => $request->CH11[$index] ?? null,
                    'CH12' => $request->CH12[$index] ?? null,
                    'CH13' => $request->CH13[$index] ?? null,
                    'CH14' => $request->CH14[$index] ?? null,
                    'CH15' => $request->CH15[$index] ?? null,
                    'CH16' => $request->CH16[$index] ?? null,
                    'CH17' => $request->CH17[$index] ?? null,
                    'CH18' => $request->CH18[$index] ?? null,
                    'CH19' => $request->CH19[$index] ?? null,
                    'CH20' => $request->CH20[$index] ?? null,
                    'CH21' => $request->CH21[$index] ?? null,
                    'CH22' => $request->CH22[$index] ?? null,
                    'CH23' => $request->CH23[$index] ?? null,
                    'CH24' => $request->CH24[$index] ?? null,
                    'CH25' => $request->CH25[$index] ?? null,
                    'CH26' => $request->CH26[$index] ?? null,
                    'CH27' => $request->CH27[$index] ?? null,
                    'CH28' => $request->CH28[$index] ?? null,
                    'CH29' => $request->CH29[$index] ?? null,
                    'CH30' => $request->CH30[$index] ?? null,
                    'CH31' => $request->CH31[$index] ?? null,
                    'CH32' => $request->CH32[$index] ?? null,
                ]);
            }
        }

        return redirect()->route('water-chiller.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function show($check_id)
    {
        $check = WaterChillerCheck::findOrFail($check_id);
        $results = WaterChillerResult::where('check_id', $check_id)->get();
        
        return view('water_chiller.show', compact('check', 'results'));
    }

    public function approve(Request $request, $check_id)
    {
        $check = WaterChillerCheck::findOrFail($check_id);
        
        // Update approved_by field dengan username approver yang login
        $check->update([
            'approved_by' => Auth::user()->username
        ]);
        
        return redirect()->route('water-chiller.index')
            ->with('success', 'Data berhasil disetujui!');
    }

    public function downloadPdf($id)
    {
        // Ambil data dari database berdasarkan ID
        $check = WaterChillerCheck::findOrFail($id);
        $results = WaterChillerResult::where('check_id', $id)->get();

        // Load view untuk PDF dengan ukuran halaman yang sesuai
        $pdf = Pdf::loadView('water_chiller.pdf', compact('check', 'results'))
            ->setPaper('a4', 'landscape'); // Set ukuran kertas A4 landscape

        // Mengembalikan file PDF untuk di-download
        return $pdf->download('water_chiller_' . $id . '.pdf');
    }
}