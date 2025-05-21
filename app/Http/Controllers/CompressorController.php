<?php

namespace App\Http\Controllers;
use App\Models\CompressorCheck;
use App\Models\CompressorResultKh;
use App\Models\CompressorResultKl;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF


use Illuminate\Http\Request;

class CompressorController extends Controller
{
    public function index(Request $request)
    {
        $query = CompressorCheck::orderBy('created_at', 'desc');

        // Filter berdasarkan peran user (Checker hanya bisa melihat data sendiri)
        // if (Auth::user() instanceof \App\Models\Checker) {
        //     $query->where('checked_by_shift1', Auth::user()->username)
        //           ->orWhere('checked_by_shift2', Auth::user()->username);
        // }

        // Filter berdasarkan bulan jika ada
        if ($request->filled('bulan')) {
            $bulan = date('m', strtotime($request->bulan));
            $tahun = date('Y', strtotime($request->bulan));
            $query->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', $tahun);
        }

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('checked_by_shift1', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('checked_by_shift2', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('compressor.index', compact('checks'));
    }

    public function create()
    {
        return view('compressor.create'); 
    }

    public function store(Request $request)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required|string',
            'checked_by_shift1' => 'nullable|string',
            'checked_by_shift2' => 'nullable|string',
            'kompressor_on_kl' => 'nullable|string',
            'kompressor_on_kh' => 'nullable|string',
            'mesin_on' => 'nullable|string',
            'mesin_off' => 'nullable|string',
            'temperatur_shift1' => 'nullable|string',
            'temperatur_shift2' => 'nullable|string',
            'humidity_shift1' => 'nullable|string',
            'humidity_shift2' => 'nullable|string',
        ]);
        
        $tanggal = $request->tanggal;
        $user = Auth::user();

        // Cek jika tanggal sudah ada
        $existing = CompressorCheck::where('tanggal', $tanggal)->first();

        if ($user instanceof \App\Models\Checker) {
            if ($existing) {
                $formattedDate = Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY');

                return redirect()->route('compressor.create')
                    ->with('error', "Data di tanggal $formattedDate tersebut telah dibuat.");
            }
        } else {
            if ($existing) {
                $formattedDate = Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY');

                return redirect()->route('compressor.create')
                    ->with('error', "Data di tanggal $formattedDate tersebut telah dibuat.");
            }
        }
        // Simpan data ke tabel compressor_checks
        $compressorCheck = CompressorCheck::create([
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'checked_by_shift1' => $request->checked_by_shift1,
            'checked_by_shift2' => $request->checked_by_shift2,
            'kompressor_on_kl' => $request->kompressor_on_kl,
            'kompressor_on_kh' => $request->kompressor_on_kh,
            'mesin_on' => $request->mesin_on,
            'mesin_off' => $request->mesin_off,
            'temperatur_shift1' => $request->temperatur_shift1,
            'temperatur_shift2' => $request->temperatur_shift2,
            'humidity_shift1' => $request->humidity_shift1,
            'humidity_shift2' => $request->humidity_shift2,
        ]);

        // Simpan data hasil pemeriksaan Low Kompressor ke tabel compressor_results
        $lowCheckedItems = [
            "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
            "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
            "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
            "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
        ];

        // Untuk Low Kompressor
        foreach ($lowCheckedItems as $index => $item) {
            CompressorResultKl::create([
                'check_id' => $compressorCheck->id,
                'checked_items' => $item,
                'kl_10I' => $request->input("kl_KL_10I")[$index] ?? null,
                'kl_10II' => $request->input("kl_KL_10II")[$index] ?? null,
                'kl_5I' => $request->input("kl_KL_5I")[$index] ?? null,
                'kl_5II' => $request->input("kl_KL_5II")[$index] ?? null,
                'kl_6I' => $request->input("kl_KL_6I")[$index] ?? null,
                'kl_6II' => $request->input("kl_KL_6II")[$index] ?? null,
                'kl_7I' => $request->input("kl_KL_7I")[$index] ?? null,
                'kl_7II' => $request->input("kl_KL_7II")[$index] ?? null,
                'kl_8I' => $request->input("kl_KL_8I")[$index] ?? null,
                'kl_8II' => $request->input("kl_KL_8II")[$index] ?? null,
                'kl_9I' => $request->input("kl_KL_9I")[$index] ?? null,
                'kl_9II' => $request->input("kl_KL_9II")[$index] ?? null
            ]);
        }

