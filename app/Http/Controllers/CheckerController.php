<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CheckerController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data checker dari database dengan filter jika ada
        $query = Checker::query();
        
        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('username', 'like', $searchTerm);
            });
        }
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Ambil data dengan pagination, urutan terlama di atas (asc)
        $checkers = $query->orderBy('created_at', 'asc')->paginate(10);
        
        // Tampilkan halaman menu.checkers.index dengan data checkers
        return view('menu.checkers.index', compact('checkers'));
    }

    public function create()
    {
        return view('menu.checkers.create');
    }

    public function store(Request $request)
    {
        // Validasi input pengguna
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:checkers,username',
            'password' => 'required|string|min:6',
            'status' => 'required|string|in:aktif,nonaktif',
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus aktif atau nonaktif',
        ]);
    
        if ($validator->fails()) {
            return redirect()
                ->route('host.checkers.create')
                ->withErrors($validator)
                ->withInput();
        }
    
        // Buat checker baru
        Checker::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ]);
    
        // Kembali ke halaman yang sama dengan pesan sukses, tanpa menyimpan input sebelumnya
        return redirect()
            ->route('host.checkers.create')
            ->with('success', 'Checker berhasil ditambahkan! Anda dapat menambahkan checker lainnya.');
    }

    public function edit($id)
    {
        // Mengambil data checker berdasarkan id
        $checker = Checker::findOrFail($id);
        
        // Menampilkan halaman edit dengan data checker
        return view('menu.checkers.edit', compact('checker'));
    }

    public function update(Request $request, $id)
    {
        // Mencari checker yang akan diupdate
        $checker = Checker::findOrFail($id);
        
        // Aturan validasi
        $rules = [
            'username' => 'required|string|max:255|unique:checkers,username,' . $id,
            'status' => 'required|string|in:aktif,tidak_aktif',
        ];
        
        if (!empty($request->password)) {
            $rules['password'] = 'string|min:6';
        }

        // Jika password diisi, tambahkan validasi password
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }
        
        // Pesan error validasi dalam bahasa Indonesia
        $messages = [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 6 karakter',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus aktif atau tidak aktif',
        ];
        
        // Validasi request
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return redirect()
                ->route('host.checkers.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
        
        // Update username
        $checker->username = $request->username;
        
        // Update status dan pastikan nilai dalam format string
        $checker->status = $request->status;
        
        // Jika password diisi, update password
        if ($request->filled('password')) {
            $checker->password = Hash::make($request->password);
        }
        
        // Simpan perubahan
        $checker->save();
        
        return redirect()
            ->route('host.checkers.index')
            ->with('success', 'Data checker berhasil diperbarui!');
    }
}
