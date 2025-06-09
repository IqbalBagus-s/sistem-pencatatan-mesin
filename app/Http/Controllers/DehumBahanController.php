<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DehumBahanCheck;
use App\Models\DehumBahanResult;
use App\Models\Form;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use App\Traits\WithAuthentication;

class DehumBahanController extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $query = DehumBahanCheck::query();

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('checkerMinggu1', function($qc) use ($search) {
                    $qc->where('username', 'LIKE', $search);
                })
                ->orWhereHas('checkerMinggu2', function($qc) use ($search) {
                    $qc->where('username', 'LIKE', $search);
                })
                ->orWhereHas('checkerMinggu3', function($qc) use ($search) {
                    $qc->where('username', 'LIKE', $search);
                })
                ->orWhereHas('checkerMinggu4', function($qc) use ($search) {
                    $qc->where('username', 'LIKE', $search);
                });
            });
        }

        // Filter berdasarkan nomor dehum bahan
        if ($request->filled('search_dehum')) {
            $query->where('nomer_dehum_bahan', $request->search_dehum); // Menggunakan filter exact match
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

        // Urutkan berdasarkan data terbaru
        $query->orderBy('created_at', 'desc');

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        return view('dehum-bahan.index', compact('checks', 'user', 'currentGuard'));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        return view('dehum-bahan.create', compact('user', 'currentGuard'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        $customMessages = [
            'nomer_dehum_bahan.required' => 'Silakan pilih nomor dehum bahan terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!'
        ];
        
        // Validate the request
        $validatedData = $request->validate([
            'nomer_dehum_bahan' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
            
            // Validation for checker fields (ID)
            'checker_id_minggu1' => 'nullable|integer|exists:checkers,id',
            'tanggal_minggu1' => 'nullable|date',
            'checker_id_minggu2' => 'nullable|integer|exists:checkers,id',
            'tanggal_minggu2' => 'nullable|date',
            'checker_id_minggu3' => 'nullable|integer|exists:checkers,id',
            'tanggal_minggu3' => 'nullable|date',
            'checker_id_minggu4' => 'nullable|integer|exists:checkers,id',
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

        // Check for existing record with the same nomer_dehum and bulan
        $existingRecord = DehumBahanCheck::where('nomer_dehum_bahan', $request->input('nomer_dehum_bahan'))
            ->where('bulan', $request->input('bulan'))
            ->first();

        if ($existingRecord) {
            // Create a more specific error message showing which values are duplicated
            $nomerDehum = $request->input('nomer_dehum_bahan');
            $bulan = Carbon::parse($request->bulan . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            $errorMessage = "Data sudah ada untuk Dehum Bahan nomor {$nomerDehum} pada bulan {$bulan}, silahkan buat ulang";
            
            // Return with detailed error message
            return redirect()->back()->with('error', $errorMessage)
                            ->withInput();
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create dehumBahanCheck record
            $dehumBahanCheck = DehumBahanCheck::create([
                'nomer_dehum_bahan' => $request->input('nomer_dehum_bahan'),
                'bulan' => $request->input('bulan'),
                'tanggal_minggu1' => $request->input('tanggal_minggu1'),
                'tanggal_minggu2' => $request->input('tanggal_minggu2'),
                'tanggal_minggu3' => $request->input('tanggal_minggu3'),
                'tanggal_minggu4' => $request->input('tanggal_minggu4'),
                'checker_id_minggu1' => $request->input('checker_id_minggu1'),
                'checker_id_minggu2' => $request->input('checker_id_minggu2'),
                'checker_id_minggu3' => $request->input('checker_id_minggu3'),
                'checker_id_minggu4' => $request->input('checker_id_minggu4'),
            ]);

            // Prepare and create DehumBahanResult records
            $checkedItems = $request->input('checked_items');
            
            // Array untuk menyimpan detail items yang diproses (untuk activity log)
            $itemsProcessed = [];
            
            foreach ($checkedItems as $index => $item) {
                $resultData = [
                    'check_id' => $dehumBahanCheck->id,
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
                
                DehumBahanResult::create($resultData);
                
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
                $date = $request->input("tanggal_minggu_{$i}");
                $checker = $request->input("checker_id_minggu_{$i}");
                
                if ($date || $checker) {
                    $weeklyData["minggu_{$i}"] = [
                        'tanggal' => $date ? Carbon::parse($date)->locale('id')->isoFormat('D MMMM YYYY') : null,
                        'checker' => $checker
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
                'Checker ' . $user->username . ' membuat pemeriksaan Dehum Bahan Nomor ' . $request->input('nomer_dehum_bahan') . ' untuk bulan ' . $bulanFormatted,  // description
                'dehum_bahan_check',                                    // target_type
                $dehumBahanCheck->id,                                   // target_id
                [
                    'nomer_dehum_bahan' => $request->input('nomer_dehum_bahan'),
                    'bulan' => $request->input('bulan'),
                    'bulan_formatted' => $bulanFormatted,
                    'weekly_data' => $weeklyData,
                    'total_items' => count($checkedItems),
                    'items_processed' => $itemsProcessed,
                    'total_weeks_filled' => count($weeklyData),
                    'status' => $dehumBahanCheck->status ?? 'belum_disetujui'
                ]                                                       // details (JSON)
            );

            // Commit the transaction
            DB::commit();

            // Log untuk debugging
            Log::info('Transaksi dehum bahan berhasil disimpan dengan ID: ' . $dehumBahanCheck->id);

            // Redirect with success message
            return redirect()->route('dehum-bahan.index')->with('success', 'Data Pencatatan sudah tersimpan!');
            
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Log error detail untuk debugging
            Log::error('Error saat menyimpan data dehum bahan: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));

            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to save Dehum Bahan check data: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function edit($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model DehumBahanCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $dehumCheck = (new DehumBahanCheck)->resolveRouteBinding($hashid);
        
        $dehumResults = $dehumCheck->results;
        return view('dehum-bahan.edit', compact('dehumCheck', 'dehumResults', 'user', 'currentGuard'));
    }

    public function update(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Validasi data request
        $validatedData = $request->validate([
            'nomer_dehum_bahan' => 'required|integer|min:1',
            'bulan' => 'required|date_format:Y-m',
            'checker_id_minggu1' => 'nullable|integer|exists:checkers,id',
            'tanggal_minggu1' => 'nullable|date',
            'checker_id_minggu2' => 'nullable|integer|exists:checkers,id',
            'tanggal_minggu2' => 'nullable|date',
            'checker_id_minggu3' => 'nullable|integer|exists:checkers,id',
            'tanggal_minggu3' => 'nullable|date',
            'checker_id_minggu4' => 'nullable|integer|exists:checkers,id',
            'tanggal_minggu4' => 'nullable|date',
            'approver_id_minggu1' => 'nullable|integer|exists:approvers,id',
            'approver_id_minggu2' => 'nullable|integer|exists:approvers,id',
            'approver_id_minggu3' => 'nullable|integer|exists:approvers,id',
            'approver_id_minggu4' => 'nullable|integer|exists:approvers,id',
            'checked_items' => 'required|array',
            'check_1' => 'required|array',
            'keterangan_1' => 'nullable|array',
            'check_2' => 'nullable|array',
            'keterangan_2' => 'nullable|array',
            'check_3' => 'nullable|array',
            'keterangan_3' => 'nullable|array',
            'check_4' => 'nullable|array',
            'keterangan_4' => 'nullable|array',
        ]);

        // Gunakan resolveRouteBinding yang sudah ada di trait
        $dehumCheck = (new DehumBahanCheck)->resolveRouteBinding($hashid);

        // Cek apakah ada record lain dengan nomor dehum dan bulan yang sama (kecuali record saat ini)
        $existingRecord = DehumBahanCheck::where('nomer_dehum_bahan', $request->input('nomer_dehum_bahan'))
            ->where('bulan', $request->input('bulan'))
            ->where('id', '!=', $dehumCheck->id)
            ->first();

        if ($existingRecord) {
            // Jika ditemukan record dengan nomor dehum dan bulan yang sama, kembalikan error
            return redirect()->back()->with('error', 'Data dengan nomor dehum dan bulan ini sudah ada.')
                            ->withInput();
        }

        try {
            // Mulai transaksi database
            DB::beginTransaction();

            // Update data utama DehumBahanCheck
            $dehumCheck->update([
                'nomer_dehum_bahan' => $request->input('nomer_dehum_bahan'),
                'bulan' => $request->input('bulan'),
                'tanggal_minggu1' => $request->input('tanggal_minggu1'),
                'tanggal_minggu2' => $request->input('tanggal_minggu2'),
                'tanggal_minggu3' => $request->input('tanggal_minggu3'),
                'tanggal_minggu4' => $request->input('tanggal_minggu4'),
                'checker_id_minggu1' => $request->input('checker_id_minggu1'),
                'checker_id_minggu2' => $request->input('checker_id_minggu2'),
                'checker_id_minggu3' => $request->input('checker_id_minggu3'),
                'checker_id_minggu4' => $request->input('checker_id_minggu4'),
                'approver_id_minggu1' => $request->input('approver_id_minggu1'),
                'approver_id_minggu2' => $request->input('approver_id_minggu2'),
                'approver_id_minggu3' => $request->input('approver_id_minggu3'),
                'approver_id_minggu4' => $request->input('approver_id_minggu4'),
            ]);

            // Ambil existing DehumBahanResult records untuk check ini
            $existingResults = DehumBahanResult::where('check_id', $dehumCheck->id)->get();
            
            // Persiapkan data yang akan diupdate
            $checkedItems = $request->input('checked_items');
            
            // Iterasi melalui semua checked items
            foreach ($checkedItems as $index => $item) {
                // Cari result yang sudah ada berdasarkan index/urutan
                $existingResult = $existingResults->where('checked_items', $item)->first();
                
                if ($existingResult) {
                    // Jika sudah ada, update datanya
                    $existingResult->update([
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
                    ]);
                } else {
                    // Jika belum ada, buat baru
                    DehumBahanResult::create([
                        'check_id' => $dehumCheck->id,
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
                    ]);
                }
            }
            
            // Hapus hasil yang tidak diperlukan lagi (jika ada item yang dihapus dari form)
            $currentItems = collect($checkedItems);
            $itemsToDelete = $existingResults->filter(function($result) use ($currentItems) {
                return !$currentItems->contains($result->checked_items);
            });
            
            if ($itemsToDelete->count() > 0) {
                DehumBahanResult::whereIn('id', $itemsToDelete->pluck('id'))->delete();
            }

            // Commit transaksi
            DB::commit();

            // Redirect dengan pesan sukses
            return redirect()->route('dehum-bahan.index')->with('success', 'Data pengecekan dehum bahan berhasil diperbarui.');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();

            // Redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Gagal memperbarui data pengecekan dehum: ' . $e->getMessage());
        }
    }

    public function show($dehumBahan)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Check if $dehumBahan is string or model instance
        if (is_string($dehumBahan)) {
            // If it's still a string, use the trait's resolve method
            $dehumBahanRecord = (new DehumBahanCheck())->resolveRouteBinding($dehumBahan);
        } else {
            // If it's already a model instance
            $dehumBahanRecord = $dehumBahan;
        }
        
        // Map the field names from the database to match template expectations
        $viewData = [
            'id' => $dehumBahanRecord->id,
            'hashid' => $dehumBahanRecord->hashid, // Add hashid property
            'nomer_dehum_bahan' => $dehumBahanRecord->nomer_dehum_bahan,
            'bulan' => $dehumBahanRecord->bulan,
            'checker_1' => $dehumBahanRecord->checkerMinggu1?->username,
            'checker_2' => $dehumBahanRecord->checkerMinggu2?->username,
            'checker_3' => $dehumBahanRecord->checkerMinggu3?->username,
            'checker_4' => $dehumBahanRecord->checkerMinggu4?->username,
            'created_date_1' => $dehumBahanRecord->tanggal_minggu1,
            'created_date_2' => $dehumBahanRecord->tanggal_minggu2,
            'created_date_3' => $dehumBahanRecord->tanggal_minggu3,
            'created_date_4' => $dehumBahanRecord->tanggal_minggu4,
            'approver_1' => $dehumBahanRecord->approverMinggu1?->username,
            'approver_2' => $dehumBahanRecord->approverMinggu2?->username,
            'approver_3' => $dehumBahanRecord->approverMinggu3?->username,
            'approver_4' => $dehumBahanRecord->approverMinggu4?->username,
        ];

        // Prepare the checked items
        $items = [
            1 => 'Filter',
            2 => 'Selang', 
            3 => 'Kontraktor',
            4 => 'Temperatur Control',
            5 => 'MCB',
            6 => 'Dew Point'
        ];

        // Mapping nama item di view dengan kemungkinan nama item di database
        $itemMapping = [
            1 => ['Filter'],
            2 => ['Selang'],
            3 => ['Kontraktor'],
            4 => ['Temperatur Control'],
            5 => ['MCB'],
            6 => ['Dew Point']
        ];

        // Fetch associated results using the record's actual ID
        $dehumBahanResults = DehumBahanResult::where('check_id', $dehumBahanRecord->id)->get();
        
        // Debug untuk melihat semua item di database (uncomment jika perlu)
        // $checkItemsList = $dehumBahanResults->pluck('checked_items')->toArray();
        // dd($checkItemsList);
        
        // Create arrays for check and keterangan data
        for ($weekNum = 1; $weekNum <= 4; $weekNum++) {
            $viewData["check_$weekNum"] = [];
            $viewData["keterangan_$weekNum"] = [];
            
            foreach ($items as $index => $item) {
                $result = null;
                // Coba semua kemungkinan nama untuk item ini
                foreach ($itemMapping[$index] as $possibleName) {
                    $result = $dehumBahanResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break; // Jika ditemukan, hentikan pencarian
                }
                
                $viewData["check_$weekNum"][$index] = $result ? $result->{"minggu$weekNum"} : '';
                $viewData["keterangan_$weekNum"][$index] = $result ? $result->{"keterangan_minggu$weekNum"} : '';
            }
        }

        // Convert to object for view compatibility
        $dehumBahanRecordObj = (object) $viewData;

        return view('dehum-bahan.show', [
            'dehumBahanRecord' => $dehumBahanRecordObj,
            'items' => $items,
            'user' => $user,
            'currentGuard' => $currentGuard
        ]);
    }

    public function approve(Request $request, $dehumBahan)
    {
        $user = $this->ensureAuthenticatedUser(['approver']);
        if (!is_object($user)) return $user;
        if (!$this->isAuthenticatedAs('approver')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
        }
        
        // Validate the request
        $validatedData = $request->validate([
            'approver_id_minggu1' => 'nullable|integer|exists:approvers,id',
            'approver_id_minggu2' => 'nullable|integer|exists:approvers,id', 
            'approver_id_minggu3' => 'nullable|integer|exists:approvers,id',
            'approver_id_minggu4' => 'nullable|integer|exists:approvers,id',
            // Tambahkan field nama approver juga jika diperlukan
            'approved_by_minggu1' => 'nullable|string',
            'approved_by_minggu2' => 'nullable|string',
            'approved_by_minggu3' => 'nullable|string', 
            'approved_by_minggu4' => 'nullable|string',
        ]);

        // Handle parameter - could be string (hashid) or model instance
        if (is_string($dehumBahan)) {
            // If it's still a string, use the trait's resolve method
            $dehumBahanRecord = (new DehumBahanCheck())->resolveRouteBinding($dehumBahan);
        } else {
            // If it's already a model instance
            $dehumBahanRecord = $dehumBahan;
        }

        // Update hanya field yang ada dalam request dan tidak null
        for ($i = 1; $i <= 4; $i++) {
            $approverIdField = "approver_id_minggu{$i}";
            if ($request->filled($approverIdField)) {
                $dehumBahanRecord->{$approverIdField} = $request->{$approverIdField};
            }
        }

        // Save the record
        $dehumBahanRecord->save();

        // Redirect back with success message
        return redirect()->route('dehum-bahan.index')
            ->with('success', 'Persetujuan berhasil disimpan!');
    }

    public function reviewPdf($hashid) 
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Gunakan trait untuk mendapatkan model berdasarkan hashid
        $dehumBahanCheck = app(DehumBahanCheck::class)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/035/REV.02')->firstOrFail();
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk dehum bahan
        $dehumBahanResults = DehumBahanResult::where('check_id', $dehumBahanCheck->id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Filter',
            2 => 'Selang',
            3 => 'Kontraktor',
            4 => 'Temperatur Control',
            5 => 'MCB',
            6 => 'Dew Point'  // Tambahkan item ini
        ];
        
        // Definisikan mapping untuk menangani kemungkinan variasi nama
        $itemMapping = [
            1 => ['Filter'],
            2 => ['Selang'],
            3 => ['Kontraktor'],
            4 => ['Temperatur Control', 'Temperatur Kontrol'],  // Tambahkan kedua variasi
            5 => ['MCB'],
            6 => ['Dew Point']
        ];
        
        // Siapkan semua field check dan keterangan untuk empat minggu
        for ($j = 1; $j <= 4; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per minggu
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari dehumBahanResults menggunakan pendekatan yang lebih fleksibel
            foreach ($items as $i => $item) {
                $result = null;
                // Coba semua kemungkinan nama untuk item ini
                foreach ($itemMapping[$i] as $possibleName) {
                    $result = $dehumBahanResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break; // Jika ditemukan, hentikan pencarian
                }
                
                ${'check_' . $j}[$i] = $result ? $result->{'minggu' . $j} : '';
                ${'keterangan_' . $j}[$i] = $result ? $result->{'keterangan_minggu' . $j} : '';
            }
            
            // Tambahkan array ke dehumBahanCheck object
            $dehumBahanCheck->{'check_' . $j} = ${'check_' . $j};
            $dehumBahanCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Setelah $dehumBahanCheck didefinisikan di reviewPdf, tambahkan:
        for ($i = 1; $i <= 4; $i++) {
            $dehumBahanCheck->{'checker_' . $i} = $dehumBahanCheck->{'checkerMinggu' . $i}?->username;
            $dehumBahanCheck->{'approver_' . $i} = $dehumBahanCheck->{'approverMinggu' . $i}?->username;
        }
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('dehum-bahan.review_pdf', [
            'dehumBahanCheck' => $dehumBahanCheck,
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
        $dehumBahanCheck = app(DehumBahanCheck::class)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/035/REV.02')->firstOrFail();
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk dehum bahan
        $dehumBahanResults = DehumBahanResult::where('check_id', $dehumBahanCheck->id)->get();
        
        // Definisikan items yang akan ditampilkan di PDF
        $items = [
            1 => 'Filter',
            2 => 'Selang',
            3 => 'Kontraktor',
            4 => 'Temperatur Control',
            5 => 'MCB',
            6 => 'Dew Point'
        ];
        
        // Definisikan mapping untuk menangani kemungkinan variasi nama
        $itemMapping = [
            1 => ['Filter'],
            2 => ['Selang'],
            3 => ['Kontraktor'],
            4 => ['Temperatur Control', 'Temperatur Kontrol'],
            5 => ['MCB'],
            6 => ['Dew Point']
        ];
        
        // Siapkan semua field check dan keterangan untuk empat minggu
        for ($j = 1; $j <= 4; $j++) {
            // Inisialisasi array untuk menyimpan hasil check dan keterangan per minggu
            ${'check_' . $j} = [];
            ${'keterangan_' . $j} = [];
            
            // Isi array dengan data dari dehumBahanResults menggunakan pendekatan yang lebih fleksibel
            foreach ($items as $i => $item) {
                $result = null;
                // Coba semua kemungkinan nama untuk item ini
                foreach ($itemMapping[$i] as $possibleName) {
                    $result = $dehumBahanResults->first(function($value) use ($possibleName) {
                        return stripos($value->checked_items, $possibleName) !== false;
                    });
                    
                    if ($result) break; // Jika ditemukan, hentikan pencarian
                }
                
                ${'check_' . $j}[$i] = $result ? $result->{'minggu' . $j} : '';
                ${'keterangan_' . $j}[$i] = $result ? $result->{'keterangan_minggu' . $j} : '';
            }
            
            // Tambahkan array ke dehumBahanCheck object
            $dehumBahanCheck->{'check_' . $j} = ${'check_' . $j};
            $dehumBahanCheck->{'keterangan_' . $j} = ${'keterangan_' . $j};
        }
        
        // Setelah $dehumBahanCheck didefinisikan di downloadPdf, tambahkan:
        for ($i = 1; $i <= 4; $i++) {
            $dehumBahanCheck->{'checker_' . $i} = $dehumBahanCheck->{'checkerMinggu' . $i}?->username;
            $dehumBahanCheck->{'approver_' . $i} = $dehumBahanCheck->{'approverMinggu' . $i}?->username;
        }
        
        $bulan = Carbon::createFromFormat('Y-m', $dehumBahanCheck->bulan)->translatedFormat('F Y');

        // Generate nama file PDF
        $filename = 'Dehum_bahan_nomer_' . $dehumBahanCheck->nomer_dehum_bahan . '_bulan_' . $bulan . '.pdf';
        
        // Render view sebagai HTML
        $html = view('dehum-bahan.review_pdf', [
            'dehumBahanCheck' => $dehumBahanCheck,
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
