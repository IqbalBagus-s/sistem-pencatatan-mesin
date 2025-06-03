@extends('layouts.index-layout')

@section('title', 'Pencatatan Mesin Compressor')

@section('page-title', 'Pencatatan Mesin Compressor')

@section('form-action')
    {{ route('compressor.index') }}
@endsection

@section('create-route')
    {{ route('compressor.create') }}
@endsection

@section('create-button-text')
    <i class="fas fa-plus mr-2"></i>Buat Baru
@endsection


@section('custom-filters')
    @if($currentGuard === 'approver')
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
@endsection

@section('table-content')
    <table class="w-full">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Tanggal</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Hari</th>
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
                            {{ \Carbon\Carbon::parse($check->tanggal)->translatedFormat('d F Y') }}
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">{{ $check->hari }}</td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if($check->checkerShift1)
                                <span class="bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-sm mb-1 inline-block">
                                    Shift 1 :{{ $check->checkerShift1->username }}
                                </span>
                            @endif
                            @if($check->checkerShift2)
                                <span class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm mb-1 inline-block">
                                    Shift 2 :{{ $check->checkerShift2->username }}
                                </span>
                            @endif
                            @if(!$check->checkerShift1 && !$check->checkerShift2)
                                <span class="bg-gray-200 text-gray-700 px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Belum Diisi
                                </span>
                            @endif
                        </td>
                        
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if(($check->approver_shift1_id && !$check->approver_shift2_id) || (!$check->approver_shift1_id && $check->approver_shift2_id))
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui Sebagian
                                </span>
                            @elseif($check->status === 'disetujui')
                                <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui
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
                                <a href="{{ route('compressor.show', $check->id) }}" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                </a>
                            {{-- Menu edit --}}
                            @elseif($currentGuard === 'checker')
                                @if($check->status === 'belum_disetujui')
                                    <a href="{{ route('compressor.edit', $check->id) }}" title="Edit">
                                        <i class="fas fa-pen text-amber-500 text-lg hover:text-amber-600 cursor-pointer"></i>
                                    </a>
                                @else
                                    <i class="fas fa-pen text-amber-300 opacity-50 text-lg cursor-not-allowed" title="Tidak dapat diedit karena sudah disetujui sepenuhnya"></i>
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