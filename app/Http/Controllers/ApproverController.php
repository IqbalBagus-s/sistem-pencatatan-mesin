<?php

namespace App\Http\Controllers;

use App\Models\Checker;
use App\Models\Approver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Ambil data dengan pagination
        $approvers = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Tampilkan halaman menu.approvers.index dengan data approvers
        return view('menu.approvers.index', compact('approvers'));
    }
}
