@extends('layouts.index-layout')

@section('title', 'Pemeriksaan Mesin Giling')

@section('page-title', 'Pemeriksaan Mesin Giling')

@section('form-action')
    {{ route('giling.index') }}
@endsection

@section('custom-filters')
    @if(auth()->user() instanceof \App\Models\Approver)
    <div>
        <label for="search" class="block font-medium text-gray-700 mb-2">Cari berdasarkan nama Checker:</label>
        <input type="text" name="search" id="search" placeholder="Masukkan nama checker..." 
            value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    @endif
    <div>
        <label for="filter_bulan" class="block font-medium text-gray-700 mb-2">Filter berdasarkan Bulan:</label>
        <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    <div>
        <label for="minggu" class="block font-medium text-gray-700 mb-2">Filter berdasarkan Minggu:</label>
        <select name="minggu" id="minggu" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
            <option value="">Semua Minggu</option>
            <option value="Minggu 1" {{ request('minggu') == 'Minggu 1' ? 'selected' : '' }}>Minggu 1</option>
            <option value="Minggu 2" {{ request('minggu') == 'Minggu 2' ? 'selected' : '' }}>Minggu 2</option>
            <option value="Minggu 3" {{ request('minggu') == 'Minggu 3' ? 'selected' : '' }}>Minggu 3</option>
            <option value="Minggu 4" {{ request('minggu') == 'Minggu 4' ? 'selected' : '' }}>Minggu 4</option>
        </select>
    </div>
@endsection

@section('create-route')
    {{ route('giling.create') }}
@endsection

@section('create-button-text')
    Tambah Pemeriksaan
@endsection

@section('table-content')
    <table class="w-full">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Bulan</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Minggu</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Checker</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Status</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if($checks->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-4">Tidak ada data ditemukan.</td>
                </tr>
            @else
                @foreach($checks as $check)
                    <tr class="text-center hover:bg-gray-50">
                        <td class="py-3 px-4 border-b border-gray-200">
                            @php
                                // Untuk format YYYY-MM
                                $bulan = $check->bulan;
                                
                                // Cek apakah format tahun-bulan (contoh: 2025-04)
                                if (preg_match('/^(\d{4})-(\d{1,2})$/', $bulan, $matches)) {
                                    $tahun = $matches[1];
                                    $bulanAngka = (int)$matches[2];
                                    
                                    $namaBulan = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 
                                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 
                                        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    
                                    echo $namaBulan[$bulanAngka] . ' ' . $tahun;
                                } else {
                                    // Tampilkan apa adanya jika format tidak sesuai
                                    echo $bulan;
                                }
                            @endphp
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">{{ $check->minggu }}</td>
                        <td class="py-3 px-4 border-b border-gray-200">{{ $check->checked_by }}</td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if($check->approved_by1 && $check->approved_by2)
                                <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui Lengkap
                                </span>
                            @elseif($check->approved_by1 || $check->approved_by2)
                                <span class="bg-warning text-warningText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui Sebagian
                                </span>
                            @else
                                <span class="bg-pending text-pendingText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Belum Disetujui
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            {{-- Menu lihat --}}
                            @if(auth()->user() instanceof \App\Models\Approver)
                                <a href="{{ route('giling.show', $check->id) }}" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                </a>
                            {{-- Menu edit --}}
                            @elseif(auth()->user() instanceof \App\Models\Checker)
                                @if(!$check->approved_by1 && !$check->approved_by2)
                                    <a href="{{ route('giling.edit', $check->id) }}" title="Edit">
                                        <i class="fas fa-pen text-amber-500 text-lg hover:text-amber-600 cursor-pointer"></i>
                                    </a>
                                @else
                                    <i class="fas fa-pen text-amber-300 opacity-50 text-lg cursor-not-allowed" title="Tidak dapat diedit karena sudah disetujui"></i>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
@endsection

@section('pagination')
    <div class="flex justify-center mt-4">
        <div class="flex flex-wrap gap-1 justify-center">
            <!-- Previous button -->
            @if (!$checks->onFirstPage())
                <a href="{{ $checks->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-primary hover:bg-gray-100 transition duration-200">&laquo; Previous</a>
            @endif
            
            <!-- Page numbers -->
            @foreach ($checks->getUrlRange(1, $checks->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="px-3 py-2 border {{ $page == $checks->currentPage() ? 'bg-primary text-white border-primary font-bold' : 'bg-white text-primary border-gray-300 hover:bg-gray-100' }} rounded-md transition duration-200">
                    {{ $page }}
                </a>
            @endforeach
            
            <!-- Next button -->
            @if ($checks->hasMorePages())
                <a href="{{ $checks->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-primary hover:bg-gray-100 transition duration-200">Next &raquo;</a>
            @endif
        </div>
    </div>
@endsection

@section('back-route')
    {{ route('dashboard') }}
@endsection