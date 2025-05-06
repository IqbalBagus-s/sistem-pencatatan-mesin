@extends('layouts.index-layout')

@section('title', 'Pencatatan Crane Matras')

@section('page-title', 'Pencatatan Crane Matras')

@section('form-action')
    {{ route('crane-matras.index') }}
@endsection

@section('custom-filters')
    @if(auth()->user() instanceof \App\Models\Approver)
    <div>
        <label for="search" class="block font-medium text-gray-700 mb-2">Cari berdasarkan nama Checker:</label>
        <input type="text" name="search" id="search" placeholder="Masukkan nama checker..." 
            value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    @endif

    <!-- Dropdown Filter Crane Matras -->
    <div x-data="{ 
        open: false, 
        selected: null,
        cranes: Array.from({length: 3}, (_, i) => i + 1),
        reset() {
            this.selected = null;
            this.open = false;
        }
        }" class="relative w-full font-sans">
        <!-- Label -->
        <label class="block mb-2 font-medium text-gray-700">Filter Berdasarkan Nomor Crane Matras:</label>
        
        <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 relative">
            <span x-text="selected ? 'Crane ' + selected : 'Pilih Crane Matras'" class="text-gray-800"></span>
            
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
            <!-- Crane List -->
            <div class="flex flex-row flex-wrap justify-center items-center gap-2 p-3">
                <template x-for="crane in cranes" :key="crane">
                    <button 
                        type="button" 
                        @click="selected = crane; open = false" 
                        :class="selected === crane 
                            ? 'bg-blue-500 text-white' 
                            : 'bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-800'"
                        class="px-3 py-1 text-sm rounded-md transition-colors duration-200 ease-in-out text-center w-16"
                    >
                        <span x-text="crane"></span>
                    </button>
                </template>
            </div>
        </div>
        
        <!-- Hidden Input untuk dikirim ke server -->
        <input type="hidden" name="crane" x-model="selected">
    </div>

    <!-- Filter Bulan -->
    <div>
        <label for="filter_bulan" class="block font-medium text-gray-700 mb-2">Filter berdasarkan Bulan:</label>
        <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
@endsection

@section('create-route')
    {{ route('crane-matras.create') }}
@endsection

@section('create-button-text')
    Tambah Pencatatan
@endsection

@section('table-content')
    <table class="table-auto w-full">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">No Crane Matras</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Bulan</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Checker</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Status</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if($results->isEmpty())
                <tr>
                    <td colspan="6" class="text-center py-4">Tidak ada data ditemukan.</td>
                </tr>
            @else
                @foreach($results as $result)
                    <tr class="text-center hover:bg-gray-50">
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if(isset($result->craneMatrasCheck))
                                {{ $result->craneMatrasCheck->nomer_crane_matras }}
                            @else
                                {{ $check ? $check->nomer_crane_matras : 'N/A' }}
                            @endif
                        </td>
                        <td 
                            x-data="{ 
                                formatMonth(monthYear) {
                                    const monthNames = [
                                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                                    ];
                                    
                                    if (!monthYear) return 'N/A';
                                    
                                    // Handle different format possibilities
                                    let year, month;
                                    
                                    if (monthYear.includes('-')) {
                                        [year, month] = monthYear.split('-');
                                    } else if (monthYear.includes('/')) {
                                        [month, year] = monthYear.split('/');
                                    } else if (monthYear.length === 1 || monthYear.length === 2) {
                                        month = monthYear;
                                        year = new Date().getFullYear();
                                    } else {
                                        return monthYear; // Return as is if format is unknown
                                    }
                                    
                                    const monthIndex = parseInt(month) - 1;
                                    if (isNaN(monthIndex) || monthIndex < 0 || monthIndex > 11) return monthYear;
                                    
                                    return `${monthNames[monthIndex]} ${year}`;
                                }
                            }"
                            x-text="formatMonth('{{ isset($result->craneMatrasCheck) ? $result->craneMatrasCheck->bulan : ($check ? $check->bulan : '') }}')"
                            class="py-3 px-4 border-b border-gray-200">
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            <div class="flex flex-col items-center space-y-1">
                                @php
                                    // Collect all non-empty checker names
                                    $checkers = collect([
                                        $result->checked_by_minggu1 ?? null,
                                        $result->checked_by_minggu2 ?? null,
                                        $result->checked_by_minggu3 ?? null,
                                        $result->checked_by_minggu4 ?? null
                                    ])->filter()->unique()->values();
                                @endphp
                                
                                @forelse($checkers as $checker)
                                    <div class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm inline-block">
                                        {{ $checker }}
                                    </div>
                                @empty
                                    <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">
                                        Belum Diisi
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if($result->approved_by)
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
                                <a href="{{ route('crane-matras.show', $result->id) }}" title="Lihat Detail">
                                    @if($result->approved_by)
                                        <i class="fas fa-eye text-primary opacity-70" title="Sudah disetujui"></i>
                                    @else
                                        <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                    @endif
                                </a>
                            {{-- Menu edit --}}
                            @elseif(auth()->user() instanceof \App\Models\Checker)
                                @if(!$result->approved_by)
                                    <a href="{{ route('crane-matras.edit', $result->id) }}" title="Edit">
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
            @if (!$results->onFirstPage())
                <a href="{{ $results->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-primary hover:bg-gray-100 transition duration-200">&laquo; Previous</a>
            @endif
            
            <!-- Page numbers -->
            @foreach ($results->getUrlRange(1, $results->lastPage()) as $page => $url)
                <a href="{{ $url }}" class="px-3 py-2 border {{ $page == $results->currentPage() ? 'bg-primary text-white border-primary font-bold' : 'bg-white text-primary border-gray-300 hover:bg-gray-100' }} rounded-md transition duration-200">
                    {{ $page }}
                </a>
            @endforeach
            
            <!-- Next button -->
            @if ($results->hasMorePages())
                <a href="{{ $results->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-primary hover:bg-gray-100 transition duration-200">Next &raquo;</a>
            @endif
        </div>
    </div>
@endsection

@section('back-route')
    {{ route('dashboard') }}
@endsection

@section('scripts')
    
@endsection