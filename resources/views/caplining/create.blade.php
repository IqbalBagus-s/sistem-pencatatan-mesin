<!-- resources/views/caplining/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Caplining')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Caplining</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('caplining.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Dropdown Pilih No Caplining -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    }
                }" class="relative w-full">
                    <!-- Label -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">Pilih No Caplining:</label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Caplining ' + selected : 'Pilih Caplining'"></span>
                        
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
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-1 w-full bg-white border border-gray-300 shadow-lg rounded-md p-2 z-50 max-h-60 overflow-y-auto">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="i in 6" :key="i">
                                <div @click.stop>
                                    <button type="button" @click="selected = i; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'Caplining ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server -->
                    <input type="hidden" name="nomer_caplining" x-model="selected">
                </div>
            </div>                    
            @php
                // Items yang perlu di-check (updated list sesuai permintaan)
                $items = [
                    1 => 'Kelistrikan',
                    2 => 'MCB',
                    3 => 'PLC',
                    4 => 'Power Supply',
                    5 => 'Relay',
                    6 => 'Selenoid',
                    7 => 'Selang Angin',
                    8 => 'Regulator',
                    9 => 'Pir',
                    10 => 'Motor',
                    11 => 'Vanbelt',
                    12 => 'Conveyor',
                    13 => 'Motor Conveyor',
                    14 => 'Vibrator',
                    15 => 'Motor Vibrator',
                    16 => 'Gear Box',
                    17 => 'Rantai',
                    18 => 'Stang Penggerak',
                    19 => 'Suction Pad',
                    20 => 'Sensor',
                ];

                // Opsi check dengan ikon
                $options = [
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];

                // Nama bulan dalam bahasa Indonesia (singkatan)
                $bulanSingkat = [
                    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 
                    6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 
                    11 => 'Nov', 12 => 'Des'
                ];
            @endphp

            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 1; $i <= 5; $i++)
                                <th class="border border-gray-300 bg-sky-50 p-2" colspan="1">
                                    <div x-data="{ 
                                        formattedDate: '', 
                                        showPicker: false,
                                        formatDate: function(value) {
                                            if (!value) return '';
                                            const date = new Date(value);
                                            const day = date.getDate();
                                            const month = date.getMonth();
                                            const year = date.getFullYear();
                                            
                                            // Mengambil nama bulan dari array PHP
                                            const monthNames = {{ json_encode($bulanSingkat) }};
                                            this.formattedDate = `${day} ${monthNames[month+1]} ${year}`;
                                            
                                            // Update hidden input untuk tanggal_i
                                            document.querySelector('input[name=\'tanggal_' + {{ $i }} + '\']').value = this.formattedDate;
                                            
                                            this.showPicker = false;
                                            return this.formattedDate;
                                        },
                                        clearDate: function() {
                                            if (this.formattedDate) {
                                                this.formattedDate = '';
                                                document.querySelector('input[name=\'tanggal_' + {{ $i }} + '\']').value = '';
                                                document.querySelector('input[name=\'tanggal_raw_' + {{ $i }} + '\']').value = '';
                                                return true;
                                            }
                                            return false;
                                        }
                                    }" class="relative">
                                        <!-- Tombol kalender dengan ikon dan tampilan tanggal -->
                                        <div class="flex items-center justify-center">
                                            <button type="button" 
                                                @click="formattedDate ? clearDate() : showPicker = !showPicker"
                                                class="flex items-center justify-center px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-50">
                                                <span x-show="!formattedDate" class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span class="text-xs">Pilih</span>
                                                </span>
                                                <span x-show="formattedDate" class="text-sm font-medium" x-text="formattedDate"></span>
                                            </button>
                                        </div>
                                        
                                        <!-- Date picker popup -->
                                        <div x-show="showPicker" 
                                            @click.outside="showPicker = false"
                                            class="absolute z-20 top-full left-0 mt-1 bg-white shadow-lg rounded border border-gray-200 p-1">
                                            <input type="date" 
                                                name="tanggal_raw_{{ $i }}" 
                                                @change="formatDate($event.target.value); showPicker = false;"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        </div>
                                        
                                        <input type="hidden" name="tanggal_{{ $i }}" :value="formattedDate">
                                    </div>
                                </th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-64" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 1; $i <= 5; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-20">Cek</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                    </td>
                                    
                                    @for($j = 1; $j <= 5; $j++)
                                        <td class="border border-gray-300 p-1 h-10">
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}">{!! $symbol !!}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan"
                                                style="min-width: 200px;">
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 5; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: false }">
                                            <div class="mt-1" x-show="selected">
                                                <span class="block w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                                    {{ Auth::user()->username }}
                                                </span>
                                            </div>
                                            <button type="button" 
                                                @click="selected = !selected;
                                                    if(selected) {
                                                        document.querySelector('input[name=\'checked_by{{ $j }}\']').value = '{{ Auth::user()->username }}';
                                                        document.querySelector('input[name=\'tanggal_check{{ $j }}\']').value = 
                                                        document.querySelector('input[name=\'tanggal_{{ $j }}\']').value;
                                                    } else {
                                                        document.querySelector('input[name=\'checked_by{{ $j }}\']').value = '';
                                                        document.querySelector('input[name=\'tanggal_check{{ $j }}\']').value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                            <!-- Hidden input to store who checked this column -->
                                            <input type="hidden" name="checked_by{{ $j }}" value="">
                                            <!-- Hidden input to store the check date for the controller -->
                                            <input type="hidden" name="tanggal_check{{ $j }}" value="">
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- catatan pemeriksaan --}}
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-5 rounded-lg shadow-sm mb-6 border-l-4 border-blue-400">
                <h5 class="text-lg font-semibold text-blue-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Pemeriksaan
                </h5>
                
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span> Pengecekan mesin, 4hari sebelum mesin dijadwalkan jalan.</span>
                    </li>
                </ul>
            </div>

            @include('components.create-form-buttons', ['backRoute' => route('caplining.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById("tanggal")) {
            document.getElementById("tanggal").addEventListener("change", function() {
                let tanggal = new Date(this.value);
                let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
                if (document.getElementById("hari")) {
                    document.getElementById("hari").value = hari;
                }
            });
        }
    });
    
</script>
@endsection