<?php

namespace App\Http\Controllers;

use App\Models\Checker;
use App\Models\Approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApproverController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data approver dari database dengan filter jika ada
        $query = Approver::query();
        
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
        
        // Filter berdasarkan role
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        // Ambil data dengan pagination, urutan terlama di atas (asc)
        $approvers = $query->orderBy('created_at', 'asc')->paginate(10);
        
        // Tampilkan halaman menu.approvers.index dengan data approvers
        return view('menu.approvers.index', compact('approvers'));
    }

    public function create()
    {
        return view('menu.approvers.create');
    }

    public function store(Request $request)
    {
        // Validasi input pengguna
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:approvers,username',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:Penanggung Jawab,Kepala Regu',
            'status' => 'required|string|in:aktif,nonaktif',
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role.required' => 'Peran harus dipilih',
            'role.in' => 'Peran harus penanggung jawab atau kepala regu',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus aktif atau nonaktif',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('host.approvers.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Buat approver baru
        Approver::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('host.approvers.index')
            ->with('success', 'Approver berhasil ditambahkan!');
    }

    public function edit($id)
    {
        // Mengambil data approver berdasarkan id
        $approver = Approver::findOrFail($id);
        
        // Menampilkan halaman edit dengan data approver
        return view('menu.approvers.edit', compact('approver'));
    }

    public function update(Request $request, $id)
    {
        // Mencari approver yang akan diupdate
        $approver = Approver::findOrFail($id);
        
        // Aturan validasi
        $rules = [
            'username' => 'required|string|max:255|unique:approvers,username,' . $id,
            'role' => 'required|string|in:Penanggung Jawab,Kepala Regu',
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
            'role.required' => 'Peran harus dipilih',
            'role.in' => 'Peran harus penanggung jawab atau kepala regu',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status harus aktif atau tidak aktif',
        ];
        
        // Validasi request
        $validator = Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            return redirect()
                ->route('host.approvers.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }
        
        // Update username
        $approver->username = $request->username;
        
        // Update role dan pastikan nilai dalam format string
        $approver->role = $request->role;
        
        // Update status dan pastikan nilai dalam format string
        $approver->status = $request->status;
        
        // Jika password diisi, update password
        if ($request->filled('password')) {
            $approver->password = Hash::make($request->password);
        }
        
        // Simpan perubahan
        $approver->save();
        
        return redirect()
            ->route('host.approvers.index')
            ->with('success', 'Data approver berhasil diperbarui!');
    }
}
