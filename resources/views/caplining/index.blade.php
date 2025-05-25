@extends('layouts.index-layout')

@section('title', 'Pencatatan Mesin Caplining')

@section('page-title', 'Pencatatan Mesin Caplining')

@section('form-action')
    {{ route('caplining.index') }}
@endsection

@section('custom-filters')
    @if(auth()->user() instanceof \App\Models\Approver)
    <div>
        <label for="search" class="block font-medium text-gray-700 mb-2">Cari berdasarkan nama Checker:</label>
        <input type="text" name="search" id="search" placeholder="Masukkan nama checker..." 
            value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
    @endif

    <!-- Dropdown Filter Caplining dengan nomor -->
    <div x-data="{ 
        open: false, 
        selected: null,
        caplinings: Array.from({length: 6}, (_, i) => i + 1),
        reset() {
            this.selected = null;
            this.open = false;
        }
        }" class="relative w-full font-sans">
        <!-- Label -->
        <label class="block mb-2 font-medium text-gray-700">Filter Berdasarkan Nomor Caplining:</label>
        
        <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 relative">
            <span x-text="selected ? 'Caplining ' + selected : 'Pilih Caplining'" class="text-gray-800"></span>
            
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
        
        <!-- Dropdown List - Horizontal layout for options -->
        <div 
            x-show="open" 
            @click.away="open = false" 
            class="absolute left-0 mt-1 w-full bg-white border border-gray-300 shadow-lg rounded-md overflow-hidden z-10"
            style="display: none;"
        >
            <!-- Caplining List - Grid layout for better spacing -->
            <div class="p-2 grid grid-cols-3 gap-2">
                <template x-for="caplining in caplinings" :key="caplining">
                    <button 
                        type="button" 
                        @click="selected = caplining; open = false" 
                        :class="selected === caplining 
                            ? 'bg-blue-500 text-white' 
                            : 'text-gray-700 hover:bg-blue-100 hover:text-blue-800'"
                        class="px-4 py-2 text-sm rounded-md transition-colors duration-200 ease-in-out flex justify-center"
                    >
                        <span x-text="caplining"></span>
                    </button>
                </template>
            </div>
        </div>
        
        <!-- Hidden Input untuk dikirim ke server -->
        <input type="hidden" name="search_caplining" x-model="selected">
    </div>
    
    <!-- Filter Tanggal -->
    <div>
        <label for="filter_tanggal" class="block font-medium text-gray-700 mb-2">Filter berdasarkan Tanggal:</label>
        <input type="date" name="tanggal" id="filter_tanggal" value="{{ request('tanggal') }}" 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
    </div>
@endsection

@section('create-route')
    {{ route('caplining.create') }}
@endsection

@section('create-button-text')
    <i class="fas fa-plus mr-2"></i>Buat Baru
@endsection

@section('table-content')
    <table class="table-auto w-full">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">No Caplining</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Tanggal</th>
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
                        <td class="py-3 px-4 border-b border-gray-200">{{ $check->nomer_caplining }}</td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if (!$check->hasTanggal)
                                <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm inline-block">Belum Diisi</span>
                            @else
                                {{ $check->tanggalFormatted }}
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if(count($check->allCheckers) > 0)
                                <div class="flex flex-col items-center space-y-1">
                                    @foreach($check->allCheckers as $checker)
                                        <div class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm inline-block">
                                            {{ $checker }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm inline-block">
                                    Belum Diisi
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 border-b border-gray-200">
                            @if($check->approvalStatus === 'fully_approved')
                                <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                    Disetujui Penuh
                                </span>
                            @elseif($check->approvalStatus === 'partially_approved')
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
                                <a href="{{ route('caplining.show', $check->id) }}" title="Lihat Detail">
                                    <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                </a>
                            {{-- Menu edit --}}
                            @elseif(auth()->user() instanceof \App\Models\Checker)
                                @if(!$check->isApproved)
                                    <a href="{{ route('caplining.edit', $check->id) }}" title="Edit">
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
    <script>
        // Any additional JavaScript can be added here
    </script>
@endsection