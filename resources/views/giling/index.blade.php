@extends('layouts.index-layout')

@section('title', 'Pemeriksaan Mesin Giling')

@section('page-title', 'Pemeriksaan Mesin Giling')

@section('form-action')
    {{ route('giling.index') }}
@endsection

@section('custom-filters')
    @if($currentGuard === 'approver')
        <div>
            <label for="search" class="block font-medium text-gray-700 mb-2">
                Cari berdasarkan nama Checker:
            </label>
            <input  id="search" name="search" placeholder="Masukkan nama checker…" value="{{ request('search') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md
                           focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
    @endif

    {{-- Filter Bulan --}}
    <div>
        <label for="filter_bulan" class="block font-medium text-gray-700 mb-2">
            Filter berdasarkan Bulan:
        </label>
        <input  type="month" id="filter_bulan" name="bulan" value="{{ request('bulan') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md
                       focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>

    {{-- Filter Minggu --}}
    <div>
        <label for="minggu" class="block font-medium text-gray-700 mb-2">
            Filter berdasarkan Minggu:
        </label>

        <div class="relative">
            <select name="minggu" id="minggu"
                class="w-full px-3 py-2 pr-11 border border-gray-300 rounded-md appearance-none
                    focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                <option value="">Semua Minggu</option>
                <option value="Minggu 1" {{ request('minggu') == 'Minggu 1' ? 'selected' : '' }}>Minggu 1</option>
                <option value="Minggu 2" {{ request('minggu') == 'Minggu 2' ? 'selected' : '' }}>Minggu 2</option>
                <option value="Minggu 3" {{ request('minggu') == 'Minggu 3' ? 'selected' : '' }}>Minggu 3</option>
                <option value="Minggu 4" {{ request('minggu') == 'Minggu 4' ? 'selected' : '' }}>Minggu 4</option>
            </select>

            <!-- Icon panah -->
            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </div>
        </div>
    </div>
@endsection

@section('create-route')
    {{ route('giling.create') }}
@endsection

@section('create-button-text')
    <i class="fas fa-plus mr-2"></i>Buat Baru
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
                        <td class="py-3 px-4 border-b border-gray-200">
                            <span class="bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-sm">
                                {{ $check->checker?->username }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if(($check->approver1?->username && !$check->approver2?->username) || (!$check->approver1?->username && $check->approver2?->username))
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui Sebagian
                                </span>
                            @elseif($check->status === 'disetujui')
                                <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui Lengkap
                                </span>
                            @else
                                <span class="bg-pending text-pendingText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Belum Disetujui
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            {{-- Menu lihat --}}
                            @if($currentGuard === 'approver')
                                <a href="{{ route('giling.show', $check->hashid) }}" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                </a>
                            {{-- Menu edit --}}
                            @elseif($currentGuard === 'checker')
                                @if($check->status === 'belum_disetujui')
                                    <a href="{{ route('giling.edit', $check->hashid) }}" title="Edit">
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

@section('pagination-data')
    @if(method_exists($checks, 'links') && $checks->hasPages())
        {{-- Menggunakan komponen pagination yang sudah dibuat --}}
        @include('components.pagination', ['paginator' => $checks])
    @endif
@endsection

@section('back-route')
    {{ route('dashboard') }}
@endsection