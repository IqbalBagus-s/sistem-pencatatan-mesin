<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CraneMatrasCheck;
use App\Models\CraneMatrasResult;
use App\Models\Form;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Activity;
use App\Models\Checker;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;// Import Facade PDF
use App\Traits\WithAuthentication;

class CraneMatrasControler extends Controller
{
    use WithAuthentication;

    public function index(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        $query = CraneMatrasCheck::query();

        // Filter berdasarkan nama checker menggunakan relasi
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('checker', function ($q) use ($search) {
                $q->where('username', 'LIKE', $search)
                ->orWhere('name', 'LIKE', $search); // Jika ada field name
            });
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

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan checker_id
        if ($request->filled('checker_id')) {
            $query->where('checker_id', $request->checker_id);
        }

        // Filter berdasarkan approver_id
        if ($request->filled('approver_id')) {
            $query->where('approver_id', $request->approver_id);
        }

        // Selalu load relasi checker dan approver untuk menampilkan nama
        $query->with(['checker', 'approver']);

        // Tambahkan relasi dengan hasil pengecekan jika diperlukan
        if ($request->filled('with_results')) {
            $query->with('results');
        }

        // Urutkan berdasarkan kolom tertentu
        $sortColumn = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validasi kolom sorting untuk keamanan
        $allowedSortColumns = ['created_at', 'updated_at', 'tanggal', 'bulan', 'nomer_crane_matras', 'status'];
        if (in_array($sortColumn, $allowedSortColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Ambil data dengan paginasi dan pastikan parameter tetap diteruskan
        $checks = $query->paginate(10)->appends($request->query());

        // Data tambahan untuk dropdown filter jika diperlukan
        $checkers = \App\Models\Checker::all(); // Untuk dropdown filter checker
        $approvers = \App\Models\Approver::all(); // Untuk dropdown filter approver
        
        return view('crane_matras.index', compact(
            'checks', 
            'user', 
            'currentGuard',
            'checkers',
            'approvers'
        ));
    }

    public function create()
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        return view('crane_matras.create', compact('user', 'currentGuard'));
    }

    public function store(Request $request)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Custom error messages
        $customMessages = [
            'nomer_crane_matras.required' => 'Silakan pilih nomor crane matras terlebih dahulu!',
        ];
        
        // Validasi input - checked_by_1 dan tanggal_1 tidak wajib
        $validatedData = $request->validate([
            'nomer_crane_matras' => 'required|integer|min:1|max:3',
            'bulan' => 'required|date_format:Y-m',
            'checked_by_1' => 'nullable|string|max:255',
            'tanggal_1' => 'nullable|string',
            'check_num_1' => 'nullable|string',
            'checked_items' => 'required|array',
            'check' => 'required|array',
            'keterangan' => 'nullable|array',
        ], $customMessages);
        
        // Cek apakah ada data dengan nomer_crane_matras dan bulan yang sama
        $existingRecord = CraneMatrasCheck::where('nomer_crane_matras', $request->input('nomer_crane_matras'))
            ->where('bulan', $request->input('bulan'))
            ->first();

        if ($existingRecord) {
            // Format bulan dari Y-m menjadi nama bulan dan tahun
            $formattedMonth = Carbon::parse($request->input('bulan') . '-01')->locale('id')->isoFormat('MMMM YYYY');
            
            // Buat pesan error dengan detail data yang duplikat
            $errorMessage = "Data duplikat ditemukan untuk Crane Matras Nomor {$request->input('nomer_crane_matras')} pada bulan {$formattedMonth}!";
            
            // Redirect kembali dengan pesan error yang lebih informatif
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
        
        // Mengubah format tanggal dari "DD Bulan YYYY" menjadi "YYYY-MM-DD"
        $tanggal = null;
        $tanggalFormatted = null; // Untuk activity log
        
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
                $tanggalFormatted = $request->tanggal_1; // Simpan format asli untuk log
            }
        }
        
        // Cari user berdasarkan username yang dipilih (jika ada)
        $checkerUser = null;
        $checkerId = null;
        
