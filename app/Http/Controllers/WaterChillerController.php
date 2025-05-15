<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterChillerCheck;
use App\Models\WaterChillerResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        // Validate the request
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        // Extract month from the date
        $bulan = date('m', strtotime($request->tanggal));
        
       // Check if record for this date already exists
        $existingRecord = WaterChillerCheck::whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existingRecord) {
            // Format tanggal menggunakan Carbon dengan locale Indonesia
            $tanggal = Carbon::parse($request->tanggal)->locale('id')->isoFormat('D MMMM YYYY');
            
            // Buat pesan error dengan informasi tanggal yang spesifik
            $pesanError = "Data untuk tanggal {$tanggal} sudah ada!";
            
            return redirect()->back()
                ->withInput()
                ->with('error', $pesanError);
        }

        // Begin transaction to ensure data integrity
        DB::beginTransaction();
        
        try {
            // Create water chiller check record
            $waterChillerCheck = WaterChillerCheck::create([
                'tanggal' => $request->tanggal,
                'hari' => $request->hari,
                'checked_by' => Auth::user()->username, 
                'keterangan' => $request->catatan,
            ]);
            
            // Process each water chiller machine data (32 machines)
            for ($i = 1; $i <= 32; $i++) {
                WaterChillerResult::create([
                    'check_id' => $waterChillerCheck->id,
                    'no_mesin' => 'CH' . $i,
                    'Temperatur_Compressor' => $request->input("temperatur_kompresor.$i"),
                    'Temperatur_Kabel' => $request->input("temperatur_kabel.$i"),
                    'Temperatur_Mcb' => $request->input("temperatur_mcb.$i"),
                    'Temperatur_Air' => $request->input("temperatur_air.$i"),
                    'Temperatur_Pompa' => $request->input("temperatur_pompa.$i"),
                    'Evaporator' => $request->input("evaporator.$i"),
                    'Fan_Evaporator' => $request->input("fan_evaporator.$i"),
                    'Freon' => $request->input("freon.$i"),
                    'Air' => $request->input("air.$i"),
                ]);
            }
            
            // Commit the transaction if everything is successful
            DB::commit();
            
            return redirect()->route('water-chiller.index')
                ->with('success', 'Data pemeriksaan Water Chiller berhasil disimpan.');
                
        } catch (\Exception $e) {
            // Rollback transaction if there is an error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

        public function edit($id)
    {
        // Find the water chiller check by ID
        $waterChillerCheck = WaterChillerCheck::findOrFail($id);
        
        // Ambil semua hasil untuk check ini 
        // (relasi results seharusnya hasMany, bukan hasOne)
        $resultsCollection = WaterChillerResult::where('check_id', $id)->get();
        
        // Organize results by machine number for easy access in the view
        $results = [];
        
        foreach ($resultsCollection as $result) {
            $machineNumber = str_replace('CH', '', $result->no_mesin);
            $results[$machineNumber] = $result;
        }
        
        return view('water_chiller.edit', compact('waterChillerCheck', 'results'));
    }

    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        // Begin transaction to ensure data integrity
        DB::beginTransaction();
        
        try {
            // Find the existing water chiller check record
            $waterChillerCheck = WaterChillerCheck::findOrFail($id);
            
            // Update the main record
            $waterChillerCheck->update([
                'tanggal' => $request->tanggal,
                'hari' => $request->hari,
                'keterangan' => $request->catatan,
                // Not updating checked_by to preserve the original checker's identity
            ]);
            
            // Process each water chiller machine data (32 machines)
            for ($i = 1; $i <= 32; $i++) {
                // Find the existing result or create a new one if it doesn't exist
                $result = WaterChillerResult::updateOrCreate(
                    [
                        'check_id' => $waterChillerCheck->id,
                        'no_mesin' => 'CH' . $i,
                    ],
                    [
                        'Temperatur_Compressor' => $request->input("temperatur_kompresor.$i"),
                        'Temperatur_Kabel' => $request->input("temperatur_kabel.$i"),
                        'Temperatur_Mcb' => $request->input("temperatur_mcb.$i"),
                        'Temperatur_Air' => $request->input("temperatur_air.$i"),
                        'Temperatur_Pompa' => $request->input("temperatur_pompa.$i"),
                        'Evaporator' => $request->input("evaporator.$i"),
                        'Fan_Evaporator' => $request->input("fan_evaporator.$i"),
                        'Freon' => $request->input("freon.$i"),
                        'Air' => $request->input("air.$i"),
                    ]
                );
            }
            
            // Commit the transaction if everything is successful
            DB::commit();
            
            return redirect()->route('water-chiller.index')
                ->with('success', 'Data pemeriksaan Water Chiller berhasil diperbarui.');
                
        } catch (\Exception $e) {
            // Rollback transaction if there is an error
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }
public function show($id)
{
    // Mencari data water chiller check berdasarkan ID
    $waterChillerCheck = WaterChillerCheck::findOrFail($id);
    
    // Mengambil detail hasil pemeriksaan water chiller
    $details = WaterChillerResult::where('check_id', $id)->get();
    
    // Menampilkan view dengan data yang sesuai
    return view('water_chiller.show', compact('waterChillerCheck', 'details'));
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
    
        // Format tanggal untuk nama file
        $formattedDate = date('d-m-Y', strtotime($check->tanggal));

        // Mengembalikan file PDF untuk di-download dengan format nama yang baru
        return $pdf->download('Water Chiller Form_' . $formattedDate . '.pdf');
    }
}