<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AirDryerCheck;
use App\Models\AirDryerResult;
use App\Models\Form;
use App\Models\Activity;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\WithAuthentication;

class AirDryerController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user; // If it's a redirect response

        // Tentukan guard yang sedang aktif
        $currentGuard = $this->getCurrentGuard();

        $query = AirDryerCheck::with(['checker', 'approver'])->orderBy('created_at', 'desc');

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

        return view('air_dryer.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user; // If it's a redirect response

        return view('air_dryer.create', compact('user'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
        ]);

        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user; // If it's a redirect response

        // Check if record for this date already exists
        $existingRecord = AirDryerCheck::whereDate('tanggal', $request->tanggal)
            ->first();
        
        if ($existingRecord) {
            $formattedDate = Carbon::parse($request->tanggal)->locale('id')->isoFormat('D MMMM YYYY');
            return redirect()->back()
                ->withInput()
                ->with('error', "Data untuk tanggal $formattedDate tersebut sudah ada!");
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Simpan data pemeriksaan utama
            $airDryerCheck = AirDryerCheck::create([
                'tanggal' => $request->tanggal,
                'hari' => $request->hari,
                'checker_id' => $user->id, // gunakan ID checker
                'keterangan' => $request->catatan,
            ]);

            // Simpan detail untuk setiap mesin
            for ($i = 1; $i <= 8; $i++) {
                AirDryerResult::create([
                    'check_id' => $airDryerCheck->id,
                    'nomor_mesin' => 'AD' . $i,
                    'temperatur_kompresor' => $request->input('temperatur_kompresor.' . $i),
                    'temperatur_kabel' => $request->input('temperatur_kabel.' . $i),
                    'temperatur_mcb' => $request->input('temperatur_mcb.' . $i),
                    'temperatur_angin_in' => $request->input('temperatur_angin_in.' . $i),
                    'temperatur_angin_out' => $request->input('temperatur_angin_out.' . $i),
                    'evaporator' => $request->input('evaporator.' . $i),
                    'fan_evaporator' => $request->input('fan_evaporator.' . $i),
                    'auto_drain' => $request->input('auto_drain.' . $i),
                ]);
            }

            // LOG AKTIVITAS
            $formattedDate = Carbon::parse($request->tanggal)->locale('id')->isoFormat('D MMMM YYYY');
            Activity::logActivity(
                'checker',
                $user->id,
                $user->username,
                'created',
                'Checker ' . $user->username . ' membuat pemeriksaan Air Dryer untuk tanggal ' . $formattedDate,
                'air_dryer_check',
                $airDryerCheck->id,
                [
                    'tanggal' => $request->tanggal,
                    'hari' => $request->hari,
                    'total_mesin' => 8,
                    'keterangan' => $request->catatan ?? 'Tidak ada catatan',
                    'status' => $airDryerCheck->status
                ]
            );

            DB::commit();
            return redirect()->route('air-dryer.index')
                ->with('success', 'Data pemeriksaan Air Dryer berhasil disimpan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(AirDryerCheck $airDryer)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user; // If it's a redirect response

        $details = AirDryerResult::where('check_id', $airDryer->id)->get();
        
        return view('air_dryer.edit', compact('airDryer', 'details', 'user'));
    }
    
    public function update(Request $request, AirDryerCheck $airDryer)
    {
        // Validasi input (tanpa tanggal dan hari)
        $request->validate([
            'catatan' => 'nullable|string',
        ]);
        
        // Mulai transaksi database
        DB::beginTransaction();
        
        try {
            // Hanya update keterangan/catatan
            $airDryer->update([
                'keterangan' => $request->catatan,
            ]);
            
            // Update detail untuk setiap mesin
            for ($i = 1; $i <= 8; $i++) {
                AirDryerResult::updateOrCreate(
                    [
                        'check_id' => $airDryer->id, // Menggunakan ID dari model instance
                        'nomor_mesin' => 'AD' . $i
                    ],
                    [
                        'temperatur_kompresor' => $request->input('temperatur_kompresor.' . $i),
                        'temperatur_kabel' => $request->input('temperatur_kabel.' . $i),
                        'temperatur_mcb' => $request->input('temperatur_mcb.' . $i),
                        'temperatur_angin_in' => $request->input('temperatur_angin_in.' . $i),
                        'temperatur_angin_out' => $request->input('temperatur_angin_out.' . $i),
                        'evaporator' => $request->input('evaporator.' . $i),
                        'fan_evaporator' => $request->input('fan_evaporator.' . $i),
                        'auto_drain' => $request->input('auto_drain.' . $i),
                    ]
                );
            }
            
            // Commit transaksi jika semua operasi berhasil
            DB::commit();
            
            return redirect()->route('air-dryer.index')
                ->with('success', 'Data pemeriksaan Air Dryer berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(AirDryerCheck $airDryer)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user; // If it's a redirect response

        // Menggunakan $airDryer->id karena Laravel sudah resolve hashid ke model instance
        $details = AirDryerResult::where('check_id', $airDryer->id)->get();
        return view('air_dryer.show', compact('airDryer', 'details', 'user'));
    }

    public function approve(Request $request, AirDryerCheck $airDryer)
    {
        try {
            $user = $this->ensureAuthenticatedUser(['approver']);
            if (!is_object($user)) return $user; // If it's a redirect response

            // Verifikasi bahwa user adalah approver
            if (!$this->isAuthenticatedAs('approver')) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
            }

            // Menggunakan $airDryer langsung karena sudah resolved dari hashid
            $airDryer->approve($user->id);
            
            return redirect()->route('air-dryer.index')
                ->with('success', 'Data pemeriksaan Air Dryer berhasil disetujui!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyetujui data: ' . $e->getMessage());
        }
    }

    public function reviewPdf(AirDryerCheck $airDryer)
    {
        $form = Form::findOrFail(4); 
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Menggunakan $airDryer->id karena Laravel sudah resolve hashid ke model instance
        $details = AirDryerResult::where('check_id', $airDryer->id)->get();
        
        $view = view('air_dryer.review_pdf', compact('airDryer', 'details', 'form', 'formattedTanggalEfektif'));
        return $view;
    }

    public function downloadPdf(AirDryerCheck $airDryer)
    {
        $form = Form::findOrFail(4); 
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Menggunakan $airDryer->id karena Laravel sudah resolve hashid ke model instance
        $details = AirDryerResult::where('check_id', $airDryer->id)->get();
        
        // Format tanggal untuk nama file
        $tanggal = new \DateTime($airDryer->tanggal);
        $tanggalFormatted = $tanggal->format('d_F_Y');
        
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
        
        foreach ($bulanIndonesia as $english => $indonesia) {
            $tanggalFormatted = str_replace($english, $indonesia, $tanggalFormatted);
        }
        
        $filename = 'Airdryer_tanggal_' . $tanggalFormatted . '.pdf';
        
        // Generate HTML untuk PDF
        $html = view('air_dryer.review_pdf', compact('airDryer', 'details', 'form', 'formattedTanggalEfektif'))->render();
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        return $dompdf->stream($filename, [
            'Attachment' => false,
        ]);
    }

}
