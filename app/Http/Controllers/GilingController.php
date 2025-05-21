<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GilingCheck;
use App\Models\GilingResult;
use App\Models\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF

class GilingController extends Controller
{
    public function index(Request $request)
    {
        $query = GilingCheck::query();

        // Filter berdasarkan peran user (Checker hanya bisa melihat data sendiri)
        if (Auth::user() instanceof \App\Models\Checker) {
            $query->where('checked_by', Auth::user()->username);
        }

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('checked_by', 'LIKE', $search)
                ->orWhere('approved_by1', 'LIKE', $search)
                ->orWhere('approved_by2', 'LIKE', $search);
            });
        }

        // Filter berdasarkan minggu - perbaikan karena di database hanya angka
        if ($request->filled('minggu')) {
            // Ekstrak angka dari string "Minggu X"
            $mingguNum = (int) filter_var($request->minggu, FILTER_SANITIZE_NUMBER_INT);
            $query->where('minggu', $mingguNum);
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

        return view('giling.index', compact('checks'));
    }

    public function create()
    {
        return view('giling.create');
    }

    public function store(Request $request)
    {
        // Validate basic input
        $request->validate([
            'minggu' => 'required|in:1,2,3,4',
            'bulan' => 'required|date_format:Y-m',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Check if the combination of month and week already exists
        $existingCheck = GilingCheck::where('bulan', $request->bulan)
                            ->where('minggu', $request->minggu)
                            ->first();

        if ($existingCheck) {
            // Get the specific values that are duplicated
            $minggu = $request->minggu;
            $bulan = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Create a more descriptive error message
            $pesanError = "Data pemeriksaan untuk Minggu ke-{$minggu} pada bulan {$bulan} sudah ada!";
            
            return redirect()->back()
                ->with('error', $pesanError)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the main Giling Check record
            $gilingCheck = GilingCheck::create([
                'bulan' => $request->bulan,
                'minggu' => $request->minggu,
                'checked_by' => Auth::user()->username,
                'keterangan' => $request->catatan ?? '',
            ]);
            
            // Mapping of form input names to database column names
            $checkedItems = [
                'cek_motor_mesin_giling' => 'Cek Motor Mesin Giling',
                'cek_vanbelt' => 'Cek Vanbelt',
                'cek_dustcollector' => 'Cek Dustcollector',
                'cek_safety_switch' => 'Cek Safety Switch',
                'cek_ketajaman_pisau_putar_dan_pisau_duduk' => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
            ];
            
            // Process and save results for each checked item
            foreach ($checkedItems as $dbColumn => $itemName) {
                // Get the input for this item
                $itemInputs = $request->input(Str::snake($itemName), []);
                
                // Prepare result data
                $resultData = [
                    'check_id' => $gilingCheck->id,
                    'checked_items' => $itemName,
                    'g1' => $itemInputs['G1'] ?? '-',
                    'g2' => $itemInputs['G2'] ?? '-',
                    'g3' => $itemInputs['G3'] ?? '-',
                    'g4' => $itemInputs['G4'] ?? '-',
                    'g5' => $itemInputs['G5'] ?? '-',
                    'g6' => $itemInputs['G6'] ?? '-',
                    'g7' => $itemInputs['G7'] ?? '-',
                    'g8' => $itemInputs['G8'] ?? '-',
                    'g9' => $itemInputs['G9'] ?? '-',
                    'g10' => $itemInputs['G10'] ?? '-',
                ];
                
                // Create result record
                GilingResult::create($resultData);
            }

            DB::commit();
            return redirect()->route('giling.index')
                ->with('success', 'Data pemeriksaan mesin giling berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Giling Check Save Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $check = GilingCheck::with('result')->findOrFail($id);

        $results = [];

        $items = [
            'cek_motor_mesin_giling' => 'Cek Motor Mesin Giling',
            'cek_vanbelt' => 'Cek Vanbelt',
            'cek_dustcollector' => 'Cek Dustcollector',
            'cek_safety_switch' => 'Cek Safety Switch',
            'cek_ketajaman_pisau_putar_dan_pisau_duduk' => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
        ];

        foreach ($check->result as $result) {
            $baseKey = Str::snake($result->checked_items);

            for ($g = 1; $g <= 10; $g++) {
                $key = "{$baseKey}_G{$g}";
                $results[$key] = [
                    'status' => $result->{"g{$g}"} ?? '-'
                ];
            }
        }

        return view('giling.edit', [
            'check' => $check,
            'results' => $results
        ]);
    }

    public function update(Request $request, $id) 
    {
        // Find the GilingCheck record
        $check = GilingCheck::findOrFail($id);
        
        // Update only the keterangan field, keeping bulan and minggu unchanged
        $check->update([
            'keterangan' => $request->input('catatan'),
            // bulan and minggu are not updated to preserve the original values
        ]);
        
        // Map from field names to checked items
        $itemMappings = [
            'cek_motor_mesin_giling' => 'Cek Motor Mesin Giling',
            'cek_vanbelt' => 'Cek Vanbelt',
            'cek_dustcollector' => 'Cek Dustcollector',
            'cek_safety_switch' => 'Cek Safety Switch',
            'cek_ketajaman_pisau_putar_dan_pisau_duduk' => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
        ];
        
        // Process each check item
        foreach ($itemMappings as $fieldName => $checkedItem) {
            // If the field exists in the request
            if ($request->has($fieldName)) {
                $data = $request->$fieldName;
                
                // Find or create the result for this checked item
                $result = GilingResult::updateOrCreate(
                    [
                        'check_id' => $check->id,
                        'checked_items' => $checkedItem
                    ],
                    [
                        'g1' => $data['G1'] ?? '-',
                        'g2' => $data['G2'] ?? '-',
                        'g3' => $data['G3'] ?? '-',
                        'g4' => $data['G4'] ?? '-',
                        'g5' => $data['G5'] ?? '-',
                        'g6' => $data['G6'] ?? '-',
                        'g7' => $data['G7'] ?? '-',
                        'g8' => $data['G8'] ?? '-',
                        'g9' => $data['G9'] ?? '-',
                        'g10' => $data['G10'] ?? '-',
                    ]
                );
            }
        }
        
        // Redirect with success message
        return redirect()->route('giling.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function show($id)
    {
        // Fetch the GilingCheck record with its results
        $check = GilingCheck::with('result')->findOrFail($id);
        
        // Get the associated results and organize into a more usable format
        $results = $check->result->keyBy('checked_items');
        
        // Return the view with the GilingCheck data and its results
        return view('giling.show', compact('check', 'results'));
    }

    public function approve(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'approved_by1' => 'nullable|string|max:255',
            'approved_by2' => 'nullable|string|max:255',
            'approval_date1' => 'nullable|date',
        ]);

        // Find the specified giling check
        $gilingCheck = GilingCheck::findOrFail($id);

        // Update the approval information
        $gilingCheck->update([
            'approved_by1' => $request->input('approved_by1'),
            'approval_date1' => $request->input('approval_date1'),
            'approved_by2' => $request->input('approved_by2'),
        ]);

        // Redirect back with success message
        return redirect()->route('giling.index')
            ->with('success', 'Persetujuan berhasil disimpan!');
    }
    
    public function reviewPdf($id)
    {
        // Ambil data pemeriksaan mesin giling berdasarkan ID
        $gilingCheck = GilingCheck::findOrFail($id);

        // Ambil data form terkait
        $form = Form::findOrFail(6); // Sesuaikan ID form untuk mesin giling

        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');

        // Ambil detail hasil pemeriksaan untuk mesin giling
        $details = GilingResult::where('check_id', $id)->get();

        // Render view sebagai HTML untuk preview PDF
        $view = view('giling.review_pdf', compact('gilingCheck', 'details', 'form', 'formattedTanggalEfektif'));

        // Return view untuk preview
        return $view;
    }
    
    public function downloadPdf($id)
    {
        // Ambil data pemeriksaan mesin giling berdasarkan ID
        $gilingCheck = GilingCheck::findOrFail($id);

        // Ambil data form terkait
        $form = Form::findOrFail(6); // Pastikan ID form sesuai untuk mesin giling

        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');

        // Ambil semua detail hasil pemeriksaan untuk mesin giling
        $details = GilingResult::where('check_id', $id)->get();

        // Format nama file
        Carbon::setLocale('id'); // Pastikan hasil bulan dalam Bahasa Indonesia
        $carbonBulan = Carbon::parse($gilingCheck->bulan);
        $namaBulan = $carbonBulan->translatedFormat('F Y'); // Contoh: "Mei 2025"

        $filename = 'Giling_minggu_' . $gilingCheck->minggu . '_bulan_' . $namaBulan . '.pdf';

        // Render view sebagai HTML
        $html = view('giling.review_pdf', compact('gilingCheck', 'details', 'form', 'formattedTanggalEfektif'))->render();

        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);

        // Atur ukuran dan orientasi halaman
        $dompdf->setPaper('A4', 'landscape');

        // Render PDF
        $dompdf->render();

        // Stream / preview PDF
        return $dompdf->stream($filename, [
            'Attachment' => false,
        ]);
    }
}
