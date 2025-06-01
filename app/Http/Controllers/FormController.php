<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    /**
     * Menampilkan daftar semua form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get unique nomor_form and nama_form values for dropdowns
        $uniqueNomorForms = Form::distinct()->pluck('nomor_form');
        $uniqueNamaForms = Form::distinct()->pluck('nama_form');
        
        // Start query
        $query = Form::query();
        
        // Apply filters if provided
        if ($request->filled('nomor_form')) {
            $query->where('nomor_form', $request->nomor_form);
        }
        
        if ($request->filled('nama_form')) {
            $query->where('nama_form', $request->nama_form);
        }
        
        // Get paginated results with filters preserved
        $forms = $query->orderBy('created_at', 'desc')
                    ->paginate(10)
                    ->appends($request->query());
        
        return view('menu.forms.index', compact('forms', 'uniqueNomorForms', 'uniqueNamaForms'));
    }

    /**
     * Menampilkan formulir untuk membuat form baru.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('menu.forms.create');
    }

    /**
     * Menyimpan form baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_form' => 'required|string|max:255',
            'nama_form' => 'required|string|max:255',
            'tanggal_efektif' => 'required|date',
        ], [
            'nomor_form.required' => 'Nomor form harus diisi',
            'nomor_form.max' => 'Nomor form maksimal 255 karakter',
            'nama_form.required' => 'Nama form harus diisi',
            'nama_form.max' => 'Nama form maksimal 255 karakter',
            'tanggal_efektif.required' => 'Tanggal efektif harus diisi',
            'tanggal_efektif.date' => 'Format tanggal efektif tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('host.forms.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Pengecekan duplikasi nomor_form
        $nomorFormExists = Form::where('nomor_form', $request->nomor_form)->exists();
        if ($nomorFormExists) {
            return redirect()
                ->route('host.forms.create')
                ->withErrors(['nomor_form' => 'Nomor form sudah tersedia dalam sistem.'])
                ->withInput();
        }

        // Pengecekan duplikasi nama_form
        $namaFormExists = Form::where('nama_form', $request->nama_form)->exists();
        if ($namaFormExists) {
            return redirect()
                ->route('host.forms.create')
                ->withErrors(['nama_form' => 'Nama form sudah tersedia dalam sistem.'])
                ->withInput();
        }

        // Konversi tanggal ke format Y-m-d untuk konsistensi
        $tanggalEfektif = date('Y-m-d', strtotime($request->tanggal_efektif));

        // Cek duplikasi kombinasi dengan query yang lebih tepat (kode asli Anda)
        $exists = Form::where(function($query) use ($request, $tanggalEfektif) {
            $query->where('nomor_form', $request->nomor_form)
                ->where('nama_form', $request->nama_form)
                ->whereRaw('DATE(tanggal_efektif) = ?', [$tanggalEfektif]);
        })->exists();

        if ($exists) {
            return redirect()
                ->route('host.forms.create')
                ->withErrors(['duplicate' => 'Form dengan nomor form, nama form, dan tanggal efektif yang sama sudah ada dalam sistem.'])
                ->withInput();
        }

        Form::create([
            'nomor_form' => $request->nomor_form,
            'nama_form' => $request->nama_form,
            'tanggal_efektif' => $tanggalEfektif,
        ]);

        return redirect()
            ->route('host.forms.create')
            ->with('success', 'Form berhasil ditambahkan! Anda dapat menambahkan form lainnya.');
    }

    /**
     * Menampilkan formulir untuk mengedit form.
     *
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function edit(Form $form)
    {
        return view('menu.forms.edit', compact('form'));
    }

    /**
     * Memperbarui form tertentu di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Form  $form
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Form $form)
    {
        $validator = Validator::make($request->all(), [
            'nomor_form' => 'required|string|max:255',
            'nama_form' => 'required|string|max:255',
            'tanggal_efektif' => 'required|date',
        ], [
            'nomor_form.required' => 'Nomor form harus diisi',
            'nomor_form.max' => 'Nomor form maksimal 255 karakter',
            'nama_form.required' => 'Nama form harus diisi',
            'nama_form.max' => 'Nama form maksimal 255 karakter',
            'tanggal_efektif.required' => 'Tanggal efektif harus diisi',
            'tanggal_efektif.date' => 'Format tanggal efektif tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('host.forms.edit', $form->id)
                ->withErrors($validator)
                ->withInput();
        }

        // Pengecekan duplikasi nomor_form (kecuali data yang sedang diupdate)
        $nomorFormExists = Form::where('nomor_form', $request->nomor_form)
                            ->where('id', '!=', $form->id)
                            ->exists();
        if ($nomorFormExists) {
            return redirect()
                ->route('host.forms.edit', $form->id)
                ->withErrors(['nomor_form' => 'Nomor form sudah tersedia dalam sistem.'])
                ->withInput();
        }

        // Pengecekan duplikasi nama_form (kecuali data yang sedang diupdate)
        $namaFormExists = Form::where('nama_form', $request->nama_form)
                            ->where('id', '!=', $form->id)
                            ->exists();
        if ($namaFormExists) {
            return redirect()
                ->route('host.forms.edit', $form->id)
                ->withErrors(['nama_form' => 'Nama form sudah tersedia dalam sistem.'])
                ->withInput();
        }

        // Konversi tanggal ke format Y-m-d untuk konsistensi
        $tanggalEfektif = date('Y-m-d', strtotime($request->tanggal_efektif));

        // Cek duplikasi kombinasi dengan query yang lebih tepat (kode asli Anda)
        $exists = Form::where(function($query) use ($request, $tanggalEfektif) {
            $query->where('nomor_form', $request->nomor_form)
                ->where('nama_form', $request->nama_form)
                ->whereRaw('DATE(tanggal_efektif) = ?', [$tanggalEfektif]);
        })
        ->where('id', '!=', $form->id)
        ->exists();

        if ($exists) {
            return redirect()
                ->route('host.forms.edit', $form->id)
                ->withErrors(['duplicate' => 'Form dengan nomor form, nama form, dan tanggal efektif yang sama sudah ada dalam sistem.'])
                ->withInput();
        }

        $form->update([
            'nomor_form' => $request->nomor_form,
            'nama_form' => $request->nama_form,
            'tanggal_efektif' => $tanggalEfektif,
        ]);

        return redirect()
            ->route('host.forms.index')
            ->with('success', 'Form berhasil diperbarui!');
    }
}