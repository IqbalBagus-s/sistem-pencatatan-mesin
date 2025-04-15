<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GilingCheck;
use App\Models\GilingResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GilingController extends Controller
{
    public function index(Request $request)
    {
        $query = GilingCheck::query();

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('checked_by', 'LIKE', $search)
                ->orWhere('approved_by1', 'LIKE', $search)
                ->orWhere('approved_by2', 'LIKE', $search);
            });
        }

        // Filter berdasarkan minggu
        if ($request->filled('minggu')) {
            $query->where('minggu', $request->minggu);
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

        return view('giling.index', compact('checks'));
    }

    public function create()
    {
        return view('giling.create');
    }

    public function store(Request $request)
    {
        // Validasi input dasar
        $request->validate([
            'minggu' => 'required|string',
            'bulan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Cek apakah kombinasi bulan dan minggu sudah ada di database
        $existingCheck = GilingCheck::where('bulan', $request->bulan)
                                ->where('minggu', $request->minggu)
                                ->first();
        
        if ($existingCheck) {
            return redirect()->back()->with('error', "Pencatatan tersebut sudah ada!")->withInput();
        }

        try {
            DB::beginTransaction();

            // Simpan data utama pemeriksaan dengan username dari user yang login
            $gilingCheck = GilingCheck::create([
                'bulan' => $request->bulan,
                'minggu' => $request->minggu,
                'checked_by' => Auth::user()->username, // Mengambil username user yang sedang login
                'keterangan' => $request->keterangan ?? '',
                // Perhatikan: approval akan diisi nanti melalui proses terpisah
            ]);
            
            // Item yang harus dicek berdasarkan form
            $checkedItems = [
                'cek_motor_mesin_giling' => 'Cek Motor Mesin Giling',
                'cek_vanbelt' => 'Cek Vanbelt',
                'cek_dustcollector' => 'Cek Dustcollector',
                'cek_safety_switch' => 'Cek Safety Switch',
                'cek_ketajaman_pisau_putar_dan_pisau_duduk' => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
            ];
            
            // Simpan hasil pemeriksaan untuk setiap item
            foreach ($checkedItems as $itemKey => $itemName) {
                // Ambil data dari request untuk item ini
                $itemData = $request->input($itemKey, []);
                
                // Buat record hasil dengan data dasar
                $resultData = [
                    'check_id' => $gilingCheck->id,
                    'checked_items' => $itemName,
                ];
                
                // Tambahkan status untuk setiap gilingan (G1-G10)
                for ($i = 1; $i <= 10; $i++) {
                    $key = "G{$i}";
                    $resultData["g{$i}"] = $itemData[$key] ?? '-';
                }
                
                // Simpan hasil ke database menggunakan model GilingResult yang baru
                GilingResult::create($resultData);
            }

            DB::commit();
            return redirect()->route('giling.index')->with('success', 'Data pemeriksaan mesin giling berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
