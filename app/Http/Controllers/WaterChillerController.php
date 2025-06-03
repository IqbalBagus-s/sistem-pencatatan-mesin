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
use App\Traits\WithAuthentication;

class WaterChillerController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user; // If it's a redirect response

        // Tentukan guard yang sedang aktif
        $currentGuard = $this->getCurrentGuard();

        $query = WaterChillerCheck::orderBy('created_at', 'desc');

        // Filter berdasarkan peran user (Checker hanya bisa melihat data sendiri)
        if ($this->isAuthenticatedAs('checker')) {
            $query->where('checker_id', $user->id);
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
            $query->whereHas('checker', function ($q) use ($request) {
                $q->where('username', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('water_chiller.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        return view('water_chiller.create', compact('user'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Check if record for this date already exists
        $existingRecord = WaterChillerCheck::whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existingRecord) {
            $tanggal = Carbon::parse($request->tanggal)->locale('id')->isoFormat('D MMMM YYYY');
            return redirect()->back()
                ->withInput()
                ->with('error', "Data untuk tanggal {$tanggal} sudah ada!");
        }

        DB::beginTransaction();
        
        try {
            // Create water chiller check record
            $waterChillerCheck = WaterChillerCheck::create([
                'tanggal' => $request->tanggal,
                'hari' => $request->hari,
                'checker_id' => $user->id,
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
            
            // Log activity
            Activity::logActivity(
                'checker',
                $user->id,
                $user->username,
                'created',
                'Checker ' . $user->username . ' Membuat pemeriksaan Water Chiller untuk tanggal ' . 
                Carbon::parse($request->tanggal)->locale('id')->isoFormat('D MMMM YYYY'),
                'water_chiller_check',
                $waterChillerCheck->id,
                [
                    'tanggal' => $request->tanggal,
                    'hari' => $request->hari,
                    'total_mesin' => 32,
                    'catatan' => $request->catatan,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]
            );
            
            DB::commit();
            
            return redirect()->route('water-chiller.index')
                ->with('success', 'Data pemeriksaan Water Chiller berhasil disimpan.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

        $waterChillerCheck = WaterChillerCheck::findOrFail($id);
        $resultsCollection = WaterChillerResult::where('check_id', $id)->get();
        
        $results = [];
        foreach ($resultsCollection as $result) {
            $machineNumber = str_replace('CH', '', $result->no_mesin);
            $results[$machineNumber] = $result;
        }
        
        return view('water_chiller.edit', compact('waterChillerCheck', 'results', 'user', 'currentGuard'));
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
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

        $waterChillerCheck = WaterChillerCheck::findOrFail($id);
        $details = WaterChillerResult::where('check_id', $id)->get();
        
        return view('water_chiller.show', compact('waterChillerCheck', 'details', 'user', 'currentGuard'));
    }

    public function approve(Request $request, $check_id)
    {
        try {
            $user = $this->ensureAuthenticatedUser(['approver']);
            if (!is_object($user)) return $user;

            // Verifikasi bahwa user adalah approver
            if (!$this->isAuthenticatedAs('approver')) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
            }

            $check = WaterChillerCheck::findOrFail($check_id);
            $check->approver_id = $user->id;
            $check->save();

            // Tambahkan log aktivitas
            Activity::logActivity(
                'approver',
                $user->id,
                $user->username,
                'approved',
                'Approver ' . $user->username . ' menyetujui pemeriksaan Water Chiller tanggal ' . 
                Carbon::parse($check->tanggal)->translatedFormat('d F Y'),
                'water_chiller_check',
                $check->id,
                [
                    'tanggal' => $check->tanggal,
                    'checker' => $check->checker?->username,
                    'status' => 'disetujui'
                ]
            );
            
            return redirect()->route('water-chiller.index')
                ->with('success', 'Data berhasil disetujui!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyetujui data: ' . $e->getMessage());
        }
    }

    public function reviewPdf($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

        // Ambil data pemeriksaan water chiller berdasarkan ID
        $waterChiller = WaterChillerCheck::findOrFail($id);

        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/023/REV.01')->firstOrFail();

        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');

        // Ambil detail hasil pemeriksaan untuk water chiller
        $details = WaterChillerResult::where('check_id', $id)->get();

        // Render view sebagai HTML untuk preview PDF
        return view('water_chiller.review_pdf', compact('waterChiller', 'details', 'form', 'formattedTanggalEfektif', 'user', 'currentGuard'));
    }

    public function downloadPdf($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

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
        $html = view('water_chiller.review_pdf', compact('waterChiller', 'details', 'form', 'formattedTanggalEfektif', 'user', 'currentGuard'))->render();
        
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