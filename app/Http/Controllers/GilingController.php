<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GilingCheck;
use App\Models\GilingResultMinggu1;
use App\Models\GilingResultMinggu2;
use App\Models\GilingResultMinggu3;
use App\Models\GilingResultMinggu4;
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
}