        // Simpan data hasil pemeriksaan High Kompressor ke tabel compressor_results
        $highCheckedItems = [
            "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
            "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
            "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
            "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
        ];

        // Untuk High Kompressor
        foreach ($highCheckedItems as $index => $item) {
            CompressorResultKh::create([
                'check_id' => $compressorCheck->id,
                'checked_items' => $item,
                'kh_7I' => $request->input("kh_KH_7I")[$index] ?? null,
                'kh_7II' => $request->input("kh_KH_7II")[$index] ?? null,
                'kh_8I' => $request->input("kh_KH_8I")[$index] ?? null,
                'kh_8II' => $request->input("kh_KH_8II")[$index] ?? null,
                'kh_9I' => $request->input("kh_KH_9I")[$index] ?? null,
                'kh_9II' => $request->input("kh_KH_9II")[$index] ?? null,
                'kh_10I' => $request->input("kh_KH_10I")[$index] ?? null,
                'kh_10II' => $request->input("kh_KH_10II")[$index] ?? null,
                'kh_11I' => $request->input("kh_KH_11I")[$index] ?? null,
                'kh_11II' => $request->input("kh_KH_11II")[$index] ?? null
            ]);
        }

        // Uncomment kode ini untuk debugging
        // dd([
        //     'sample_kl' => $request->input("kl_KL_10I"),
        //     'sample_kh' => $request->input("kh_KH_7I"),
        //     'all_request' => $request->all()
        // ]);

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('compressor.index')->with('success', 'Data berhasil disimpan!');
    }

    public function edit($id)
    {
        // Ambil data check berdasarkan ID
        $check = CompressorCheck::findOrFail($id);
        
        // Ambil data hasil low kompressor
        $lowResults = CompressorResultKl::where('check_id', $id)->get();
        
        // Ambil data hasil high kompressor
        $highResults = CompressorResultKh::where('check_id', $id)->get();
        
        // Tampilkan view edit dengan data yang diperlukan
        return view('compressor.edit', compact('check', 'lowResults', 'highResults'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data yang diterima dari form
        $request->validate([
            'checked_by_shift1' => 'nullable|string',
            'checked_by_shift2' => 'nullable|string',
            'kompressor_on_kl' => 'nullable|string',
            'kompressor_on_kh' => 'nullable|string',
            'mesin_on' => 'nullable|string',
            'mesin_off' => 'nullable|string',
            'temperatur_shift1' => 'nullable|string',
            'temperatur_shift2' => 'nullable|string',
            'humidity_shift1' => 'nullable|string',
            'humidity_shift2' => 'nullable|string',
        ]);
        
        // Cari data compressor check berdasarkan ID
        $compressorCheck = CompressorCheck::findOrFail($id);
        
        // Update data di tabel compressor_checks
        $compressorCheck->update([
            'checked_by_shift1' => $request->checked_by_shift1,
            'checked_by_shift2' => $request->checked_by_shift2,
            'kompressor_on_kl' => $request->kompressor_on_kl,
            'kompressor_on_kh' => $request->kompressor_on_kh,
            'mesin_on' => $request->mesin_on,
            'mesin_off' => $request->mesin_off,
            'temperatur_shift1' => $request->temperatur_shift1,
            'temperatur_shift2' => $request->temperatur_shift2,
            'humidity_shift1' => $request->humidity_shift1,
            'humidity_shift2' => $request->humidity_shift2,
        ]);

        // Daftar item pemeriksaan untuk Low Kompressor
        $lowCheckedItems = [
            "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
            "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
            "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
            "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
        ];

        // Update data hasil pemeriksaan Low Kompressor
        foreach ($lowCheckedItems as $index => $item) {
            $compressorResult = CompressorResultKl::where('check_id', $id)
                ->where('checked_items', $item)
                ->first();
                
            if ($compressorResult) {
                $compressorResult->update([
                    'kl_10I' => $request->input("kl_KL_10I")[$index] ?? null,
                    'kl_10II' => $request->input("kl_KL_10II")[$index] ?? null,
                    'kl_5I' => $request->input("kl_KL_5I")[$index] ?? null,
                    'kl_5II' => $request->input("kl_KL_5II")[$index] ?? null,
                    'kl_6I' => $request->input("kl_KL_6I")[$index] ?? null,
                    'kl_6II' => $request->input("kl_KL_6II")[$index] ?? null,
                    'kl_7I' => $request->input("kl_KL_7I")[$index] ?? null,
                    'kl_7II' => $request->input("kl_KL_7II")[$index] ?? null,
                    'kl_8I' => $request->input("kl_KL_8I")[$index] ?? null,
                    'kl_8II' => $request->input("kl_KL_8II")[$index] ?? null,
                    'kl_9I' => $request->input("kl_KL_9I")[$index] ?? null,
                    'kl_9II' => $request->input("kl_KL_9II")[$index] ?? null
                ]);
            } else {
                // Jika data tidak ditemukan, buat baru
                CompressorResultKl::create([
                    'check_id' => $id,
                    'checked_items' => $item,
                    'kl_10I' => $request->input("kl_KL_10I")[$index] ?? null,
                    'kl_10II' => $request->input("kl_KL_10II")[$index] ?? null,
                    'kl_5I' => $request->input("kl_KL_5I")[$index] ?? null,
                    'kl_5II' => $request->input("kl_KL_5II")[$index] ?? null,
                    'kl_6I' => $request->input("kl_KL_6I")[$index] ?? null,
                    'kl_6II' => $request->input("kl_KL_6II")[$index] ?? null,
                    'kl_7I' => $request->input("kl_KL_7I")[$index] ?? null,
                    'kl_7II' => $request->input("kl_KL_7II")[$index] ?? null,
                    'kl_8I' => $request->input("kl_KL_8I")[$index] ?? null,
                    'kl_8II' => $request->input("kl_KL_8II")[$index] ?? null,
                    'kl_9I' => $request->input("kl_KL_9I")[$index] ?? null,
                    'kl_9II' => $request->input("kl_KL_9II")[$index] ?? null
                ]);
            }
        }

        // Daftar item pemeriksaan untuk High Kompressor
        $highCheckedItems = [
            "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
            "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
            "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
            "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
        ];

        // Update data hasil pemeriksaan High Kompressor
        foreach ($highCheckedItems as $index => $item) {
            $compressorResult = CompressorResultKh::where('check_id', $id)
                ->where('checked_items', $item)
                ->first();
                
            if ($compressorResult) {
                $compressorResult->update([
                    'kh_7I' => $request->input("kh_KH_7I")[$index] ?? null,
                    'kh_7II' => $request->input("kh_KH_7II")[$index] ?? null,
                    'kh_8I' => $request->input("kh_KH_8I")[$index] ?? null,
                    'kh_8II' => $request->input("kh_KH_8II")[$index] ?? null,
                    'kh_9I' => $request->input("kh_KH_9I")[$index] ?? null,
                    'kh_9II' => $request->input("kh_KH_9II")[$index] ?? null,
                    'kh_10I' => $request->input("kh_KH_10I")[$index] ?? null,
                    'kh_10II' => $request->input("kh_KH_10II")[$index] ?? null,
                    'kh_11I' => $request->input("kh_KH_11I")[$index] ?? null,
                    'kh_11II' => $request->input("kh_KH_11II")[$index] ?? null
                ]);
            } else {
                // Jika data tidak ditemukan, buat baru
                CompressorResultKh::create([
                    'check_id' => $id,
                    'checked_items' => $item,
                    'kh_7I' => $request->input("kh_KH_7I")[$index] ?? null,
                    'kh_7II' => $request->input("kh_KH_7II")[$index] ?? null,
                    'kh_8I' => $request->input("kh_KH_8I")[$index] ?? null,
                    'kh_8II' => $request->input("kh_KH_8II")[$index] ?? null,
                    'kh_9I' => $request->input("kh_KH_9I")[$index] ?? null,
                    'kh_9II' => $request->input("kh_KH_9II")[$index] ?? null,
                    'kh_10I' => $request->input("kh_KH_10I")[$index] ?? null,
                    'kh_10II' => $request->input("kh_KH_10II")[$index] ?? null,
                    'kh_11I' => $request->input("kh_KH_11I")[$index] ?? null,
                    'kh_11II' => $request->input("kh_KH_11II")[$index] ?? null
                ]);
            }
        }

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('compressor.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function show($id)
    {
        // Ambil data compressor check berdasarkan ID
        $check = CompressorCheck::findOrFail($id);
        
        // Ambil data low kompressor
        $lowResults = CompressorResultkl::where('check_id', $id)
            ->whereIn('checked_items', [
                "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
            ])
            ->get();
        
        // Ambil data high kompressor
        $highResults = CompressorResultkh::where('check_id', $id)
            ->whereIn('checked_items', [
                "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
            ])
            ->get();
        
        // Tampilkan view dengan data yang diperlukan
        return view('compressor.show', compact('check', 'lowResults', 'highResults'));
    }

    public function approve(Request $request, $id)
    {
        $check = CompressorCheck::findOrFail($id);

        // Update field yang tersedia
        if ($request->shift1) {
            $check->approved_by_shift1 = $request->shift1;
        }

        if ($request->shift2) {
            $check->approved_by_shift2 = $request->shift2;
        }

        $check->save(); // Simpan perubahan ke database

        return redirect()->route('compressor.index')->with('success', 'Persetujuan berhasil disimpan.');
    }

    public function reviewPdf($id) 
    {
        // Ambil data pemeriksaan kompressor berdasarkan ID
        $check = CompressorCheck::findOrFail($id);
        
        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/041/REV.00')->firstOrFail();
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil data low kompressor
        $lowResults = CompressorResultkl::where('check_id', $id)
            ->whereIn('checked_items', [
                "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
            ])
            ->get();
        
        // Ambil data high kompressor
        $highResults = CompressorResultkh::where('check_id', $id)
            ->whereIn('checked_items', [
                "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
            ])
            ->get();
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('compressor.review_pdf', [
            'check' => $check,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'lowResults' => $lowResults,
            'highResults' => $highResults
        ]);
        
        // Return view untuk preview
        return $view;
    }

    public function downloadPdf($id)
    {
        // Ambil data pemeriksaan kompressor berdasarkan ID
        $check = CompressorCheck::findOrFail($id);
        
        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/041/REV.00')->firstOrFail();
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil data low kompressor
        $lowResults = CompressorResultkl::where('check_id', $id)
            ->whereIn('checked_items', [
                "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
            ])
            ->get();
        
        // Ambil data high kompressor
        $highResults = CompressorResultkh::where('check_id', $id)
            ->whereIn('checked_items', [
                "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
            ])
            ->get();
        
        // Format tanggal dari model CompressorCheck
        $tanggal = new \DateTime($check->tanggal);
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
        $filename = 'Compressor_tanggal_' . $tanggalFormatted . '.pdf';
        
        // Render view sebagai HTML
        $html = view('compressor.review_pdf', [
            'check' => $check,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'lowResults' => $lowResults,
            'highResults' => $highResults
        ])->render();
        
        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Atur ukuran dan orientasi halaman
        $dompdf->setPaper('A4', 'landscape');
        
        // Render PDF (mengubah HTML menjadi PDF)
        $dompdf->render();
        
        // Download file PDF
        return $dompdf->stream($filename, [
            'Attachment' => false, // Set true untuk download otomatis
        ]);
    }
}

