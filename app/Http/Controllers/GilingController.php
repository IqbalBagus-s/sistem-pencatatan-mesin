<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GilingCheck;
use App\Models\GilingResult;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use App\Traits\WithAuthentication;

class GilingController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

        $query = GilingCheck::query();

        // Filter berdasarkan peran user (Checker hanya bisa melihat data sendiri)
        if ($this->isAuthenticatedAs('checker')) {
            $query->where('checker_id', $user->id);
        }

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('checker', function ($qc) use ($search) {
                    $qc->where('username', 'LIKE', $search);
                })
                ->orWhereHas('approver1', function ($qa1) use ($search) {
                    $qa1->where('username', 'LIKE', $search);
                })
                ->orWhereHas('approver2', function ($qa2) use ($search) {
                    $qa2->where('username', 'LIKE', $search);
                });
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

        return view('giling.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        return view('giling.create', compact('user'));
    }

    public function store(Request $request)
    {
        // Custom error messages untuk validasi
        $customMessages = [
            'minggu.required' => 'Silakan pilih minggu terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!'
        ];

        // Validate basic input dengan custom messages
        $request->validate([
            'minggu' => 'required|in:1,2,3,4',
            'bulan' => 'required|date_format:Y-m',
            'catatan' => 'nullable|string|max:1000',
        ], $customMessages);

        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Check for duplicate record
        $existingCheck = GilingCheck::where('bulan', $request->bulan)
                            ->where('minggu', $request->minggu)
                            ->first();

        if ($existingCheck) {
            // Format bulan dari Y-m menjadi nama bulan dan tahun (contoh: Mei 2025)
            $formattedMonth = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Pesan error dengan detail data duplikat
            $errorMessage = "Data duplikat ditemukan untuk Minggu ke-{$request->minggu} pada Bulan {$formattedMonth}!";
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Create the main Giling Check record
            $gilingCheck = GilingCheck::create([
                'bulan' => $request->bulan,
                'minggu' => $request->minggu,
                'checker_id' => $user->id,
                'keterangan' => $request->catatan ?? '',
            ]);
            
            // Define the checked items
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

            // LOG AKTIVITAS - Tambahkan setelah data berhasil disimpan
            $formattedMonth = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            $mingguText = "Minggu ke-" . $request->minggu;
            
            Activity::logActivity(
                'checker',                                              // user_type
                $user->id,                                             // user_id
                $user->username,                                       // user_name
                'created',                                             // action
                'Checker ' . $user->username . ' membuat pemeriksaan Mesin Giling untuk ' . $mingguText . ' bulan ' . $formattedMonth,  // description
                'giling_check',                                        // target_type
                $gilingCheck->id,                                      // target_id
                [
                    'minggu' => $request->minggu,
                    'bulan' => $request->bulan,
                    'bulan_formatted' => $formattedMonth,
                    'checker_id' => $user->id,
                    'keterangan' => $request->catatan ?? '',
                    'total_items' => count($checkedItems),
                    'items_checked' => array_values($checkedItems),
                    'status' => $gilingCheck->status ?? 'belum_disetujui'
                ]                                                       // details (JSON)
            );

            // Commit the transaction
            DB::commit();
            
            return redirect()->route('giling.index')
                ->with('success', 'Data pemeriksaan mesin giling berhasil disimpan!');

        } catch (\Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

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

        return view('giling.edit', compact('check', 'results', 'user', 'currentGuard'));
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
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

        // Fetch the GilingCheck record with its results
        $check = GilingCheck::with('result')->findOrFail($id);
        
        // Get the associated results and organize into a more usable format
        $results = $check->result->keyBy('checked_items');
        
        // Return the view with the GilingCheck data and its results
        return view('giling.show', compact('check', 'results', 'user', 'currentGuard'));
    }

    public function approve(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'approver_id1' => 'nullable|integer|exists:approvers,id',
            'approver_id2' => 'nullable|integer|exists:approvers,id',
            'approval_date1' => 'nullable|date',
        ]);

        // Find the specified giling check
        $gilingCheck = GilingCheck::findOrFail($id);

        // Update the approval information
        $gilingCheck->update([
            'approver_id1' => $request->input('approver_id1'),
            'approval_date1' => $request->input('approval_date1'),
            'approver_id2' => $request->input('approver_id2'),
        ]);

        // Redirect back with success message
        return redirect()->route('giling.index')
            ->with('success', 'Persetujuan berhasil disimpan!');
    }
    
    public function reviewPdf($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

        // Ambil data pemeriksaan mesin giling berdasarkan ID
        $gilingCheck = GilingCheck::findOrFail($id);

        // Ambil data form terkait
        $form = Form::findOrFail(6); // Sesuaikan ID form untuk mesin giling

        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');

        // Ambil detail hasil pemeriksaan untuk mesin giling
        $details = GilingResult::where('check_id', $id)->get();

        // Render view sebagai HTML untuk preview PDF
        return view('giling.review_pdf', compact('gilingCheck', 'details', 'form', 'formattedTanggalEfektif', 'user', 'currentGuard'));
    }
    
    public function downloadPdf($id)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;

        // Get current guard
        $currentGuard = $this->getCurrentGuard();

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
        $html = view('giling.review_pdf', compact('gilingCheck', 'details', 'form', 'formattedTanggalEfektif', 'user', 'currentGuard'))->render();

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
