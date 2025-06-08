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

    public function edit(WaterChillerCheck $waterChillerCheck)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

        $resultsCollection = WaterChillerResult::where('check_id', $waterChillerCheck->id)->get();
        $results = [];
        foreach ($resultsCollection as $result) {
            $machineNumber = str_replace('CH', '', $result->no_mesin);
            $results[$machineNumber] = $result;
        }
        return view('water_chiller.edit', compact('waterChillerCheck', 'results', 'user', 'currentGuard'));
    }

    public function update(Request $request, WaterChillerCheck $waterChillerCheck)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'catatan' => 'nullable|string',
        ]);
        DB::beginTransaction();
        try {
            $waterChillerCheck->update([
                'tanggal' => $request->tanggal,
                'hari' => $request->hari,
                'keterangan' => $request->catatan,
            ]);
            for ($i = 1; $i <= 32; $i++) {
                WaterChillerResult::updateOrCreate(
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
            DB::commit();
            return redirect()->route('water-chiller.index')
                ->with('success', 'Data pemeriksaan Water Chiller berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(WaterChillerCheck $waterChillerCheck)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $details = WaterChillerResult::where('check_id', $waterChillerCheck->id)->get();
        return view('water_chiller.show', compact('waterChillerCheck', 'details', 'user', 'currentGuard'));
    }

    public function approve(Request $request, WaterChillerCheck $waterChillerCheck)
    {
        try {
            $user = $this->ensureAuthenticatedUser(['approver']);
            if (!is_object($user)) return $user;
            if (!$this->isAuthenticatedAs('approver')) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
            }
            $waterChillerCheck->approver_id = $user->id;
            $waterChillerCheck->save();
            Activity::logActivity(
                'approver',
                $user->id,
                $user->username,
                'approved',
                'Approver ' . $user->username . ' menyetujui pemeriksaan Water Chiller tanggal ' . 
                Carbon::parse($waterChillerCheck->tanggal)->translatedFormat('d F Y'),
                'water_chiller_check',
                $waterChillerCheck->id,
                [
                    'tanggal' => $waterChillerCheck->tanggal,
                    'checker' => $waterChillerCheck->checker?->username,
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

    public function reviewPdf(WaterChillerCheck $waterChillerCheck)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $form = Form::where('nomor_form', 'APTEK/023/REV.01')->firstOrFail();
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        $details = WaterChillerResult::where('check_id', $waterChillerCheck->id)->get();
        return view('water_chiller.review_pdf', compact('waterChillerCheck', 'details', 'form', 'formattedTanggalEfektif', 'user', 'currentGuard'));
    }

    public function downloadPdf(WaterChillerCheck $waterChillerCheck)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $form = Form::where('nomor_form', 'APTEK/023/REV.01')->firstOrFail();
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        $details = WaterChillerResult::where('check_id', $waterChillerCheck->id)->get();
        $tanggal = new \DateTime($waterChillerCheck->tanggal);
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
        $filename = 'Waterchiller_tanggal_' . $tanggalFormatted . '.pdf';
        $html = view('water_chiller.review_pdf', compact('waterChillerCheck', 'details', 'form', 'formattedTanggalEfektif', 'user', 'currentGuard'))->render();
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        return $dompdf->stream($filename, [
            'Attachment' => false,
        ]);
    }
}