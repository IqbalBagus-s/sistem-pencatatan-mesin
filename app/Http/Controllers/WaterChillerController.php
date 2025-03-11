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
            'keterangan' => 'nullable|string',
        ]);

        // Simpan data ke dalam tabel WaterChillerCheck
        $check = WaterChillerCheck::create([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'checked_by' => Auth::user()->username,
            'approved_by' => null,
            'keterangan' => $request->keterangan,
        ]);

        // Simpan data per mesin ke dalam tabel WaterChillerResult
        foreach ($request->checked_items as $index => $checked_item) {
            $data = [
                'check_id' => $check->id,
                'checked_items' => $checked_item,
            ];

            for ($j = 1; $j <= 32; $j++) {
                $key = "CH{$j}";
                $value = $request->{$key}[$index] ?? "➖"; // Default ke strip jika kosong

                switch ($checked_item) {
                    case 'Evaporator':
                        $data[$key] = ($value === '✔️') ? "✔️" : (($value === '❌') ? "❌" : "-");
                        break;
                    case 'Fan Evaporator':
                        $data[$key] = ($value === '✔️') ? "✔️" : (($value === '❌') ? "❌" : "-");
                        break;
                    case 'Freon':
                        $data[$key] = ($value === '✔️') ? "✔️" : (($value === '❌') ? "❌" : "-");
                        break;
                    case 'Air':
                        $data[$key] = ($value === '✔️') ? "✔️" : (($value === '❌') ? "❌" : "-");
                        break;
                    default:
                        $data[$key] = $value; // Menyimpan langsung nilai ✔️, ❌, atau ➖
                        break;
                }
            }

            WaterChillerResult::create($data);
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
            'keterangan' => 'nullable|string',
        ]);

        // Update WaterChillerCheck
        $check = WaterChillerCheck::findOrFail($check_id);
        $check->update([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'keterangan' => $request->keterangan,
        ]);

        // Update WaterChillerResult
        foreach ($request->checked_items as $index => $checked_item) {
            $result = WaterChillerResult::where('check_id', $check_id)
                ->where('checked_items', $checked_item)
                ->first();

            if ($result) {
                for ($j = 1; $j <= 32; $j++) {
                    $key = "CH{$j}";
                    $result->$key = $request->{$key}[$index] ?? "-";
                }
            $result->save();
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