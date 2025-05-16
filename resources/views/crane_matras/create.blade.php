<!-- resources/views/crane-matras/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Crane Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Crane Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('crane-matras.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Dropdown Pilih No Crane Matras - With Required Validation -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    error: false,
                    reset() {
                        this.selected = null;
                        this.open = false;
                        this.error = true;
                    },
                    validate() {
                        this.error = this.selected === null;
                        return !this.error;
                    }
                }" class="relative w-full">
                    <!-- Label with Required Indicator -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        Pilih No Crane Matras: <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- Dropdown Button with Error State -->
                    <button type="button" 
                        @click="open = !open" 
                        class="w-full h-10 px-3 py-2 bg-gray-100 border rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative"
                        :class="error ? 'border-red-500' : 'border-gray-300'">
                        <span x-text="selected ? 'Crane Matras ' + selected : 'Pilih Crane Matras'"></span>
                        
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
                    
                    <!-- Error Message -->
                    <div x-show="error" class="text-red-500 text-sm mt-1">
                        Silakan pilih No Crane Matras
                    </div>
                    
                    <!-- Dropdown List -->
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-1 w-full bg-white border border-gray-300 shadow-lg rounded-md p-2 z-50 max-h-60 overflow-y-auto">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="i in 3" :key="i">
                                <div @click.stop>
                                    <button type="button" @click="selected = i; open = false; error = false;" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'Crane Matras ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server (required) -->
                    <input type="hidden" name="nomer_crane_matras" x-model="selected" required x-on:invalid="error = true">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                        Pilih Bulan: <span class="text-red-500">*</span>
                    </label>
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                </div>
            </div>                  
            @php
                // Items yang perlu di-check
                $items = [
                    1 => 'INVERTER',
                    2 => 'KONTAKTOR',
                    3 => 'THERMAL OVERLOAD',
                    4 => 'PUSH BOTTOM',
                    5 => 'MOTOR',
                    6 => 'BREAKER',
                    7 => 'TRAFO',
                    8 => 'CONECTOR BUSBAR',
                    9 => 'REL BUSBAR',
                    10 => 'GREASE',
                    11 => 'RODA',
                    12 => 'RANTAI',
                ];

                // Opsi check
                $options = [
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];
            @endphp
            
            <!-- Input untuk menyimpan semua checked items -->
            @foreach($items as $i => $item)
                <input type="hidden" name="checked_items[{{ $i-1 }}]" value="{{ $item }}">
            @endforeach

            <!-- Tabel Inspeksi Crane Matras -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm sticky left-0 z-10" style="width: 40px;">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm sticky left-10 z-10" style="width: 180px; max-width: 180px;">Item Terperiksa</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" style="width: 80px;">Check</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" style="width: auto; min-width: 220px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10" style="width: 40px;">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10" style="width: 180px; max-width: 180px;">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center overflow-hidden text-ellipsis">{{ $item }}</div>
                                    </td>
                                    
                                    <!-- Check - DIUBAH MENJADI FORMAT ARRAY -->
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check[{{ $i-1 }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10">
                                        <input type="text" name="keterangan[{{ $i-1 }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1" style="width: 40px;">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10" style="width: 150px; max-width: 150px;">Dibuat Oleh</td>
                                
                                <td colspan="4" class="border border-gray-300 p-1 bg-sky-50">
                                    <div x-data="{ selected: false, userName: '', tanggal: '' }">
                                        <div class="mt-1" x-show="selected">
                                            <input type="text" name="checked_by_1" x-ref="user1" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" name="tanggal_1" x-ref="date1" x-bind:value="tanggal"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_1" x-ref="checkNum1" value="1">
                                        </div>
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user1.value = userName;
                                                    
                                                    // Format tanggal: DD Bulan YYYY
                                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    const today = new Date();
                                                    const day = today.getDate();
                                                    const month = monthNames[today.getMonth()];
                                                    const year = today.getFullYear();
                                                    tanggal = day + ' ' + month + ' ' + year;
                                                    
                                                    $refs.date1.value = tanggal;
                                                    $refs.checkNum1.value = '1';
                                                } else {
                                                    userName = '';
                                                    tanggal = '';
                                                    $refs.user1.value = '';
                                                    $refs.date1.value = '';
                                                    $refs.checkNum1.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Tombol Submit dan Kembali -->
            @include('components.create-form-buttons', ['backRoute' => route('crane-matras.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Menambahkan validasi sebelum form dikirim
        document.querySelector('form').addEventListener('submit', function(e) {
            // Mendapatkan komponen Alpine dari dropdown
            const dropdown = Alpine.evaluate(document.querySelector('[name="nomer_crane_matras"]').closest('[x-data]'), 'validate()');
            
            // Jika validasi gagal, hentikan pengiriman form
            if (!dropdown) {
                e.preventDefault();
            }
        });
        
        // Fungsi untuk format tanggal Indonesia
        Alpine.data('dateFormatter', () => ({
            formatDate() {
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const today = new Date();
                const day = today.getDate();
                const month = monthNames[today.getMonth()];
                const year = today.getFullYear();
                return day + ' ' + month + ' ' + year;
            }
        }));
    });
</script>
@endsection