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
                            @if($check->checked_by_shift1)
                                <span class="bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-sm mb-1 inline-block">
                                    Shift 1 :{{ $check->checked_by_shift1 }}
                                </span>
                                
                            @endif
                            @if($check->checked_by_shift2)
                                <span class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm mb-1 inline-block">
                                    Shift 2 :{{ $check->checked_by_shift2 }}
                                </span>
                            @endif
                            @if(!$check->checked_by_shift1 && !$check->checked_by_shift2)
                                <span class="bg-gray-200 text-gray-700 px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Belum Diisi
                                </span>
                            @endif
                        </td>
                        
                        <td class="py-3 px-4 border-b border-gray-200">
                            @php
                                $isFullyApproved = !is_null($check->approved_by_shift1) && !is_null($check->approved_by_shift2);
                                $isPartiallyApproved = !is_null($check->approved_by_shift1) || !is_null($check->approved_by_shift2);
                            @endphp
                            
                            @if($isFullyApproved)
                                <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui
                                </span>
                            @elseif($isPartiallyApproved)
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-1 rounded-full text-sm font-medium inline-block">
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
                                <a href="{{ route('compressor.show', $check->id) }}" title="Lihat Detail">
                                    @if($isFullyApproved)
                                        <i class="fas fa-eye text-primary opacity-70" title="Sudah disetujui"></i>
                                    @else
                                        <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                    @endif
                                </a>
                            {{-- Menu edit --}}
                            @elseif(auth()->user() instanceof \App\Models\Checker)
                                @php
                                    $canEdit = !$isFullyApproved;
                                @endphp
                                
                                @if($canEdit)
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