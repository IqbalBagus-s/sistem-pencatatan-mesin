<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CraneMatrasCheck;
use App\Models\CraneMatrasResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF

class CraneMatrasControler extends Controller
{
    public function index(Request $request)
    {
        $query = CraneMatrasCheck::query();
    
        // Filter berdasarkan nama checker
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where('checked_by', 'LIKE', $search);
        }
    
        // Filter berdasarkan nomor crane matras
        if ($request->filled('search_crane')) {
            $query->where('nomer_crane_matras', $request->search_crane);
        }
    
        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            try {
                $date = \Carbon\Carbon::parse($request->bulan);
                $bulan = $date->format('Y-m'); // Format tahun-bulan (YYYY-MM)
                $query->where('bulan', $bulan);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Format bulan tidak valid.');
            }
        }
    
        // Tambahkan relasi dengan hasil pengecekan jika diperlukan
        if ($request->filled('with_results')) {
            $query->with('results');
        }
    
        // Urutkan berdasarkan kolom tertentu
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortColumn, $sortDirection);
    
        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());
    
        return view('crane_matras.index', compact('checks'));
    }

    public function create()
    {
        return view('crane_matras.create');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'nomer_crane_matras' => 'required|integer|min:1|max:3',
            'bulan' => 'required|date_format:Y-m',
            'checked_by_1' => 'nullable|string|max:255',
            'tanggal_1' => 'nullable|string',
            'checked_items' => 'required|array',
            'check' => 'required|array',
            'keterangan' => 'nullable|array',
        ]);
        
        // Cek apakah ada data dengan nomer_crane_matras dan bulan yang sama
        $existingRecord = CraneMatrasCheck::where('nomer_crane_matras', $request->input('nomer_crane_matras'))
            ->where('bulan', $request->input('bulan'))
            ->first();
    
        if ($existingRecord) {
            // Jika data dengan nomor Crane Matras dan bulan yang sama sudah ada, kembalikan error
            return redirect()->back()->with('error', 'Data sudah ada, silahkan buat ulang')
                            ->withInput();
        }
        
        // Mengubah format tanggal dari "DD Bulan YYYY" menjadi "YYYY-MM-DD"
        $tanggal = null;
        if ($request->filled('tanggal_1')) {
            $bulanIndonesia = [
                'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
                'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
                'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12'
            ];
            
            $parts = explode(' ', $request->tanggal_1);
            if (count($parts) == 3 && isset($bulanIndonesia[$parts[1]])) {
                $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $month = $bulanIndonesia[$parts[1]];
                $year = $parts[2];
                $tanggal = "$year-$month-$day";
            }
        }
        
        try {
            // Mulai transaksi database
            DB::beginTransaction();
            
            // Buat record baru di CraneMatrasCheck
            $craneMatrasCheck = CraneMatrasCheck::create([
                'nomer_crane_matras' => $request->input('nomer_crane_matras'),
                'bulan' => $request->input('bulan'),
                'tanggal' => $tanggal,
                'checked_by' => $request->input('checked_by_1'),
                'approved_by' => null, // Diisi pada tahap approval
            ]);
    
            // Log untuk debugging
            Log::info('Checked Items:', $request->input('checked_items'));
            Log::info('Check Values:', $request->input('check'));
            
            // Mendefinisikan daftar item yang diperiksa
            $items = $request->input('checked_items');
            
            // Simpan hasil pemeriksaan untuk setiap item
            foreach ($items as $index => $item) {
                // Ambil nilai check dan keterangan untuk item ini
                $checkValue = isset($request->input('check')[$index]) ? $request->input('check')[$index] : null;
                $keterangan = isset($request->input('keterangan')[$index]) ? $request->input('keterangan')[$index] : null;
                
                // Log untuk debugging
                Log::info("Menyimpan item {$index}: {$item} dengan nilai check: {$checkValue}");
                
                // Simpan ke model CraneMatrasResult
                CraneMatrasResult::create([
                    'check_id' => $craneMatrasCheck->id,
                    'checked_items' => $item,
                    'check' => $checkValue,
                    'keterangan' => $keterangan,
                ]);
            }
    
            // Commit transaksi
            DB::commit();
    
            // Redirect dengan pesan sukses
            return redirect()->route('crane-matras.index')
                ->with('success', 'Data pemeriksaan Crane Matras berhasil disimpan.');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
    
            // Log kesalahan untuk debugging
            Log::error('Crane Matras store error: ' . $e->getMessage());
            Log::error('Error detail: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
    
            // Redirect kembali dengan pesan kesalahan
            return redirect()->back()->with('error', 'Gagal menyimpan data pemeriksaan Crane Matras: ' . $e->getMessage())
                             ->withInput();
        }
    }
}
