<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HopperCheck;
use App\Models\HopperResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF

class HopperController extends Controller
{
    public function index(Request $request)
    {
        $query = HopperCheck::query();

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

        // Filter berdasarkan nomor hopper
        if ($request->filled('search_hopper')) {
            $query->where('nomer_hopper', $request->search_hopper); // Menggunakan filter exact match
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

        return view('hopper.index', compact('checks'));
    }

    public function create()
    {
        return view('hopper.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'nomer_hopper' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
            
            // Validation for creator fields - updated to match the form field names
            'checked_by_minggu1' => 'nullable|string|max:255',
            'tanggal_minggu1' => 'nullable|date',
            'checked_by_minggu2' => 'nullable|string|max:255',
            'tanggal_minggu2' => 'nullable|date',
            'checked_by_minggu3' => 'nullable|string|max:255',
            'tanggal_minggu3' => 'nullable|date',
            'checked_by_minggu4' => 'nullable|string|max:255',
            'tanggal_minggu4' => 'nullable|date',
            
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

        // Check for existing record with the same nomer_hopper and bulan
        $existingRecord = HopperCheck::where('nomer_hopper', $request->input('nomer_hopper'))
            ->where('bulan', $request->input('bulan'))
            ->first();

        if ($existingRecord) {
            // If a record with the same hopper number and month exists, return an error
            return redirect()->back()->with('error', 'A record for this hopper number and month already exists.')
                            ->withInput();
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create HopperCheck record - using the correct field names from the form
            $hopperCheck = HopperCheck::create([
                'nomer_hopper' => $request->input('nomer_hopper'),
                'bulan' => $request->input('bulan'),
                
                // Directly use the matching field names from the form
                'tanggal_minggu1' => $request->input('tanggal_minggu1'),
                'tanggal_minggu2' => $request->input('tanggal_minggu2'),
                'tanggal_minggu3' => $request->input('tanggal_minggu3'),
                'tanggal_minggu4' => $request->input('tanggal_minggu4'),
                
                // Directly use the matching field names from the form
                'checked_by_minggu1' => $request->input('checked_by_minggu1'),
                'checked_by_minggu2' => $request->input('checked_by_minggu2'),
                'checked_by_minggu3' => $request->input('checked_by_minggu3'),
                'checked_by_minggu4' => $request->input('checked_by_minggu4'),
            ]);

            // Prepare and create HopperResult records
            $checkedItems = $request->input('checked_items');
            
            foreach ($checkedItems as $index => $item) {
                HopperResult::create([
                    'check_id' => $hopperCheck->id,
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
            return redirect()->route('hopper.index')->with('success', 'Hopper check data successfully saved.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to save hopper check data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        // Fetch the HopperCheck record with its results
        $hopperCheck = HopperCheck::with('results')->findOrFail($id);
        
        // Get the associated results
        $hopperResults = $hopperCheck->results;

        // Return the view and pass both $hopperCheck and $hopperResults
        return view('hopper.edit', compact('hopperCheck', 'hopperResults'));
    }

    public function update(Request $request, $id)
    {
        // Validasi hanya untuk field yang dibutuhkan, tidak perlu validasi semua field
        $validatedData = $request->validate([
            'nomer_hopper' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Find the existing HopperCheck record
        $hopperCheck = HopperCheck::findOrFail($id);
        $hopperResults = $hopperCheck->results;

        // Check for existing record with the same nomer_hopper and bulan, excluding the current record
        $existingRecord = HopperCheck::where('nomer_hopper', $request->input('nomer_hopper'))
            ->where('bulan', $request->input('bulan'))
            ->where('id', '!=', $id)
            ->first();

        if ($existingRecord) {
            // If a record with the same hopper number and month exists, return an error
            return redirect()->back()->with('error', 'Data untuk nomor hopper dan bulan ini sudah ada.')
                            ->withInput();
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Update HopperCheck record - hanya perbarui nilai yang tidak disetujui
            $updateData = [
                'nomer_hopper' => $request->input('nomer_hopper'),
                'bulan' => $request->input('bulan'),
            ];
            
            // Update data checked_by dan tanggal hanya jika minggu tersebut belum disetujui
            for ($j = 1; $j <= 4; $j++) {
                if (!$hopperCheck->{'approved_by_minggu'.$j} || $hopperCheck->{'approved_by_minggu'.$j} == '-') {
                    $updateData['checked_by_minggu'.$j] = $request->input('checked_by_minggu'.$j);
                    $updateData['tanggal_minggu'.$j] = $request->input('tanggal_minggu'.$j);
                }
            }

            $hopperCheck->update($updateData);

            // Delete existing HopperResult records for this check
            HopperResult::where('check_id', $hopperCheck->id)->delete();

            // Prepare and create new HopperResult records
            $checkedItems = $request->input('checked_items');
            
            foreach ($checkedItems as $index => $item) {
                $resultData = [
                    'check_id' => $hopperCheck->id,
                    'checked_items' => $item,
                ];
                
                // Proses data untuk setiap minggu
                for ($j = 1; $j <= 4; $j++) {
                    if (!$hopperCheck->{'approved_by_minggu'.$j} || $hopperCheck->{'approved_by_minggu'.$j} == '-') {
                        $resultData['minggu'.$j] = $request->input("check_{$j}.{$index}", null);
                        $resultData['keterangan_minggu'.$j] = $request->input("keterangan_{$j}.{$index}", null);
                    } else {
                        // Jika sudah disetujui, gunakan data lama
                        $oldResult = $hopperResults->firstWhere('checked_items', $item);
                        $resultData['minggu'.$j] = $oldResult ? $oldResult->{'minggu'.$j} : null;
                        $resultData['keterangan_minggu'.$j] = $oldResult ? $oldResult->{'keterangan_minggu'.$j} : null;
                    }
                }
                
                HopperResult::create($resultData);
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->route('hopper.index')->with('success', 'Data pencatatan mesin Hopper berhasil diperbarui.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Redirect back with error message
            return redirect()->back()->with('error', 'Gagal memperbarui data pencatatan mesin Hopper: ' . $e->getMessage());
        }
    }

    public function show($check_id)
    {
        // Find the main hopper record
        $hopperRecord = HopperCheck::findOrFail($check_id);

        // Modify the checker fields to ensure unique values
        $checkerFields = [
            'checked_by_minggu1',
            'checked_by_minggu2',
            'checked_by_minggu3',
            'checked_by_minggu4'
        ];

        // Collect unique checkers
        $uniqueCheckers = collect($checkerFields)
            ->map(function ($field) use ($hopperRecord) {
                return $hopperRecord->$field;
            })
            ->filter()
            ->unique()
            ->values();

        // Add unique checkers to the record
        $hopperRecord->unique_checkers = $uniqueCheckers->implode(', ');

        // Prepare the checked items
        $items = [
            1 => 'Filter',
            2 => 'Selang', 
            3 => 'Kontraktor',
            4 => 'Temperatur Kontrol',
            5 => 'MCB'
        ];

        // Fetch associated results
        $hopperResults = HopperResult::where('check_id', $check_id)->get()->keyBy('checked_items');

        // Prepare check and keterangan arrays for each week
        $weekFields = [
            'check_1' => 'minggu1',
            'check_2' => 'minggu2',
            'check_3' => 'minggu3',
            'check_4' => 'minggu4',
            'keterangan_1' => 'keterangan_minggu1',
            'keterangan_2' => 'keterangan_minggu2',
            'keterangan_3' => 'keterangan_minggu3',
            'keterangan_4' => 'keterangan_minggu4'
        ];

        // Create a new array to store the modified data
        $viewData = $hopperRecord->toArray();

        // Dynamically populate the arrays
        foreach ($weekFields as $recordKey => $dbField) {
            $viewData[$recordKey] = [];
            foreach ($items as $index => $item) {
                $viewData[$recordKey][$index] = optional($hopperResults->get($item))->$dbField ?? '';
            }
        }

        // Convert back to an object for view compatibility
        $viewData = (object) $viewData;

        return view('hopper.show', [
            'hopperRecord' => $viewData,
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

        // Find the existing Hopper record
        $hopperRecord = HopperCheck::findOrFail($id);

        // Update the approval fields
        // Note: We use the exact field names from the database
        $hopperRecord->approved_by_minggu1 = $validatedData['approved_by_minggu1'] ?? null;
        $hopperRecord->approved_by_minggu2 = $validatedData['approved_by_minggu2'] ?? null;
        $hopperRecord->approved_by_minggu3 = $validatedData['approved_by_minggu3'] ?? null;
        $hopperRecord->approved_by_minggu4 = $validatedData['approved_by_minggu4'] ?? null;


        // Save the record
        $hopperRecord->save();

        // Redirect back with a success message
        return redirect()->route('hopper.index')
            ->with('success', 'Hopper record approved successfully.');
    }
}
