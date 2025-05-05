<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SlittingCheck;
use App\Models\SlittingResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            // If a record with the same Slitting number and month exists, return an error
            return redirect()->back()->with('error', 'Data sudah ada, silahkan buat ulang')
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
}
