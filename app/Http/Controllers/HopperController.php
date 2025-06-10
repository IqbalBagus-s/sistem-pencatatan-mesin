<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HopperCheck;
use App\Models\HopperResult;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF
use App\Traits\WithAuthentication;

class HopperController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $query = HopperCheck::query();

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('checkerMinggu1', function($q) use ($search) {
                $q->where('username', 'LIKE', $search);
            })
            ->orWhereHas('checkerMinggu2', function($q) use ($search) {
                $q->where('username', 'LIKE', $search);
            })
            ->orWhereHas('checkerMinggu3', function($q) use ($search) {
                $q->where('username', 'LIKE', $search);
            })
            ->orWhereHas('checkerMinggu4', function($q) use ($search) {
                $q->where('username', 'LIKE', $search);
            });
        }

        // Filter berdasarkan nomor hopper
        if ($request->filled('search_hopper')) {
            $query->where('nomer_hopper', $request->search_hopper); // Menggunakan filter exact match
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

        return view('hopper.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $checkers = \App\Models\Checker::all();
        return view('hopper.create', compact('user', 'currentGuard', 'checkers'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $customMessages = [
            'nomer_hopper.required' => 'Silakan pilih nomer hopper terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!'
        ];
        
        // Validate the request
        $validatedData = $request->validate([
            'nomer_hopper' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
            
            // Validation for creator fields - now using checker_id_mingguX
            'checker_id_minggu1' => 'nullable|exists:checkers,id',
            'tanggal_minggu1' => 'nullable|date',
            'checker_id_minggu2' => 'nullable|exists:checkers,id',
            'tanggal_minggu2' => 'nullable|date',
            'checker_id_minggu3' => 'nullable|exists:checkers,id',
            'tanggal_minggu3' => 'nullable|date',
            'checker_id_minggu4' => 'nullable|exists:checkers,id',
            'tanggal_minggu4' => 'nullable|date',
            
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
        ], $customMessages);

        // Check for existing record with the same nomer_hopper and bulan
        $existingRecord = HopperCheck::where('nomer_hopper', $request->input('nomer_hopper'))
            ->where('bulan', $request->input('bulan'))
            ->first();

        if ($existingRecord) {
            // Ambil nilai yang duplikat
            $nomerHopper = $request->input('nomer_hopper');
            $bulan = Carbon::parse($request->input('bulan') . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Buat pesan error dengan informasi spesifik
            $pesanError = "Data sudah ada untuk Hopper nomor {$nomerHopper} pada bulan {$bulan}!";
            
            // Redirect dengan pesan error yang detail
            return redirect()->back()->with('error', $pesanError)
                            ->withInput();
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create HopperCheck record - using the correct field names from the form
            $hopperCheck = HopperCheck::create([
                'nomer_hopper' => $request->input('nomer_hopper'),
                'bulan' => $request->input('bulan'),
                
                // Directly use the matching field names from the form
                'tanggal_minggu1' => $request->input('tanggal_minggu1'),
                'tanggal_minggu2' => $request->input('tanggal_minggu2'),
                'tanggal_minggu3' => $request->input('tanggal_minggu3'),
                'tanggal_minggu4' => $request->input('tanggal_minggu4'),
                
                // Directly use the matching field names from the form
                'checker_id_minggu1' => $request->input('checker_id_minggu1'),
                'checker_id_minggu2' => $request->input('checker_id_minggu2'),
                'checker_id_minggu3' => $request->input('checker_id_minggu3'),
                'checker_id_minggu4' => $request->input('checker_id_minggu4'),
            ]);

            // Prepare and create HopperResult records
            $checkedItems = $request->input('checked_items');
            
            // Array untuk menyimpan detail items yang diproses (untuk activity log)
            $itemsProcessed = [];
            
            foreach ($checkedItems as $index => $item) {
                $resultData = [
                    'check_id' => $hopperCheck->id,
                    'checked_items' => $item,
                    
                    // Week 1 data
                    'minggu1' => $request->input("check_1.{$index}", null),
                    'keterangan_minggu1' => $request->input("keterangan_1.{$index}", null),
                    
                    // Week 2 data
                    'minggu2' => $request->input("check_2.{$index}", null),
                    'keterangan_minggu2' => $request->input("keterangan_2.{$index}", null),
                    
                    // Week 3 data
                    'minggu3' => $request->input("check_3.{$index}", null),
                    'keterangan_minggu3' => $request->input("keterangan_3.{$index}", null),
                    
                    // Week 4 data
                    'minggu4' => $request->input("check_4.{$index}", null),
                    'keterangan_minggu4' => $request->input("keterangan_4.{$index}", null),
                ];
                
                HopperResult::create($resultData);
                
                // Simpan detail untuk activity log
                $itemsProcessed[] = [
                    'item' => $item,
                    'minggu1' => $resultData['minggu1'],
                    'minggu2' => $resultData['minggu2'],
                    'minggu3' => $resultData['minggu3'],
                    'minggu4' => $resultData['minggu4'],
                    'keterangan_minggu1' => $resultData['keterangan_minggu1'],
                    'keterangan_minggu2' => $resultData['keterangan_minggu2'],
                    'keterangan_minggu3' => $resultData['keterangan_minggu3'],
                    'keterangan_minggu4' => $resultData['keterangan_minggu4'],
                ];
            }

            // LOG AKTIVITAS - Tambahkan setelah data berhasil disimpan
            $bulanFormatted = Carbon::parse($request->input('bulan') . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Kumpulkan tanggal dan checker untuk setiap minggu
            $weeklyData = [];
            for ($i = 1; $i <= 4; $i++) {
                $date = $request->input("tanggal_minggu{$i}");
                $checkerId = $request->input("checker_id_minggu{$i}");
                $checker = $checkerId ? \App\Models\Checker::find($checkerId) : null;
                $checkerName = $checker ? $checker->username : null;
                if ($date || $checkerName) {
                    $weeklyData["minggu_{$i}"] = [
                        'tanggal' => $date ? Carbon::parse($date)->locale('id')->isoFormat('D MMMM YYYY') : null,
                        'checker' => $checkerName
                    ];
                }
            }
            
            // Buat string deskripsi untuk tanggal
            $tanggalString = [];
            foreach ($weeklyData as $minggu => $data) {
                if ($data['tanggal']) {
                    $tanggalString[] = ucfirst(str_replace('_', ' ', $minggu)) . ': ' . $data['tanggal'];
                }
            }
            $tanggalDescription = !empty($tanggalString) ? implode(', ', $tanggalString) : 'Tidak ada tanggal pemeriksaan';
            
            Activity::logActivity(
                'checker',                                              // user_type
                $user->id,                                       // user_id
                $user->username,                                 // user_name
                'created',                                              // action
                'Checker ' . $user->username . ' membuat pemeriksaan Hopper Nomor ' . $request->input('nomer_hopper') . ' untuk bulan ' . $bulanFormatted,  // description
                'hopper_check',                                         // target_type
                $hopperCheck->id,                                       // target_id
                [
                    'nomer_hopper' => $request->input('nomer_hopper'),
                    'bulan' => $request->input('bulan'),
                    'bulan_formatted' => $bulanFormatted,
                    'weekly_data' => $weeklyData,
                    'total_items' => count($checkedItems),
                    'items_processed' => $itemsProcessed,
                    'total_weeks_filled' => count($weeklyData),
                    'status' => $hopperCheck->status ?? 'belum_disetujui'
                ]                                                       // details (JSON)
            );

            // Commit the transaction
            DB::commit();

            // Log untuk debugging
            Log::info('Transaksi hopper berhasil disimpan dengan ID: ' . $hopperCheck->id);

            // Redirect with success message
            return redirect()->route('hopper.index')->with('success', 'Data Pencatatan sudah tersimpan!');
            
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Log error detail untuk debugging
            Log::error('Error saat menyimpan data hopper: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));

            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to save hopper check data: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function edit($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model HopperCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $hopperCheck = (new HopperCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $hopperCheck->load([
            'results',
            // Tambahkan relasi untuk checker dan approver setiap minggu
            'checkerMinggu1',
            'checkerMinggu2', 
            'checkerMinggu3',
            'checkerMinggu4',
            'approverMinggu1',
            'approverMinggu2',
            'approverMinggu3', 
            'approverMinggu4'
        ]);
        
        // Get the associated results
        $hopperResults = $hopperCheck->results;

        // Return the view and pass both $hopperCheck and $hopperResults
        return view('hopper.edit', compact('hopperCheck', 'hopperResults', 'user', 'currentGuard'));
    }

    public function update(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Validasi hanya untuk field yang dibutuhkan, tidak perlu validasi semua field
        $validatedData = $request->validate([
            'nomer_hopper' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Model HopperCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $hopperCheck = (new HopperCheck)->resolveRouteBinding($hashid);

        // Check for existing record with the same nomer_hopper and bulan, excluding the current record
        $existingRecord = HopperCheck::where('nomer_hopper', $request->input('nomer_hopper'))
            ->where('bulan', $request->input('bulan'))
            ->where('id', '!=', $hopperCheck->id)
            ->first();

        if ($existingRecord) {
            // If a record with the same hopper number and month exists, return an error
            return redirect()->back()->with('error', 'Data untuk nomor hopper dan bulan ini sudah ada.')
                            ->withInput();
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Update HopperCheck record - hanya perbarui nilai yang tidak disetujui
            $updateData = [
                'nomer_hopper' => $request->input('nomer_hopper'),
                'bulan' => $request->input('bulan'),
            ];
            
            // Update data checked_by dan tanggal hanya jika minggu tersebut belum disetujui
            for ($j = 1; $j <= 4; $j++) {
                if (!$hopperCheck->{'approver_id_minggu'.$j} || $hopperCheck->{'approver_id_minggu'.$j} == '-') {
                    $updateData['checker_id_minggu'.$j] = $request->input('checker_id_minggu'.$j);
                    $updateData['tanggal_minggu'.$j] = $request->input('tanggal_minggu'.$j);
                }
            }

            $hopperCheck->update($updateData);

            // Mendapatkan semua data hasil yang ada saat ini
            $existingResults = HopperResult::where('check_id', $hopperCheck->id)
                ->get()
                ->keyBy('checked_items');
            
            // Prepare and update/create HopperResult records
            $checkedItems = $request->input('checked_items');
            $processedItems = [];
            
            foreach ($checkedItems as $index => $item) {
                // Cek apakah item sudah ada di database
                if (isset($existingResults[$item])) {
                    // Update record yang sudah ada
                    $existingResult = $existingResults[$item];
                    $resultData = [];
                    
                    // Proses data untuk setiap minggu
                    for ($j = 1; $j <= 4; $j++) {
                        if (!$hopperCheck->{'approver_id_minggu'.$j} || $hopperCheck->{'approver_id_minggu'.$j} == '-') {
                            $resultData['minggu'.$j] = $request->input("check_{$j}.{$index}", null);
                            $resultData['keterangan_minggu'.$j] = $request->input("keterangan_{$j}.{$index}", null);
                        }
                        // Jika sudah disetujui, data lama akan tetap dipertahankan
                    }
                    
                    // Hanya update jika ada data yang perlu diubah
                    if (!empty($resultData)) {
                        $existingResult->update($resultData);
                    }
                } else {
                    // Membuat record baru jika belum ada
                    $resultData = [
                        'check_id' => $hopperCheck->id,
                        'checked_items' => $item,
                    ];
                    
                    // Proses data untuk setiap minggu
                    for ($j = 1; $j <= 4; $j++) {
                        $resultData['minggu'.$j] = $request->input("check_{$j}.{$index}", null);
                        $resultData['keterangan_minggu'.$j] = $request->input("keterangan_{$j}.{$index}", null);
                    }
                    
                    HopperResult::create($resultData);
                }
                
                $processedItems[] = $item;
            }
            
            // Hapus record yang tidak ada lagi dalam daftar checked_items
            if (!empty($processedItems)) {
                HopperResult::where('check_id', $hopperCheck->id)
                    ->whereNotIn('checked_items', $processedItems)
                    ->delete();
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->route('hopper.index')->with('success', 'Data pencatatan mesin Hopper berhasil diperbarui!');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Redirect back with error message
            return redirect()->back()->with('error', 'Gagal memperbarui data pencatatan mesin Hopper: ' . $e->getMessage());
        }
    }

    public function show($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model HopperCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $hopperRecord = (new HopperCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $hopperRecord->load([
            'checkerMinggu1', 'checkerMinggu2', 'checkerMinggu3', 'checkerMinggu4',
            'approverMinggu1', 'approverMinggu2', 'approverMinggu3', 'approverMinggu4',
        ]);

        // Collect unique checker usernames
        $checkerUsernames = collect([
            optional($hopperRecord->checkerMinggu1)->username,
            optional($hopperRecord->checkerMinggu2)->username,
            optional($hopperRecord->checkerMinggu3)->username,
            optional($hopperRecord->checkerMinggu4)->username,
        ])->filter()->unique()->values();
        $hopperRecord->unique_checkers = $checkerUsernames->implode(', ');

        // Collect unique approver usernames
        $approverUsernames = collect([
            optional($hopperRecord->approverMinggu1)->username,
            optional($hopperRecord->approverMinggu2)->username,
            optional($hopperRecord->approverMinggu3)->username,
            optional($hopperRecord->approverMinggu4)->username,
        ])->filter()->unique()->values();
        $hopperRecord->unique_approvers = $approverUsernames->implode(', ');

        // Prepare the checked items
        $items = [
            1 => 'Filter',
            2 => 'Selang', 
            3 => 'Kontraktor',
            4 => 'Temperatur Kontrol',
            5 => 'MCB'
        ];

        // Fetch associated results
        $hopperResults = HopperResult::where('check_id', $hopperRecord->id)->get()->keyBy('checked_items');

        // Prepare check and keterangan arrays for each week
        $weekFields = [
            'check_1' => 'minggu1',
            'check_2' => 'minggu2',
            'check_3' => 'minggu3',
            'check_4' => 'minggu4',
            'keterangan_1' => 'keterangan_minggu1',
            'keterangan_2' => 'keterangan_minggu2',
            'keterangan_3' => 'keterangan_minggu3',
            'keterangan_4' => 'keterangan_minggu4'
        ];

        // Create a new array to store the modified data dengan mapping yang jelas
        $viewData = [
            'id' => $hopperRecord->id,
            'hashid' => $hopperRecord->hashid, // Add hashid property
            'nomer_hopper' => $hopperRecord->nomer_hopper,
            'bulan' => $hopperRecord->bulan,
            'unique_checkers' => $hopperRecord->unique_checkers,
            'unique_approvers' => $hopperRecord->unique_approvers,
        ];

        // Add checker and approver data for each week
        for ($i = 1; $i <= 4; $i++) {
            $viewData['checker_id_minggu'.$i] = $hopperRecord->{'checker_id_minggu'.$i};
            $viewData['tanggal_minggu'.$i] = $hopperRecord->{'tanggal_minggu'.$i};
            $viewData['approver_id_minggu'.$i] = $hopperRecord->{'approver_id_minggu'.$i};
            $viewData['checker_username_minggu'.$i] = optional($hopperRecord->{'checkerMinggu'.$i})->username;
            $viewData['approver_username_minggu'.$i] = optional($hopperRecord->{'approverMinggu'.$i})->username;
        }

        // Dynamically populate the arrays
        foreach ($weekFields as $recordKey => $dbField) {
            $viewData[$recordKey] = [];
            foreach ($items as $index => $item) {
                $viewData[$recordKey][$index] = optional($hopperResults->get($item))->$dbField ?? '';
            }
        }

        // Convert back to an object for view compatibility
        $viewData = (object) $viewData;

        return view('hopper.show', [
            'hopperRecord' => $viewData,
            'items' => $items,
            'user' => $user,
            'currentGuard' => $currentGuard
        ]);
    }

    public function approve(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser(['approver']);
        if (!is_object($user)) return $user;
        if (!$this->isAuthenticatedAs('approver')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
        }
        
        // Validate the request - hanya validasi field yang dikirim dalam request
        $validatedData = $request->validate([
            'approver_id_minggu1' => 'nullable|exists:approvers,id',
            'approver_id_minggu2' => 'nullable|exists:approvers,id',
            'approver_id_minggu3' => 'nullable|exists:approvers,id',
            'approver_id_minggu4' => 'nullable|exists:approvers,id'
        ]);

        // Model HopperCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $hopperRecord = (new HopperCheck)->resolveRouteBinding($hashid);

        // Hanya update field yang ada dalam request
        foreach ($validatedData as $field => $value) {
            if ($request->has($field)) {
                $hopperRecord->{$field} = $value;
            }
        }

        // Save the record
        $hopperRecord->save();

        // Redirect back with a success message
        return redirect()->route('hopper.index')
            ->with('success', 'Persetujuan berhasil disimpan!');
    }

    public function reviewPdf($hashid) 
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Gunakan trait untuk mendapatkan model berdasarkan hashid
        $hopperCheck = app(HopperCheck::class)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait
        $form = Form::findOrFail(7); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk hopper dan urutkan berdasarkan item terperiksa
        $hopperResults = HopperResult::where('check_id', $hopperCheck->id)->get()->keyBy('checked_items');
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Filter',
            2 => 'Selang',
            3 => 'Kontraktor',
            4 => 'Temperatur Kontrol',
            5 => 'MCB'
        ];
        
        // Debug: Cek struktur data hopperResults
        // dd($hopperResults); // Uncomment untuk debugging
        
        // Siapkan semua field check dan keterangan untuk empat minggu
        for ($j = 1; $j <= 4; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per minggu
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari hopperResults
            foreach ($items as $i => $item) {
                $result = $hopperResults->get($item);
                
                // Pastikan data ada dan tidak null
                if ($result) {
                    ${'check_' . $j}[$i] = $result->{'minggu' . $j} ?? '-';
                    ${'keterangan_' . $j}[$i] = $result->{'keterangan_minggu' . $j} ?? '';
                } else {
                    ${'check_' . $j}[$i] = '-';
                    ${'keterangan_' . $j}[$i] = '';
                }
            }
            
            // Tambahkan array ke hopperCheck object
            $hopperCheck->{'check_' . $j} = ${'check_' . $j};
            $hopperCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
            
            // Set status checker untuk setiap minggu
            // Periksa apakah ada data yang terisi untuk minggu ini
            $hasData = false;
            foreach (${'check_' . $j} as $checkValue) {
                if ($checkValue !== '-' && $checkValue !== '') {
                    $hasData = true;
                    break;
                }
            }
            $hopperCheck->{'checked_by_minggu' . $j} = $hasData ? 'checked' : '';
        }
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('hopper.review_pdf', [
            'hopperCheck' => $hopperCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items,
            'user' => $user,
            'currentGuard' => $currentGuard
        ]);
        
        // Return view untuk preview
        return $view;
    }

    public function downloadPdf($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Gunakan trait untuk mendapatkan model berdasarkan hashid
        $hopperCheck = app(HopperCheck::class)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait
        $form = Form::findOrFail(7); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk hopper dan urutkan berdasarkan item terperiksa
        $hopperResults = HopperResult::where('check_id', $hopperCheck->id)->get()->keyBy('checked_items');
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Filter',
            2 => 'Selang',
            3 => 'Kontraktor',
            4 => 'Temperatur Kontrol',
            5 => 'MCB'
        ];
        
        // Siapkan semua field check dan keterangan untuk empat minggu
        for ($j = 1; $j <= 4; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per minggu
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari hopperResults
            foreach ($items as $i => $item) {
                $result = $hopperResults->get($item);
                
                // Pastikan data ada dan tidak null - sesuai dengan reviewPdf
                if ($result) {
                    ${'check_' . $j}[$i] = $result->{'minggu' . $j} ?? '-';
                    ${'keterangan_' . $j}[$i] = $result->{'keterangan_minggu' . $j} ?? '';
                } else {
                    ${'check_' . $j}[$i] = '-';
                    ${'keterangan_' . $j}[$i] = '';
                }
            }
            
            // Tambahkan array ke hopperCheck object
            $hopperCheck->{'check_' . $j} = ${'check_' . $j};
            $hopperCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
            
            // Set status checker untuk setiap minggu - sesuai dengan reviewPdf
            // Periksa apakah ada data yang terisi untuk minggu ini
            $hasData = false;
            foreach (${'check_' . $j} as $checkValue) {
                if ($checkValue !== '-' && $checkValue !== '') {
                    $hasData = true;
                    break;
                }
            }
            $hopperCheck->{'checked_by_minggu' . $j} = $hasData ? 'checked' : '';
        }
        
        // Format tanggal dari model HopperCheck untuk mendapatkan bulan dan tahun
        $tanggal = new \DateTime($hopperCheck->tanggal);
        $bulan = $tanggal->format('F');
        $tahun = $tanggal->format('Y');
        
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
        $bulanFormatted = $bulanIndonesia[$bulan] ?? $bulan;
        
        // Generate nama file PDF dengan format Hopper_nomer_1_bulan_Mei_2025
        $filename = 'Hopper_nomer_' . $hopperCheck->nomer_hopper . '_bulan_' . $bulanFormatted . '_' . $tahun . '.pdf';
        
        // Render view sebagai HTML
        $html = view('hopper.review_pdf', [
            'hopperCheck' => $hopperCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items,
            'user' => $user,
            'currentGuard' => $currentGuard
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
