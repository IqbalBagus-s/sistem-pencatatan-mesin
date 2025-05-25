@extends('layouts.index-layout')

@section('title', 'Pencatatan Mesin Air Dryer')

@section('page-title', 'Pencatatan Mesin Air Dryer')

@section('form-action')
    {{ route('air-dryer.index') }}
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
@endsection

@section('create-route')
    {{ route('air-dryer.create') }}
@endsection

@section('create-button-text')
    <i class="fas fa-plus mr-2"></i>Buat Baru
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
                        <td class="py-3 px-4 border-b border-gray-200">{{ Carbon\Carbon::parse($check->tanggal)->locale('id')->isoFormat('D MMMM Y') }}</td>
                        <td class="py-3 px-4 border-b border-gray-200">{{ $check->hari }}</td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            <span class="bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-sm">
                                {{ $check->checked_by }}
                            </span>
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if($check->approved_by)
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
                            @if(auth()->user() instanceof \App\Models\Approver)
                                <a href="{{ route('air-dryer.show', $check->id) }}" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                </a>
                            {{-- Menu edit --}}
                            @elseif(auth()->user() instanceof \App\Models\Checker)
                                @if(!$check->approved_by)
                                    <a href="{{ route('air-dryer.edit', $check->id) }}" title="Edit">
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