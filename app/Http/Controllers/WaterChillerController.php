<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaterChillerCheck;
use App\Models\WaterChillerResult;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF

class WaterChillerController extends Controller
{
    public function index(Request $request)
    {
        $query = WaterChillerCheck::orderBy('created_at', 'desc');

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
            
            // Log activity menggunakan model Activity
            Activity::logActivity(
                userType: 'checker',
                userId: Auth::id(),
                userName: Auth::user()->username,
                action: 'created',
                description: 'Checker ' . Auth::user()->username . ' Membuat pemeriksaan Water Chiller untuk tanggal ' . Carbon::parse($request->tanggal)->locale('id')->isoFormat('D MMMM YYYY'),
                targetType: 'water_chiller_check',
                targetId: $waterChillerCheck->id,
                details: [
                    'tanggal' => $request->tanggal,
                    'hari' => $request->hari,
                    'total_mesin' => 32,
                    'catatan' => $request->catatan,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]
            );
            
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

    public function reviewPdf($id)
    {
        // Ambil data pemeriksaan water chiller berdasarkan ID
        $waterChiller = WaterChillerCheck::findOrFail($id);

        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/023/REV.01')->firstOrFail();

        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');

        // Ambil detail hasil pemeriksaan untuk water chiller
        $details = WaterChillerResult::where('check_id', $id)->get();

        // Render view sebagai HTML untuk preview PDF
        $view = view('water_chiller.review_pdf', compact('waterChiller', 'details', 'form', 'formattedTanggalEfektif'));

        // Return view untuk preview
        return $view;
    }

    public function downloadPdf($id)
    {
        // Ambil data pemeriksaan water chiller berdasarkan ID
        $waterChiller = WaterChillerCheck::findOrFail($id);

        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/023/REV.01')->firstOrFail();

        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil semua detail hasil pemeriksaan untuk setiap mesin
        $details = WaterChillerResult::where('check_id', $id)->get();
        
        // Format tanggal dari model WaterChillerCheck
        $tanggal = new \DateTime($waterChiller->tanggal);
        $tanggalFormatted = $tanggal->format('d_F_Y');
        // Ubah nama bulan ke Bahasa Indonesia
        $bulanIndonesia = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        // Ganti nama bulan dalam bahasa Inggris dengan nama bulan dalam Bahasa Indonesia
        foreach ($bulanIndonesia as $english => $indonesia) {
            $tanggalFormatted = str_replace($english, $indonesia, $tanggalFormatted);
        }
        
        // Generate nama file PDF
        $filename = 'Waterchiller_tanggal_' . $tanggalFormatted . '.pdf';
        
        // Render view sebagai HTML
        $html = view('water_chiller.review_pdf', compact('waterChiller', 'details', 'form', 'formattedTanggalEfektif'))->render();
        
        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Atur ukuran dan orientasi halaman (opsional)
        $dompdf->setPaper('A4', 'landscape');
        
        // Render PDF (mengubah HTML menjadi PDF)
        $dompdf->render();
        
        // Download file PDF
        return $dompdf->stream($filename, [
            'Attachment' => false, // Set true untuk download, false untuk preview di browser
        ]);
    }
}