        if ($request->filled('checked_by_1') && $request->filled('check_num_1')) {
            // Cari user berdasarkan username yang dipilih
            $checkerUser = Checker::where('username', $request->input('checked_by_1'))->first();
            
            if (!$checkerUser) {
                return redirect()->back()
                    ->with('error', 'User dengan username "' . $request->input('checked_by_1') . '" tidak ditemukan!')
                    ->withInput();
            }
            
            $checkerId = $checkerUser->id;
        }
        
        try {
            // Mulai transaksi database
            DB::beginTransaction();
            
            // Buat record baru di CraneMatrasCheck
            $craneMatrasCheck = CraneMatrasCheck::create([
                'nomer_crane_matras' => $request->input('nomer_crane_matras'),
                'bulan' => $request->input('bulan'),
                'tanggal' => $tanggal, // Akan null jika tombol tidak dipilih
                'checker_id' => $checkerId, // Akan null jika tombol tidak dipilih
                'approver_id' => null, // Diisi pada tahap approval
                // status akan otomatis diset menjadi 'belum_disetujui' oleh model
            ]);

            // Log untuk debugging
            Log::info('Checked Items:', $request->input('checked_items'));
            Log::info('Check Values:', $request->input('check'));
            
            // Mendefinisikan daftar item yang diperiksa
            $items = $request->input('checked_items');
            
            // Array untuk menyimpan detail items yang disimpan (untuk activity log)
            $itemsProcessed = [];
            
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
                
                // Simpan detail untuk activity log
                $itemsProcessed[] = [
                    'item' => $item,
                    'check' => $checkValue,
                    'keterangan' => $keterangan
                ];
            }

            // LOG AKTIVITAS
            $bulanFormatted = Carbon::parse($request->input('bulan') . '-01')->locale('id')->isoFormat('MMMM YYYY');
            $tanggalString = $tanggalFormatted ? $tanggalFormatted : 'Tidak ada tanggal pemeriksaan';
            
            // Buat deskripsi berdasarkan apakah checker dipilih atau tidak
            $description = '';
            if ($checkerUser) {
                $description = 'User ' . $user->username . ' membuat pemeriksaan Crane Matras Nomor ' . $request->input('nomer_crane_matras') . ' untuk bulan ' . $bulanFormatted . ' atas nama checker ' . $checkerUser->username . ($tanggalFormatted ? ' pada tanggal: ' . $tanggalString : '');
            } else {
                $description = 'User ' . $user->username . ' membuat pemeriksaan Crane Matras Nomor ' . $request->input('nomer_crane_matras') . ' untuk bulan ' . $bulanFormatted . ' (tanpa checker yang ditentukan)';
            }
            
            Activity::logActivity(
                'checker',                                              // user_type
                $user->id,                                             // user_id (yang melakukan input)
                $user->username,                                       // user_name (yang melakukan input)
                'created',                                             // action
                $description,                                          // description
                'crane_matras_check',                                  // target_type
                $craneMatrasCheck->id,                                 // target_id
                [
                    'nomer_crane_matras' => $request->input('nomer_crane_matras'),
                    'bulan' => $request->input('bulan'),
                    'bulan_formatted' => $bulanFormatted,
                    'tanggal_check' => $tanggalFormatted,
                    'tanggal_check_db' => $tanggal,
                    'checker_id' => $checkerId,
                    'checker_name' => $checkerUser ? $checkerUser->username : null,
                    'input_by_id' => $user->id,
                    'input_by_name' => $user->username,
                    'total_items' => count($items),
                    'items_processed' => $itemsProcessed,
                    'status' => $craneMatrasCheck->status,
                    'has_checker' => $checkerUser ? true : false
                ]                                                      // details (JSON)
            );

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

