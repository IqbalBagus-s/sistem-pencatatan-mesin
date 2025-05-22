<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DehumBahanCheck;
use App\Models\DehumBahanResult;
use App\Models\Form;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF

class DehumBahanController extends Controller
{
    public function index(Request $request)
    {
        $query = DehumBahanCheck::query();

        // Filter berdasarkan nama checker jika ada
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('checked_by_minggu1', 'LIKE', $search)
                ->orWhere('checked_by_minggu2', 'LIKE', $search)
                ->orWhere('checked_by_minggu3', 'LIKE', $search)
                ->orWhere('checked_by_minggu4', 'LIKE', $search);
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

        return view('dehum-bahan.index', compact('checks'));
    }

    public function create()
    {
        return view('dehum-bahan.create');
    }

    public function store(Request $request)
    {
        $customMessages = [
            'nomer_dehum_bahan.required' => 'Silakan pilih nomor dehum bahan terlebih dahulu!',
            'bulan.required' => 'Silakan pilih bulan terlebih dahulu!'
        ];
        // Validate the request
        $validatedData = $request->validate([
            'nomer_dehum_bahan' => 'required|integer|min:1|max:15',
            'bulan' => 'required|date_format:Y-m',
            
            // Validation for creator fields
            'created_by_1' => 'nullable|string|max:255',
            'created_date_1' => 'nullable|date',
            'created_by_2' => 'nullable|string|max:255',
            'created_date_2' => 'nullable|date',
            'created_by_3' => 'nullable|string|max:255',
            'created_date_3' => 'nullable|date',
            'created_by_4' => 'nullable|string|max:255',
            'created_date_4' => 'nullable|date',
            
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
                
                // Populate weekly dates
                'tanggal_minggu1' => $request->input('created_date_1'),
                'tanggal_minggu2' => $request->input('created_date_2'),
                'tanggal_minggu3' => $request->input('created_date_3'),
                'tanggal_minggu4' => $request->input('created_date_4'),
                
                // Populate weekly checkers
                'checked_by_minggu1' => $request->input('created_by_1'),
                'checked_by_minggu2' => $request->input('created_by_2'),
                'checked_by_minggu3' => $request->input('created_by_3'),
                'checked_by_minggu4' => $request->input('created_by_4'),
            ]);

            // Prepare and create DehumBahanResult records
            $checkedItems = $request->input('checked_items');
            
            foreach ($checkedItems as $index => $item) {
                DehumBahanResult::create([
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
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Redirect with success message
            return redirect()->route('dehum-bahan.index')->with('success', 'Data Pencatatan sudah tersimpan!');
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            // Redirect back with error message
            return redirect()->back()->with('error', 'Failed to save Dehum Bahan check data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $dehumCheck = DehumBahanCheck::findOrFail($id);
        $dehumResults = $dehumCheck->results;
        return view('dehum-bahan.edit', compact('dehumCheck', 'dehumResults'));
    }

    public function update(Request $request, $id)
    {
        // Validasi data request
        $validatedData = $request->validate([
            'nomer_dehum_bahan' => 'required|integer|min:1',
            'bulan' => 'required|date_format:Y-m',
            
            // Validasi untuk checked_by fields
            'checked_by_minggu1' => 'nullable|string|max:255',
            'tanggal_minggu1' => 'nullable|date',
            'checked_by_minggu2' => 'nullable|string|max:255',
            'tanggal_minggu2' => 'nullable|date',
            'checked_by_minggu3' => 'nullable|string|max:255',
            'tanggal_minggu3' => 'nullable|date',
            'checked_by_minggu4' => 'nullable|string|max:255',
            'tanggal_minggu4' => 'nullable|date',
            
            // Validasi untuk approved_by fields
            'approved_by_minggu1' => 'nullable|string|max:255',
            'approved_by_minggu2' => 'nullable|string|max:255',
            'approved_by_minggu3' => 'nullable|string|max:255',
            'approved_by_minggu4' => 'nullable|string|max:255',
            
            // Validasi untuk checked items dan checks
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

        // Cari record DehumBahanCheck yang akan diupdate
        $dehumCheck = DehumBahanCheck::findOrFail($id);

        // Cek apakah ada record lain dengan nomor dehum dan bulan yang sama (kecuali record saat ini)
        $existingRecord = DehumBahanCheck::where('nomer_dehum_bahan', $request->input('nomer_dehum_bahan'))
            ->where('bulan', $request->input('bulan'))
            ->where('id', '!=', $id)
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
                
                // Update tanggal mingguan
                'tanggal_minggu1' => $request->input('tanggal_minggu1'),
                'tanggal_minggu2' => $request->input('tanggal_minggu2'),
                'tanggal_minggu3' => $request->input('tanggal_minggu3'),
                'tanggal_minggu4' => $request->input('tanggal_minggu4'),
                
                // Update pemeriksa mingguan
                'checked_by_minggu1' => $request->input('checked_by_minggu1'),
                'checked_by_minggu2' => $request->input('checked_by_minggu2'),
                'checked_by_minggu3' => $request->input('checked_by_minggu3'),
                'checked_by_minggu4' => $request->input('checked_by_minggu4'),
                
                // Update status persetujuan
                'approved_by_minggu1' => $request->input('approved_by_minggu1'),
                'approved_by_minggu2' => $request->input('approved_by_minggu2'),
                'approved_by_minggu3' => $request->input('approved_by_minggu3'),
                'approved_by_minggu4' => $request->input('approved_by_minggu4'),
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

    public function show($check_id)
    {
        // Find the main dehum bahan record
        $dehumBahanRecord = DehumBahanCheck::findOrFail($check_id);
        
        // Map the field names from the database to match template expectations
        $viewData = [
            'id' => $dehumBahanRecord->id,
            'nomer_dehum_bahan' => $dehumBahanRecord->nomer_dehum_bahan,
            'bulan' => $dehumBahanRecord->bulan,
            'created_by_1' => $dehumBahanRecord->checked_by_minggu1,
            'created_by_2' => $dehumBahanRecord->checked_by_minggu2,
            'created_by_3' => $dehumBahanRecord->checked_by_minggu3,
            'created_by_4' => $dehumBahanRecord->checked_by_minggu4,
            'created_date_1' => $dehumBahanRecord->tanggal_minggu1,
            'created_date_2' => $dehumBahanRecord->tanggal_minggu2,
            'created_date_3' => $dehumBahanRecord->tanggal_minggu3,
            'created_date_4' => $dehumBahanRecord->tanggal_minggu4,
            'approved_by_minggu1' => $dehumBahanRecord->approved_by_minggu1,
            'approved_by_minggu2' => $dehumBahanRecord->approved_by_minggu2,
            'approved_by_minggu3' => $dehumBahanRecord->approved_by_minggu3,
            'approved_by_minggu4' => $dehumBahanRecord->approved_by_minggu4
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

        // Fetch associated results
        $dehumBahanResults = DehumBahanResult::where('check_id', $check_id)->get();
        
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
            'items' => $items
        ]);
    }

    public function approve(Request $request, $id)
    {
        // Validate the request - hanya validasi field yang dikirim dalam request
        $validatedData = $request->validate([
            'approved_by_minggu1' => 'nullable|string|max:255',
            'approved_by_minggu2' => 'nullable|string|max:255',
            'approved_by_minggu3' => 'nullable|string|max:255',
            'approved_by_minggu4' => 'nullable|string|max:255'
        ]);

        // Find the existing DehumBahan record
        $dehumBahanRecord = DehumBahanCheck::findOrFail($id);

        // Hanya update field yang ada dalam request
        // Ini mencegah field yang sudah diisi sebelumnya ditimpa dengan null
        foreach ($validatedData as $field => $value) {
            if ($request->has($field)) {
                $dehumBahanRecord->{$field} = $value;
            }
        }

        // Save the record
        $dehumBahanRecord->save();

        // Redirect back with a success message
        return redirect()->route('dehum-bahan.index')
            ->with('success', 'Persetujuan berhasil disimpan!');
    }

    public function reviewPdf($id) 
    {
        // Ambil data pemeriksaan dehum bahan berdasarkan ID
        $dehumBahanCheck = DehumBahanCheck::findOrFail($id);
        
        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/035/REV.02')->firstOrFail();
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk dehum bahan
        $dehumBahanResults = DehumBahanResult::where('check_id', $id)->get();
        
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
        
        // Render view sebagai HTML untuk preview PDF
        $view = view('dehum-bahan.review_pdf', [
            'dehumBahanCheck' => $dehumBahanCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items
        ]);
        
        // Return view untuk preview
        return $view;
    }
    
    public function downloadPdf($id)
    {
        // Ambil data pemeriksaan dehum bahan berdasarkan ID
        $dehumBahanCheck = DehumBahanCheck::findOrFail($id);
        
        // Ambil data form terkait
        $form = Form::where('nomor_form', 'APTEK/035/REV.02')->firstOrFail();
        
        // Format tanggal efektif
        $formattedTanggalEfektif = $form->tanggal_efektif->format('d/m/Y');
        
        // Ambil detail hasil pemeriksaan untuk dehum bahan
        $dehumBahanResults = DehumBahanResult::where('check_id', $id)->get();
        
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
        
        $bulan = Carbon::createFromFormat('Y-m', $dehumBahanCheck->bulan)->translatedFormat('F Y');

        // Generate nama file PDF
        $filename = 'Dehum_bahan_nomer_' . $dehumBahanCheck->nomer_dehum_bahan . '_bulan_' . $bulan . '.pdf';
        
        // Render view sebagai HTML
        $html = view('dehum-bahan.review_pdf', [
            'dehumBahanCheck' => $dehumBahanCheck,
            'form' => $form,
            'formattedTanggalEfektif' => $formattedTanggalEfektif,
            'items' => $items
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
