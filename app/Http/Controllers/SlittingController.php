<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlittingCheck;
use App\Models\SlittingResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF


class SlittingController extends Controller
{
    public function index(Request $request)
    {
        $query = SlittingCheck::query();

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

        // Filter berdasarkan nomor slitting
        if ($request->filled('search_slitting')) {
            $query->where('nomer_slitting', $request->search_slitting); // Menggunakan filter exact match
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

        $query->orderBy('created_at', 'desc');

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('slitting.index', compact('checks'));
    }

    public function create()
    {
        return view('slitting.create');
    }

    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'nomer_slitting' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
            
            // Updated validation for checker fields to match form names
            'checked_by_1' => 'nullable|string|max:255',
            'check_num_1' => 'nullable|string',
            'checked_by_2' => 'nullable|string|max:255',
            'check_num_2' => 'nullable|string',
            'checked_by_3' => 'nullable|string|max:255',
            'check_num_3' => 'nullable|string',
            'checked_by_4' => 'nullable|string|max:255',
            'check_num_4' => 'nullable|string',
            
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
    
        // Check for existing record with the same nomer_slitting and bulan
        $existingRecord = SlittingCheck::where('nomer_slitting', $request->input('nomer_slitting'))
            ->where('bulan', $request->input('bulan'))
            ->first();

        if ($existingRecord) {
            // Ambil nilai yang duplikat
            $nomerSlitting = $request->input('nomer_slitting');
            $bulan = Carbon::parse($request->input('bulan') . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Buat pesan error dengan informasi spesifik
            $pesanError = "Data sudah ada untuk Slitting nomor {$nomerSlitting} pada bulan {$bulan}, silahkan buat ulang";
            
            // Redirect dengan pesan error yang detail
            return redirect()->back()->with('error', $pesanError)
                            ->withInput();
        }
    
        try {
            // Start a database transaction
            DB::beginTransaction();
    
            // Create SlittingCheck record with corrected field names
            $slittingCheck = SlittingCheck::create([
                'nomer_slitting' => $request->input('nomer_slitting'),
                'bulan' => $request->input('bulan'),
                
                // Map the form field names to database field names
                'checked_by_minggu1' => $request->input('checked_by_1'),
                'checked_date_minggu1' => $request->has('check_num_1') && $request->input('check_num_1') ? now() : null,
                
                'checked_by_minggu2' => $request->input('checked_by_2'),
                'checked_date_minggu2' => $request->has('check_num_2') && $request->input('check_num_2') ? now() : null,
                
                'checked_by_minggu3' => $request->input('checked_by_3'),
                'checked_date_minggu3' => $request->has('check_num_3') && $request->input('check_num_3') ? now() : null,
                
                'checked_by_minggu4' => $request->input('checked_by_4'),
                'checked_date_minggu4' => $request->has('check_num_4') && $request->input('check_num_4') ? now() : null,
            ]);
    
            // Log untuk debugging
            Log::info('Checked Items:', $request->input('checked_items'));
            Log::info('Check 1:', $request->input('check_1'));
            
            // SOLUSI MASALAH: Mendefinisikan items sama seperti di view
            $items = [
                1 => 'Conveyor',
                2 => 'Motor Conveyor',
                3 => 'Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Inverter',
                6 => 'Vibrator',
                7 => 'Motor Vibrator',
                8 => 'Motor Blower',
                9 => 'Selang angin',
                10 => 'Flow Control',
                11 => 'Sensor',
                12 => 'Limit Switch',
                13 => 'Pisau Cutting',
                14 => 'Motor Cutting',
                15 => 'Elemen',
                16 => 'Regulator',
                17 => 'Air Filter',
            ];
            
            // Proses setiap item sesuai dengan key di $items, bukan indeks dari array checked_items
            foreach ($items as $key => $item) {
                // Mengakses nilai dari form menggunakan key yang sama dengan key di array $items
                $minggu1 = isset($request->input('check_1')[$key]) ? $request->input('check_1')[$key] : null;
                $keterangan1 = isset($request->input('keterangan_1')[$key]) ? $request->input('keterangan_1')[$key] : null;
                
                $minggu2 = isset($request->input('check_2')[$key]) ? $request->input('check_2')[$key] : null;
                $keterangan2 = isset($request->input('keterangan_2')[$key]) ? $request->input('keterangan_2')[$key] : null;
                
                $minggu3 = isset($request->input('check_3')[$key]) ? $request->input('check_3')[$key] : null;
                $keterangan3 = isset($request->input('keterangan_3')[$key]) ? $request->input('keterangan_3')[$key] : null;
                
                $minggu4 = isset($request->input('check_4')[$key]) ? $request->input('check_4')[$key] : null;
                $keterangan4 = isset($request->input('keterangan_4')[$key]) ? $request->input('keterangan_4')[$key] : null;
                
                // Log untuk debugging
                Log::info("Menyimpan item {$key}: {$item} dengan nilai minggu1: {$minggu1}");
                
                SlittingResult::create([
                    'check_id' => $slittingCheck->id,
                    'checked_items' => $item, // Gunakan nilai item dari array $items
                    
                    // Week 1 data
                    'minggu1' => $minggu1, 
                    'keterangan_minggu1' => $keterangan1,
                    
                    // Week 2 data
                    'minggu2' => $minggu2,
                    'keterangan_minggu2' => $keterangan2,
                    
                    // Week 3 data
                    'minggu3' => $minggu3,
                    'keterangan_minggu3' => $keterangan3,
                    
                    // Week 4 data
                    'minggu4' => $minggu4,
                    'keterangan_minggu4' => $keterangan4,
                ]);
            }
    
            // Commit the transaction
            DB::commit();
    
            // Redirect with success message
            return redirect()->route('slitting.index')->with('success', 'Data pemeriksaan Slitting berhasil disimpan.');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();
    
            // Log the error for debugging
            Log::error('Slitting store error: ' . $e->getMessage());
            Log::error('Error detail: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
    
            // Redirect back with error message
            return redirect()->back()->with('error', 'Gagal menyimpan data pemeriksaan Slitting: ' . $e->getMessage())
                             ->withInput();
        }
    }

    public function edit($id)
    {
        // Ambil data utama slitting check
        $check = SlittingCheck::findOrFail($id);
        
        // Ambil data hasil pemeriksaan
        $results = SlittingResult::where('check_id', $id)->get();
        
        // Definisikan items sama seperti di fungsi store
        $items = [
            1 => 'Conveyor',
            2 => 'Motor Conveyor',
            3 => 'Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Inverter',
            6 => 'Vibrator',
            7 => 'Motor Vibrator',
            8 => 'Motor Blower',
            9 => 'Selang angin',
            10 => 'Flow Control',
            11 => 'Sensor',
            12 => 'Limit Switch',
            13 => 'Pisau Cutting',
            14 => 'Motor Cutting',
            15 => 'Elemen',
            16 => 'Regulator',
            17 => 'Air Filter',
        ];
        
        // Format data untuk view
        $formattedResults = [];
        
        // Siapkan array untuk menyimpan data checker dan approval status
        $checkerData = [
            'checked_by_1' => $check->checked_by_minggu1,
            'check_num_1' => $check->checked_by_minggu1 ? true : false,
            'checked_by_2' => $check->checked_by_minggu2,
            'check_num_2' => $check->checked_by_minggu2 ? true : false,
            'checked_by_3' => $check->checked_by_minggu3,
            'check_num_3' => $check->checked_by_minggu3 ? true : false,
            'checked_by_4' => $check->checked_by_minggu4,
            'check_num_4' => $check->checked_by_minggu4 ? true : false,
            'approved_by_1' => $check->approved_by_minggu1,
            'approved_by_2' => $check->approved_by_minggu2,
            'approved_by_3' => $check->approved_by_minggu3,
            'approved_by_4' => $check->approved_by_minggu4
        ];
        
        // Format data hasil pemeriksaan berdasarkan item
        foreach ($items as $key => $item) {
            $result = $results->where('checked_items', $item)->first();
            
            if ($result) {
                $formattedResults[$key] = [
                    'item' => $item,
                    'minggu1' => $result->minggu1,
                    'keterangan_minggu1' => $result->keterangan_minggu1,
                    'minggu2' => $result->minggu2,
                    'keterangan_minggu2' => $result->keterangan_minggu2,
                    'minggu3' => $result->minggu3,
                    'keterangan_minggu3' => $result->keterangan_minggu3,
                    'minggu4' => $result->minggu4,
                    'keterangan_minggu4' => $result->keterangan_minggu4,
                ];
            } else {
                // Jika tidak ada data untuk item ini, siapkan struktur kosong
                $formattedResults[$key] = [
                    'item' => $item,
                    'minggu1' => null,
                    'keterangan_minggu1' => null,
                    'minggu2' => null,
                    'keterangan_minggu2' => null,
                    'minggu3' => null,
                    'keterangan_minggu3' => null,
                    'minggu4' => null,
                    'keterangan_minggu4' => null,
                ];
            }
        }
        
        // Pass the approval status to the view
        $approvalStatus = [
            'minggu1' => !empty($check->approved_by_minggu1),
            'minggu2' => !empty($check->approved_by_minggu2),
            'minggu3' => !empty($check->approved_by_minggu3),
            'minggu4' => !empty($check->approved_by_minggu4)
        ];
        
        return view('slitting.edit', compact('check', 'formattedResults', 'items', 'checkerData', 'approvalStatus'));
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'nomer_slitting' => 'required|integer|between:1,10',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Cari data slitting yang akan diupdate
        $slittingCheck = SlittingCheck::findOrFail($id);

        // Cek apakah ada perubahan pada data utama (nomer_slitting, bulan)
        if ($slittingCheck->nomer_slitting != $request->nomer_slitting || 
            $slittingCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = SlittingCheck::where('nomer_slitting', $request->nomer_slitting)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $id) // Kecualikan record saat ini
                ->first();
            
            if ($existingRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data dengan nomor slitting dan bulan yang sama sudah ada!');
            }
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Update data SlittingCheck
            $slittingCheck->update([
                'nomer_slitting' => $request->nomer_slitting,
                'bulan' => $request->bulan,
                'checked_by_minggu1' => $request->checked_by_1,
                'checked_date_minggu1' => $request->check_num_1 ? now() : null,
                'checked_by_minggu2' => $request->checked_by_2,
                'checked_date_minggu2' => $request->check_num_2 ? now() : null,
                'checked_by_minggu3' => $request->checked_by_3,
                'checked_date_minggu3' => $request->check_num_3 ? now() : null,
                'checked_by_minggu4' => $request->checked_by_4,
                'checked_date_minggu4' => $request->check_num_4 ? now() : null,
            ]);
            
            // Definisikan items yang diperiksa
            $items = [
                1 => 'Conveyor',
                2 => 'Motor Conveyor',
                3 => 'Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Inverter',
                6 => 'Vibrator',
                7 => 'Motor Vibrator',
                8 => 'Motor Blower',
                9 => 'Selang angin',
                10 => 'Flow Control',
                11 => 'Sensor', 
                12 => 'Limit Switch',
                13 => 'Pisau Cutting',
                14 => 'Motor Cutting',
                15 => 'Elemen',
                16 => 'Regulator',
                17 => 'Air Filter',
            ];
            
            // Ambil data existing dari tabel SlittingResult
            $existingResults = SlittingResult::where('check_id', $id)->get()->keyBy('checked_items');
            
            // Proses setiap item
            foreach ($items as $itemId => $itemName) {
                // Persiapkan data untuk update atau create
                $resultData = [
                    'minggu1' => $request->input("check_1.{$itemId}", '-'),
                    'keterangan_minggu1' => $request->input("keterangan_1.{$itemId}"),
                    'minggu2' => $request->input("check_2.{$itemId}", '-'),
                    'keterangan_minggu2' => $request->input("keterangan_2.{$itemId}"),
                    'minggu3' => $request->input("check_3.{$itemId}", '-'),
                    'keterangan_minggu3' => $request->input("keterangan_3.{$itemId}"),
                    'minggu4' => $request->input("check_4.{$itemId}", '-'),
                    'keterangan_minggu4' => $request->input("keterangan_4.{$itemId}"),
                ];
                
                // Cek apakah record sudah ada
                $existingResult = $existingResults->get($itemName);
                
                if ($existingResult) {
                    // Update record yang sudah ada
                    $existingResult->update($resultData);
                } else {
                    // Buat record baru jika belum ada
                    $resultData['check_id'] = $id;
                    $resultData['checked_items'] = $itemName;
                    SlittingResult::create($resultData);
                }
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('slitting.index')
                ->with('success', 'Data berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Fetch the slitting check with its results
        $check = SlittingCheck::findOrFail($id);
        
        // Get all results related to this check
        $results = SlittingResult::where('check_id', $id)->get();
        
        // Define items like in the edit function
        $items = [
            1 => 'Conveyor',
            2 => 'Motor Conveyor',
            3 => 'Kelistrikan',
            4 => 'Kontaktor',
            5 => 'Inverter',
            6 => 'Vibrator',
            7 => 'Motor Vibrator',
            8 => 'Motor Blower',
            9 => 'Selang angin',
            10 => 'Flow Control',
            11 => 'Sensor',
            12 => 'Limit Switch',
            13 => 'Pisau Cutting',
            14 => 'Motor Cutting',
            15 => 'Elemen',
            16 => 'Regulator',
            17 => 'Air Filter',
        ];
        
        // Format data untuk tampilan
        $formattedResults = [];
        
        // Format hasil inspeksi berdasarkan item
        foreach ($items as $key => $item) {
            // First try to find by item text
            $result = $results->where('checked_items', $item)->first();
            
            // If not found, try to find by item number in different formats
            if (!$result) {
                $result = $results->first(function($record) use ($key) {
                    // Try to match item_id if it exists
                    if (isset($record->item_id) && $record->item_id == $key) {
                        return true;
                    }
                    
                    // Try to match "1:Conveyor" format
                    if (strpos($record->checked_items, $key.':') === 0) {
                        return true;
                    }
                    
                    // Try to match just the number
                    if ($record->checked_items == (string)$key) {
                        return true;
                    }
                    
                    return false;
                });
            }
            
            if ($result) {
                $formattedResults[$key] = [
                    'minggu1' => $result->minggu1 ?? '-',
                    'keterangan_minggu1' => $result->keterangan_minggu1 ?? '',
                    'minggu2' => $result->minggu2 ?? '-',
                    'keterangan_minggu2' => $result->keterangan_minggu2 ?? '',
                    'minggu3' => $result->minggu3 ?? '-',
                    'keterangan_minggu3' => $result->keterangan_minggu3 ?? '',
                    'minggu4' => $result->minggu4 ?? '-',
                    'keterangan_minggu4' => $result->keterangan_minggu4 ?? '',
                ];
            } else {
                // If no data for this item, prepare empty structure with defaults
                $formattedResults[$key] = [
                    'minggu1' => '-',
                    'keterangan_minggu1' => '',
                    'minggu2' => '-',
                    'keterangan_minggu2' => '',
                    'minggu3' => '-',
                    'keterangan_minggu3' => '',
                    'minggu4' => '-',
                    'keterangan_minggu4' => '',
                ];
            }
        }
        
        // Periksa kolom mana yang memiliki penanggung jawab
        $hasApprovedBy = [];
        for ($i = 1; $i <= 4; $i++) {
            $approvedBy = $check->{'approved_by_minggu'.$i} ?? '';
            $hasApprovedBy[$i] = !empty($approvedBy);
        }
        
        return view('slitting.show', compact('check', 'formattedResults', 'hasApprovedBy'));
    }

    public function approve(Request $request, $id)
    {
        $check = SlittingCheck::findOrFail($id);
        
        // Process approvals for each week
        for ($week = 1; $week <= 4; $week++) {
            $approverKey = "approved_by_minggu{$week}";
            
            if ($request->has($approverKey) && !empty($request->input($approverKey))) {
                // Set the approved_by field if it's not already set
                if (empty($check->$approverKey)) {
                    $check->$approverKey = $request->input($approverKey);
                }
            }
        }
        
        $check->save();
        
        return redirect()
            ->route('slitting.index')
            ->with('success', 'Persetujuan berhasil disimpan');
    }
   
    public function reviewPdf($id)
    {
        // Cari dokumen SlittingCheck berdasarkan ID dengan eager loading untuk relasi results
        $slittingCheck = SlittingCheck::with('results')->findOrFail($id);
        
        // Data yang akan dikirim ke view
        $data = [
            'slittingCheck' => $slittingCheck,
            'title' => 'Review PDF ' . $slittingCheck->nomer_slitting,
            'results' => $slittingCheck->results,
            'bulan' => $slittingCheck->bulan
        ];
        
        // Gunakan nama view secara langsung
        return view('slitting.review_pdf', $data);
    }
    
    public function downloadPdf($id)
    {
        // Cari dokumen SlittingCheck berdasarkan ID
        $slittingCheck = SlittingCheck::findOrFail($id);
        
        // Ambil data hasil slitting
        $results = $slittingCheck->results;
        
        // Generate PDF dengan menggunakan string HTML langsung
        $pdf = PDF::loadView('slitting.review_pdf', [
            'slittingCheck' => $slittingCheck,
            'results' => $results,
            'bulan' => $slittingCheck->bulan,
            'title' => 'Dokumen Check Slitting ' . $slittingCheck->nomer_slitting
        ]);
        
        // Konfigurasi PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        // Nama file yang akan didownload
        $filename = 'Dokumen_Slitting_' . $slittingCheck->nomer_slitting . '_' . $slittingCheck->bulan . '.pdf';
        
        // Return response download
        return $pdf->download($filename);
    }
}