    public function edit($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Model CraneMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new CraneMatrasCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $check->load([
            'results',
            // Tambahkan relasi untuk checker dan approver jika diperlukan
            // 'checker',
            // 'approver'
        ]);
        
        // Ambil data hasil pemeriksaan dari relasi yang sudah di-load
        $results = $check->results;
        
        // Memformat data hasil pemeriksaan untuk tampilan
        $formattedResults = [];
        
        // Mengumpulkan semua items dari hasil pemeriksaan
        $items = [];
        $index = 0;
        
        foreach ($results as $result) {
            $items[$index] = $result->checked_items;
            $formattedResults[$index] = [
                'item' => $result->checked_items,
                'check' => $result->check,
                'keterangan' => $result->keterangan
            ];
            $index++;
        }
        
        // Format tanggal jika ada
        $tanggalFormatted = null;
        if ($check->tanggal) {
            $date = new \DateTime($check->tanggal);
            $bulanIndonesia = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $month = $bulanIndonesia[$date->format('m')];
            $tanggalFormatted = $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
        }
        
        // Siapkan data untuk form
        $checkerData = [
            'checked_by_1' => $check->getCheckerName(), // Menggunakan method dari model
            'tanggal_1' => $tanggalFormatted,
            'bulan' => $check->bulan,
            'nomer_crane_matras' => $check->nomer_crane_matras,
        ];
        
        // Status approval menggunakan method dari model
        $approvalStatus = $check->isApproved();
        
        return view('crane_matras.edit', compact('check', 'formattedResults', 'items', 'checkerData', 'approvalStatus', 'user', 'currentGuard'));
    }

