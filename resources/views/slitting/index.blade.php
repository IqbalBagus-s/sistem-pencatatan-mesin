@extends('layouts.index-layout')

@section('title', 'Pencatatan Mesin Slitting')

@section('page-title', 'Pencatatan Mesin Slitting')

@section('form-action')
    {{ route('slitting.index') }}
@endsection

@section('custom-filters')
    @if(auth()->user() instanceof \App\Models\Approver)
    <div>
        <label for="search" class="block font-medium text-gray-700 mb-2">Cari berdasarkan nama Checker:</label>
        <input type="text" name="search" id="search" placeholder="Masukkan nama checker..." 
            value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    @endif

    <!-- Dropdown Filter Slitting -->
    <div x-data="{ 
                open: false, 
                selected: null,
                slittingMachines: [1, 2, 3],
                reset() {
                    this.selected = null;
                    this.open = false;
                }
            }" class="relative w-full font-sans">
        <!-- Label -->
        <label class="block mb-2 font-medium text-gray-700">Filter Berdasarkan Nomor Slitting:</label>
        
        <!-- Dropdown Button -->
        <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 relative">
            <span x-text="selected ? 'Slitting ' + selected : 'Pilih Slitting'" class="text-gray-800"></span>
            
            <!-- Icon -->
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
            <!-- Horizontal layout for options -->
            <div class="flex flex-row flex-wrap justify-center items-center gap-2 p-3">
                <template x-for="machine in slittingMachines" :key="machine">
                    <button 
                        type="button" 
                        @click="selected = machine; open = false" 
                        :class="selected === machine 
                            ? 'bg-blue-500 text-white' 
                            : 'bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-800'"
                        class="px-3 py-1 text-sm rounded-md transition-colors duration-200 ease-in-out text-center w-16"
                    >
                        <span x-text="machine"></span>
                    </button>
                </template>
            </div>
        </div>
        
        <!-- Hidden Input untuk dikirim ke server -->
        <input type="hidden" name="search_slitting" x-model="selected">
    </div>

    <!-- Filter Bulan -->
    <div>
        <label for="filter_bulan" class="block font-medium text-gray-700 mb-2">Filter berdasarkan Bulan:</label>
        <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
@endsection

@section('create-route')
    {{ route('slitting.create') }}
@endsection

@section('create-button-text')
    <i class="fas fa-plus mr-2"></i>Buat Baru
@endsection

@section('table-content')
    <table class="table-auto w-full">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">No Slitting</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Bulan</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Checker</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Status</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if($checks->isEmpty())
                <tr>
                    <td colspan="6" class="text-center py-4">Tidak ada data ditemukan.</td>
                </tr>
            @else
                @foreach($checks as $check)
                    <tr class="text-center hover:bg-gray-50">
                        <td class="py-3 px-4 border-b border-gray-200">{{ $check->nomer_slitting }}</td>
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
                            <div class="flex flex-col items-center space-y-1">
                                @php
                                    // Collect all non-empty checker names
                                    $checkers = collect([
                                        $check->checked_by_minggu1,
                                        $check->checked_by_minggu2,
                                        $check->checked_by_minggu3,
                                        $check->checked_by_minggu4
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
                            @php
                                $weekCount = 4; // Total minggu
                                $approvedCount = 0;
                                
                                // Periksa jika field approved_by_minggu1-4 terisi (tidak kosong)
                                if(!empty($check->approved_by_minggu1)) $approvedCount++;
                                if(!empty($check->approved_by_minggu2)) $approvedCount++;
                                if(!empty($check->approved_by_minggu3)) $approvedCount++;
                                if(!empty($check->approved_by_minggu4)) $approvedCount++;
                            @endphp
                            
                            @if($approvedCount == 0)
                                <span class="bg-pending text-pendingText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Belum Disetujui
                                </span>
                            @elseif($approvedCount < $weekCount)
                                <span class="bg-yellow-100 text-yellow-800 px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui Sebagian
                                </span>
                            @else
                                <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            {{-- Menu lihat --}}
                            @if(auth()->user() instanceof \App\Models\Approver)
                                <a href="{{ route('slitting.show', $check->id) }}" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                </a>
                            {{-- Menu edit --}}
                            @elseif(auth()->user() instanceof \App\Models\Checker)
                                @php
                                    // Perbaikan: Cek apakah disetujui sepenuhnya menggunakan $approvedCount
                                    $isFullyApproved = ($approvedCount == $weekCount);
                                @endphp
                                
                                @if(!$isFullyApproved)
                                    <a href="{{ route('slitting.edit', $check->id) }}" title="Edit">
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

@section('scripts')
    <script>
        // Additional JavaScript if needed
    </script>
@endsection