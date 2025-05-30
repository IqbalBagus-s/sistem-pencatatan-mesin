@extends('layouts.index-layout')

@section('title', 'Pencatatan Mesin Hopper')

@section('page-title', 'Pencatatan Mesin Hopper')

@section('form-action')
    {{ route('hopper.index') }}
@endsection

@section('custom-filters')
    @if($currentGuard === 'approver')
    <div>
        <label for="search" class="block font-medium text-gray-700 mb-2">Cari berdasarkan nama Checker:</label>
        <input type="text" name="search" id="search" placeholder="Masukkan nama checker..." 
            value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    @endif

    <!-- Dropdown Filter Hopper -->
    <div x-data="{ 
        open: false, 
        selected: null,
        hoppers: Array.from({length: 15}, (_, i) => i + 1),
        reset() {
            this.selected = null;
            this.open = false;
        }
        }" class="relative w-full font-sans">
        <!-- Label -->
        <label class="block mb-2 font-medium text-gray-700">Filter Berdasarkan Nomor Hopper:</label>
        
        <!-- Rest of the code remains the same -->
        <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 relative">
            <span x-text="selected ? 'Hopper ' + selected : 'Pilih Hopper'" class="text-gray-800"></span>
            
            <!-- Selection Indicator -->
            <div class="absolute right-3 top-1/2 -translate-y-1/2">
                <!-- Checkmark when selected -->
                <svg x-show="selected" @click.stop="reset()" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                
                <!-- Dropdown Arrow when not selected -->
                <svg x-show="!selected" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </button>
        
        <!-- Dropdown List -->
        <div 
            x-show="open" 
            @click.away="open = false" 
            class="absolute left-0 mt-1 w-full bg-white border border-gray-300 shadow-lg rounded-md overflow-hidden z-10"
            style="display: none;"
        >
            <!-- Hopper List -->
            <div class="grid grid-cols-5 gap-1 p-1">
                <template x-for="hopper in hoppers" :key="hopper">
                    <div @click.stop>
                        <button 
                            type="button" 
                            @click="selected = hopper; open = false" 
                            :class="selected === hopper 
                                ? 'bg-blue-500 text-white' 
                                : 'text-gray-700 hover:bg-blue-100 hover:text-blue-800'"
                            class="w-full px-2 py-1.5 text-xs rounded-md transition-colors duration-200 ease-in-out"
                        >
                            <span x-text="'Hopper ' + hopper"></span>
                        </button>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Hidden Input untuk dikirim ke server -->
        <input type="hidden" name="search_hopper" x-model="selected">
    </div>

    <div>
        <label for="filter_bulan" class="block font-medium text-gray-700 mb-2">Filter berdasarkan Bulan:</label>
        <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
@endsection

@section('create-route')
    {{ route('hopper.create') }}
@endsection

@section('create-button-text')
    <i class="fas fa-plus mr-2"></i>Buat Baru
@endsection

@section('table-content')
    <table class="table-auto w-full">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="py-3 px-4 border-b border-gray-200 font-semibold w-1/6">No Hopper</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Bulan</th>
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
                        <td class="py-3 px-4 border-b border-gray-200 w-1/6">{{ $check->nomer_hopper }}</td>
                        <td 
                            x-data="{ 
                                formatMonth(monthYear) {
                                    const monthNames = [
                                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                    ];
                                    
                                    const [year, month] = monthYear.split('-');
                                    const monthIndex = parseInt(month) - 1;
                                    
                                    return `${monthNames[monthIndex]} ${year}`;
                                }
                            }"
                            x-text="formatMonth('{{ $check->bulan }}')"
                            class="py-3 px-4 border-b border-gray-200">
                        </td>
                        
                        <td class="py-3 px-4 border-b border-gray-200">
                            @php
                                $checkedByFields = [
                                    $check->checked_by_minggu1,
                                    $check->checked_by_minggu2,
                                    $check->checked_by_minggu3,
                                    $check->checked_by_minggu4
                                ];

                                // Remove duplicates and filter out null/empty values
                                $uniqueCheckedBy = array_unique(array_filter($checkedByFields));
                            @endphp
                            
                            @if(!empty($uniqueCheckedBy))
                                @foreach($uniqueCheckedBy as $checkedBy)
                                    <div class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm mb-1 inline-block">
                                        {{ $checkedBy }}
                                    </div>
                                @endforeach
                            @else
                                <span class="bg-gray-200 text-gray-700 px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Belum Diisi
                                </span>
                            @endif
                        </td>
                        
                        <td class="py-3 px-4 border-b border-gray-200">
                            @php
                                $approvedCount = collect([
                                    $check->approved_by_minggu1,
                                    $check->approved_by_minggu2,
                                    $check->approved_by_minggu3,
                                    $check->approved_by_minggu4,
                                ])->filter()->count();
                            @endphp

                            @if($check->status === 'disetujui')
                                <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui
                                </span>
                            @elseif($approvedCount > 0)
                                <span class="bg-amber-100 text-amber-700 px-4 py-1 rounded-full text-sm font-medium inline-block">
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
                            @if($currentGuard === 'approver')
                                <a href="{{ route('hopper.show', $check->id) }}" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                </a>
                            {{-- Menu edit --}}
                            @elseif($currentGuard === 'checker')
                                @if($check->status === 'belum_disetujui')
                                    <a href="{{ route('hopper.edit', $check->id) }}" title="Edit">
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

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection