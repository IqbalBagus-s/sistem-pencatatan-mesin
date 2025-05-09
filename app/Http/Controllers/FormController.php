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
        
        // Get paginated results
        $forms = $query->orderBy('created_at', 'desc')->paginate(10);
        
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
        ]);
    
        if ($validator->fails()) {
            return redirect()
                ->route('host.forms.create')
                ->withErrors($validator)
                ->withInput();
        }
    
        Form::create([
            'nomor_form' => $request->nomor_form,
            'nama_form' => $request->nama_form,
            'tanggal_efektif' => $request->tanggal_efektif,
        ]);
    
        // Redirect back to the create page with success message
        // instead of going to the index page
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
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('host.forms.edit', $form->id)
                ->withErrors($validator)
                ->withInput();
        }

        $form->update([
            'nomor_form' => $request->nomor_form,
            'nama_form' => $request->nama_form,
            'tanggal_efektif' => $request->tanggal_efektif,
        ]);

        return redirect()
            ->route('host.forms.index')
            ->with('success', 'Form berhasil diperbarui!');
    }
}