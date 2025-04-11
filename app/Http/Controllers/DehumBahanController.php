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

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'nomer_dehum' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
            
            // Validation for creator fields
            'created_by_1' => 'nullable|string|max:255',
            'created_date_1' => 'nullable|date',
            'created_by_2' => 'nullable|string|max:255',
            'created_date_2' => 'nullable|date',
            'created_by_3' => 'nullable|string|max:255',
            'created_date_3' => 'nullable|date',
            'created_by_4' => 'nullable|string|max:255',
            'created_date_4' => 'nullable|date',
            
            // Validation for checked items and checks
            'checked_items' => 'required|array',
            'check_1' => 'required|array',
            'keterangan_1' => 'nullable|array',
            'check_2' => 'nullable|array',
            'keterangan_2' => 'nullable|array',
            'check_3' => 'nullable|array',
            'keterangan_3' => 'nullable|array',
            'check_4' => 'nullable|array',
            'keterangan_4' => 'nullable|array',
        ]);

        // Check for existing record with the same nomer_dehum and bulan
        $existingRecord = DehumBahanCheck::where('nomer_dehum_bahan', $request->input('nomer_dehum'))
            ->where('bulan', $request->input('bulan'))
            ->first();

        if ($existingRecord) {
            // If a record with the same Dehum bahan number and month exists, return an error
            return redirect()->back()->with('error', 'Data sudah ada, silahkan buat ulang')
                            ->withInput();
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create dehumBahanCheck record
            $dehumBahanCheck = DehumBahanCheck::create([
                'nomer_dehum_bahan' => $request->input('nomer_dehum'),
                'bulan' => $request->input('bulan'),
                
                // Populate weekly dates
                'tanggal_minggu1' => $request->input('created_date_1'),
                'tanggal_minggu2' => $request->input('created_date_2'),
                'tanggal_minggu3' => $request->input('created_date_3'),
                'tanggal_minggu4' => $request->input('created_date_4'),
                
                // Populate weekly checkers
                'checked_by_minggu1' => $request->input('created_by_1'),
                'checked_by_minggu2' => $request->input('created_by_2'),
                'checked_by_minggu3' => $request->input('created_by_3'),
                'checked_by_minggu4' => $request->input('created_by_4'),
            ]);

            // Prepare and create DehumBahanResult records
            $checkedItems = $request->input('checked_items');
            
            foreach ($checkedItems as $index => $item) {
                DehumBahanResult::create([
                    'check_id' => $dehumBahanCheck->id,
                    'checked_items' => $item,
                    
                    // Week 1 data
                    'minggu1' => $request->input("check_1.{$index}", null),
                    'keterangan_minggu1' => $request->input("keterangan_1.{$index}", null),
                    
                    // Week 2 data
                    'minggu2' => $request->input("check_2.{$index}", null),
                    'keterangan_minggu2' => $request->input("keterangan_2.{$index}", null),
                    
                    // Week 3 data
                    'minggu3' => $request->input("check_3.{$index}", null),
                    'keterangan_minggu3' => $request->input("keterangan_3.{$index}", null),
                    
                    // Week 4 data
                    'minggu4' => $request->input("check_4.{$index}", null),
                    'keterangan_minggu4' => $request->input("keterangan_4.{$index}", null),
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->route('dehum-bahan.index')->with('success', 'Dehum check data successfully saved.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to save Dehum Bahan check data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Fetch the HopperCheck record with its results
        $dehumBahanCheck = DehumBahanCheck::with('results')->findOrFail($id);
        
        // Get the associated results
        $dehumBahanResults = $dehumBahanCheck->results;

        // Return the view and pass both $hopperCheck and $hopperResults
        return view('dehum-bahan.edit', compact('dehumBahanCheck', 'dehumBahanResults'));
    }
}