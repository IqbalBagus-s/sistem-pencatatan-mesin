<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DehumBahanCheck;
use App\Models\DehumBahanResult;
use Illuminate\Support\Facades\DB;

class DehumBahanController extends Controller
{
    public function index(Request $request)
    {
        $query = DehumBahanCheck::query();

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

        // Filter berdasarkan nomor dehum bahan
        if ($request->filled('search_dehum')) {
            $query->where('nomer_dehum', $request->search_dehum); // Menggunakan filter exact match
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

        return view('dehum-bahan.index', compact('checks'));
    }

    public function create()
    {
        return view('dehum-bahan.create');
    }
}
