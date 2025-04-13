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
            $query->where('nomer_dehum_bahan', $request->search_dehum); // Menggunakan filter exact match
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
            'nomer_dehum_bahan' => 'required|integer|min:1|max:15',
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
        $existingRecord = DehumBahanCheck::where('nomer_dehum_bahan', $request->input('nomer_dehum_bahan'))
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
                'nomer_dehum_bahan' => $request->input('nomer_dehum_bahan'),
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
        $dehumCheck = DehumBahanCheck::findOrFail($id);
        $dehumResults = $dehumCheck->results;
        return view('dehum-bahan.edit', compact('dehumCheck', 'dehumResults'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'nomer_dehum_bahan' => 'required|integer|min:1',
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

        // Find the existing DehumBahanCheck record
        $dehumCheck = DehumBahanCheck::findOrFail($id);

        // Check for existing record with the same nomer_dehum and bulan, excluding the current record
        $existingRecord = DehumBahanCheck::where('nomer_dehum_bahan', $request->input('nomer_dehum_bahan'))
            ->where('bulan', $request->input('bulan'))
            ->where('id', '!=', $id)
            ->first();

        if ($existingRecord) {
            // If a record with the same dehum number and month exists, return an error
            return redirect()->back()->with('error', 'A record for this dehum number and month already exists.')
                            ->withInput();
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Update DehumBahanCheck record
            $dehumCheck->update([
                'nomer_dehum_bahan' => $request->input('nomer_dehum_bahan'),
                'bulan' => $request->input('bulan'),
                
                // Update weekly dates
                'tanggal_minggu1' => $request->input('created_date_1'),
                'tanggal_minggu2' => $request->input('created_date_2'),
                'tanggal_minggu3' => $request->input('created_date_3'),
                'tanggal_minggu4' => $request->input('created_date_4'),
                
                // Update weekly checkers
                'checked_by_minggu1' => $request->input('created_by_1'),
                'checked_by_minggu2' => $request->input('created_by_2'),
                'checked_by_minggu3' => $request->input('created_by_3'),
                'checked_by_minggu4' => $request->input('created_by_4'),
            ]);

            // Delete existing DehumBahanResult records for this check
            DehumBahanResult::where('check_id', $dehumCheck->id)->delete();

            // Prepare and create new DehumBahanResult records
            $checkedItems = $request->input('checked_items');
            
            foreach ($checkedItems as $index => $item) {
                DehumBahanResult::create([
                    'check_id' => $dehumCheck->id,
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
            return redirect()->route('dehum-bahan.index')->with('success', 'Dehum check data successfully updated.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to update dehum check data: ' . $e->getMessage());
        }
    }

    public function show($check_id)
    {
        // Find the main dehum bahan record
        $dehumBahanRecord = DehumBahanCheck::findOrFail($check_id);
        
        // Map the field names from the database to match template expectations
        $viewData = [
            'id' => $dehumBahanRecord->id,
            'nomer_dehum_bahan' => $dehumBahanRecord->nomer_dehum_bahan,
            'bulan' => $dehumBahanRecord->bulan,
            'created_by_1' => $dehumBahanRecord->checked_by_minggu1,
            'created_by_2' => $dehumBahanRecord->checked_by_minggu2,
            'created_by_3' => $dehumBahanRecord->checked_by_minggu3,
            'created_by_4' => $dehumBahanRecord->checked_by_minggu4,
            'created_date_1' => $dehumBahanRecord->tanggal_minggu1,
            'created_date_2' => $dehumBahanRecord->tanggal_minggu2,
            'created_date_3' => $dehumBahanRecord->tanggal_minggu3,
            'created_date_4' => $dehumBahanRecord->tanggal_minggu4,
            'approved_by_minggu1' => $dehumBahanRecord->approved_by_minggu1,
            'approved_by_minggu2' => $dehumBahanRecord->approved_by_minggu2,
            'approved_by_minggu3' => $dehumBahanRecord->approved_by_minggu3,
            'approved_by_minggu4' => $dehumBahanRecord->approved_by_minggu4
        ];

        // Prepare the checked items
        $items = [
            1 => 'Filter',
            2 => 'Selang', 
            3 => 'Kontraktor',
            4 => 'Temperatur kontrol',
            5 => 'MCB',
            6 => 'Dew Point'
        ];

        // Mapping nama item di view dengan kemungkinan nama item di database
        $itemMapping = [
            1 => ['Filter'],
            2 => ['Selang'],
            3 => ['Kontraktor'],
            4 => ['Temperatur control'],
            5 => ['MCB'],
            6 => ['Dew Point']
        ];

        // Fetch associated results
        $dehumBahanResults = DehumBahanResult::where('check_id', $check_id)->get();
        
        // Debug untuk melihat semua item di database (uncomment jika perlu)
        // $checkItemsList = $dehumBahanResults->pluck('checked_items')->toArray();
        // dd($checkItemsList);
        
        // Create arrays for check and keterangan data
        for ($weekNum = 1; $weekNum <= 4; $weekNum++) {
            $viewData["check_$weekNum"] = [];
            $viewData["keterangan_$weekNum"] = [];
            
            foreach ($items as $index => $item) {
                $result = null;
                // Coba semua kemungkinan nama untuk item ini
                foreach ($itemMapping[$index] as $possibleName) {
                    $result = $dehumBahanResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break; // Jika ditemukan, hentikan pencarian
                }
                
                $viewData["check_$weekNum"][$index] = $result ? $result->{"minggu$weekNum"} : '';
                $viewData["keterangan_$weekNum"][$index] = $result ? $result->{"keterangan_minggu$weekNum"} : '';
            }
        }

        // Convert to object for view compatibility
        $dehumBahanRecordObj = (object) $viewData;

        return view('dehum-bahan.show', [
            'dehumBahanRecord' => $dehumBahanRecordObj,
            'items' => $items
        ]);
    }

    public function approve(Request $request, $id)
    {
        // Validate the request
        $validatedData = $request->validate([
            'approved_by_minggu1' => 'nullable|string|max:255',
            'approved_by_minggu2' => 'nullable|string|max:255',
            'approved_by_minggu3' => 'nullable|string|max:255',
            'approved_by_minggu4' => 'nullable|string|max:255'
        ]);

        // Find the existing DehumBahan record
        $dehumBahanRecord = DehumBahanCheck::findOrFail($id);

        // Update the approval fields
        // Note: We use the exact field names from the database
        $dehumBahanRecord->approved_by_minggu1 = $validatedData['approved_by_minggu1'] ?? null;
        $dehumBahanRecord->approved_by_minggu2 = $validatedData['approved_by_minggu2'] ?? null;
        $dehumBahanRecord->approved_by_minggu3 = $validatedData['approved_by_minggu3'] ?? null;
        $dehumBahanRecord->approved_by_minggu4 = $validatedData['approved_by_minggu4'] ?? null;

        // Save the record
        $dehumBahanRecord->save();

        // Redirect back with a success message
        return redirect()->route('dehum-bahan.index')
            ->with('success', 'Dehum Bahan record approved successfully.');
    }
}