    public function update(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();
        
        // Validasi input - hanya validasi data yang penting
        $validated = $request->validate([
            'nomer_crane_matras' => 'required|integer|between:1,10',
            'bulan' => 'required|date_format:Y-m',
        ]);

        // Model CraneMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $craneMatrasCheck = (new CraneMatrasCheck)->resolveRouteBinding($hashid);

        // Cek apakah ada perubahan pada data utama (nomer_crane_matras, bulan)
        if ($craneMatrasCheck->nomer_crane_matras != $request->nomer_crane_matras || 
            $craneMatrasCheck->bulan != $request->bulan) {
            
            // Periksa apakah data dengan kombinasi baru sudah ada
            $existingRecord = CraneMatrasCheck::where('nomer_crane_matras', $request->nomer_crane_matras)
                ->where('bulan', $request->bulan)
                ->where('id', '!=', $craneMatrasCheck->id) // Menggunakan id dari model instance
                ->first();
            
            if ($existingRecord) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data dengan nomor crane matras dan bulan yang sama sudah ada!');
            }
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Simpan data lama untuk activity log
            $oldData = [
                'nomer_crane_matras' => $craneMatrasCheck->nomer_crane_matras,
                'bulan' => $craneMatrasCheck->bulan,
                'tanggal' => $craneMatrasCheck->tanggal,
                'checker_id' => $craneMatrasCheck->checker_id,
                'status' => $craneMatrasCheck->status
            ];
            
            // Update data CraneMatrasCheck
            $updateData = [
                'nomer_crane_matras' => $request->nomer_crane_matras,
                'bulan' => $request->bulan,
                'tanggal' => now(), // Default ke waktu sekarang jika tidak ada input tanggal
            ];
            
            // Update checker_id berdasarkan user yang sedang login (karena yang edit adalah checker itu sendiri)
            $updateData['checker_id'] = $user->id;
            
            $craneMatrasCheck->update($updateData);
            
            // Reset approval jika sebelumnya sudah diapprove dan ada perubahan data
            if ($craneMatrasCheck->hasApprover() && $request->has('check')) {
                $craneMatrasCheck->update([
                    'approver_id' => null,
                    // status akan otomatis diupdate melalui model event
                ]);
            }
            
            // Ambil data existing dari tabel CraneMatrasResult - menggunakan id dari model instance
            $existingResults = CraneMatrasResult::where('check_id', $craneMatrasCheck->id)->get()->keyBy('checked_items');
            
            // Proses setiap item dari form
            $itemCount = count($request->input('checked_items', []));
            $itemsProcessed = [];
            
            for ($i = 0; $i < $itemCount; $i++) {
                $itemName = $request->input("checked_items.{$i}");
                
                // Persiapkan data untuk update atau create
                $resultData = [
                    'check' => $request->input("check.{$i}", '-'),
                    'keterangan' => $request->input("keterangan.{$i}", ''),
                ];
                
                // Cek apakah record sudah ada
                $existingResult = $existingResults->get($itemName);
                
                if ($existingResult) {
                    // Update record yang sudah ada
                    $existingResult->update($resultData);
                } else {
                    // Buat record baru jika belum ada - menggunakan id dari model instance
                    $resultData['check_id'] = $craneMatrasCheck->id;
                    $resultData['checked_items'] = $itemName;
                    CraneMatrasResult::create($resultData);
                }
                
                // Simpan detail untuk activity log
                $itemsProcessed[] = [
                    'item' => $itemName,
                    'check' => $resultData['check'],
                    'keterangan' => $resultData['keterangan']
                ];
            }
            
            // Commit transaksi
            DB::commit();
            
            return redirect()->route('crane-matras.index') // Gunakan hyphen (-) bukan underscore (_)
                ->with('success', 'Data berhasil diperbarui!');
                
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();
            
            Log::error('Error updating Crane Matras Check: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($hashid)
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();

        // Model CraneMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new CraneMatrasCheck)->resolveRouteBinding($hashid);
        
        // Load relasi setelah mendapatkan instance model
        $check->load(['checker', 'approver', 'results']);
        
        // Ambil data hasil pemeriksaan dari relasi yang sudah di-load
        $results = $check->results;
        
        // Memformat data hasil pemeriksaan untuk tampilan
        $formattedResults = [];
        
        // Mengumpulkan semua item dari hasil pemeriksaan
        foreach ($results as $result) {
            $formattedResults[] = [
                'item' => $result->checked_items,
                'check' => $result->check,
                'keterangan' => $result->keterangan
            ];
        }
        
        // Format tanggal jika ada
        $tanggalFormatted = null;
        if ($check->tanggal) {
            $date = new \DateTime($check->tanggal);
            $bulanIndonesia = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $month = $bulanIndonesia[$date->format('m')];
            $tanggalFormatted = $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
        }
        
        // Siapkan data untuk tampilan
        $checkerData = [
            'checker_name' => $check->getCheckerName(),
            'approver_name' => $check->getApproverName(),
            'tanggal' => $tanggalFormatted,
            'bulan' => $check->bulan,
            'nomer_crane_matras' => $check->nomer_crane_matras,
            'status' => $check->status_label
        ];
        
        // Status approval dari model
        $approvalStatus = $check->isApproved();
        
        return view('crane_matras.show', compact('check', 'formattedResults', 'checkerData', 'approvalStatus', 'user', 'currentGuard'));
    }

    public function approve(Request $request, $hashid)
    {
        $user = $this->ensureAuthenticatedUser(['approver']);
        if (!is_object($user)) return $user;
        if (!$this->isAuthenticatedAs('approver')) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menyetujui data.');
        }

        // Model CraneMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $check = (new CraneMatrasCheck)->resolveRouteBinding($hashid);
        
        // Cek jika sudah disetujui
        if ($check->isApproved()) {
            return redirect()->back()->with('error', 'Data sudah disetujui sebelumnya.');
        }

        // Simpan data approver menggunakan method approve dari model
        $check->approve($user->id);
        
        return redirect()
            ->route('crane-matras.index')
            ->with('success', 'Persetujuan berhasil disimpan');
    }

    public function reviewPdf($hashid) 
    {
        $user = $this->ensureAuthenticatedUser();
        if (!is_object($user)) return $user;
        $currentGuard = $this->getCurrentGuard();

        // Model CraneMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $craneMatrasCheck = (new CraneMatrasCheck)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait (sesuaikan nomor form dengan yang digunakan untuk crane matras)
        $form = Form::findOrFail(12); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk crane matras menggunakan ID asli
        $craneMatrasResults = CraneMatrasResult::where('check_id', $craneMatrasCheck->id)->get();
        
        // Format tanggal pemeriksaan dalam bahasa Indonesia
        $tanggalFormatted = null;
        if ($craneMatrasCheck->tanggal) {
            $date = new \DateTime($craneMatrasCheck->tanggal);
            $bulanIndonesia = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $month = $bulanIndonesia[$date->format('m')];
            $tanggalFormatted = $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
        }
        
        // Siapkan data pemeriksaan yang sudah diformat
        $formattedResults = [];
        
        // Mengumpulkan semua item dari hasil pemeriksaan
        foreach ($craneMatrasResults as $result) {
            $formattedResults[] = [
                'item' => $result->checked_items,
                'check' => $result->check,
                'keterangan' => $result->keterangan
            ];
        }
        
        // Siapkan data checker
        $checkerData = [
            'checker_name' => $craneMatrasCheck->getCheckerName(),
            'approver_name' => $craneMatrasCheck->getApproverName(),
            'tanggal' => $tanggalFormatted,
            'bulan' => $craneMatrasCheck->bulan,
            'nomer_crane_matras' => $craneMatrasCheck->nomer_crane_matras,
            'status' => $craneMatrasCheck->status_label
        ];
        
        // Status approval dari model
        $approvalStatus = $craneMatrasCheck->isApproved();
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('crane_matras.review_pdf', [
            'craneMatrasCheck' => $craneMatrasCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'formattedResults' => $formattedResults,
            'checkerData' => $checkerData,
            'approvalStatus' => $approvalStatus,
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

        // Model CraneMatrasCheck akan otomatis resolve hashid menjadi model instance
        // karena menggunakan trait Hashidable
        $craneMatrasCheck = (new CraneMatrasCheck)->resolveRouteBinding($hashid);
        
        // Ambil data form terkait (sesuaikan nomor form dengan yang digunakan untuk crane matras)
        $form = Form::findOrFail(12); 
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk crane matras menggunakan ID asli
        $craneMatrasResults = CraneMatrasResult::where('check_id', $craneMatrasCheck->id)->get();
        
        // Format tanggal pemeriksaan dalam bahasa Indonesia
        $tanggalFormatted = null;
        if ($craneMatrasCheck->tanggal) {
            $date = new \DateTime($craneMatrasCheck->tanggal);
            $bulanIndonesia = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $month = $bulanIndonesia[$date->format('m')];
            $tanggalFormatted = $date->format('d') . ' ' . $month . ' ' . $date->format('Y');
        }
        
        // Siapkan data pemeriksaan yang sudah diformat
        $formattedResults = [];
        
        // Mengumpulkan semua item dari hasil pemeriksaan
        foreach ($craneMatrasResults as $result) {
            $formattedResults[] = [
                'item' => $result->checked_items,
                'check' => $result->check,
                'keterangan' => $result->keterangan
            ];
        }
        
        // Siapkan data checker
        $checkerData = [
            'checker_name' => $craneMatrasCheck->getCheckerName(),
            'approver_name' => $craneMatrasCheck->getApproverName(),
            'tanggal' => $tanggalFormatted,
            'bulan' => $craneMatrasCheck->bulan,
            'nomer_crane_matras' => $craneMatrasCheck->nomer_crane_matras,
            'status' => $craneMatrasCheck->status_label
        ];
        
        // Status approval dari model
        $approvalStatus = $craneMatrasCheck->isApproved();
        
        // Format bulan untuk nama file
        $bulan = Carbon::createFromFormat('Y-m', $craneMatrasCheck->bulan)->translatedFormat('F Y');
        
        // Generate nama file PDF
        $filename = 'Crane_matras_nomer_' . $craneMatrasCheck->nomer_crane_matras . '_bulan_' . $bulan . '.pdf';
        
        // Render view sebagai HTML
        $html = view('crane_matras.review_pdf', [
            'craneMatrasCheck' => $craneMatrasCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'formattedResults' => $formattedResults,
            'checkerData' => $checkerData,
            'approvalStatus' => $approvalStatus,
            'user' => $user,
            'currentGuard' => $currentGuard
        ])->render();
        
        // Inisialisasi Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Atur ukuran dan orientasi halaman
        $dompdf->setPaper('A4', 'portrait');
        
        // Render PDF (mengubah HTML menjadi PDF)
        $dompdf->render();
        
        // Download file PDF
        return $dompdf->stream($filename, [
            'Attachment' => false, // Set true untuk download otomatis
        ]);
    }
}
