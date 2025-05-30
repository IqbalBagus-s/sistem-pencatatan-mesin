<!-- resources/views/autoloader/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Autoloader')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Autoloader</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ $user->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('autoloader.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- Dropdown Pilih No Autoloader -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    }
                }" class="relative w-full">
                    <!-- Label -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">Pilih No Autoloader:</label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Autoloader ' + selected : 'Pilih Autoloader'"></span>
                        
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
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="i in 23" :key="i">
                                <div @click.stop>
                                    <button type="button" @click="selected = i; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'Autoloader ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server -->
                    <input type="hidden" name="nomer_autoloader" x-model="selected">
                </div>
                
                <!-- Dropdown Pilih Shift -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    }
                }" class="relative w-full">
                    <!-- Label -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">Pilih Shift:</label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Shift ' + selected : 'Pilih Shift'"></span>
                        
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
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-1 w-full bg-white border border-gray-300 shadow-lg rounded-md p-2 z-10">
                        <div class="grid grid-cols-3 gap-2">
                            <div @click.stop>
                                <button type="button" @click="selected = 1; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                    <span>Shift 1</span>
                                </button>
                            </div>
                            <div @click.stop>
                                <button type="button" @click="selected = 2; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                    <span>Shift 2</span>
                                </button>
                            </div>
                            <div @click.stop>
                                <button type="button" @click="selected = 3; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                    <span>Shift 3</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server -->
                    <input type="hidden" name="shift" x-model="selected">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">Pilih Bulan:</label>
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                </div>
            </div>                    
            @php
                // Items yang perlu di-check
                $items = [
                    1 => 'Filter',
                    2 => 'Selang',
                    3 => 'Panel Kelistrikan',
                    4 => 'Kontaktor',
                    5 => 'Thermal Overload',
                    6 => 'MCB',
                ];

                // Opsi check
                $options = [
                    'V' => 'V',
                    'X' => 'X',
                    '-' => '-',
                    'OFF' => 'OFF'
                ];
            @endphp
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <!-- Tabel untuk tanggal 1-11 -->
                <!-- Notifikasi scroll horizontal untuk mobile -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 1; $i <= 11; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-40" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 1; $i <= 11; $i++)
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
                                    
                                    @for($j = 1; $j <= 11; $j++)
                                        <td class="border border-gray-300 p-1 h-10">
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}">{{ $symbol }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan">
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: false, userName: '' }">
                                            <div class="mt-1 mb-1" x-show="selected">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ $user->username }}'; 
                                                        $refs.user{{ $j }}.value = userName;
                                                        $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                    } else {
                                                        userName = '';
                                                        $refs.user{{ $j }}.value = '';
                                                        $refs.checkNum{{ $j }}.value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Tabel untuk tanggal 12-22 -->
                <!-- Notifikasi scroll horizontal untuk mobile -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 12; $i <= 22; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-40" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 12; $i <= 22; $i++)
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
                                    
                                    @for($j = 12; $j <= 22; $j++)
                                        <td class="border border-gray-300 p-1 h-10">
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}">{{ $symbol }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan">
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 12; $j <= 22; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: false, userName: '' }">
                                            <div class="mt-1 mb-1" x-show="selected">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ $user->username }}'; 
                                                        $refs.user{{ $j }}.value = userName;
                                                        $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                    } else {
                                                        userName = '';
                                                        $refs.user{{ $j }}.value = '';
                                                        $refs.checkNum{{ $j }}.value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            
                <!-- Tabel untuk tanggal 23-31 -->
                <!-- Notifikasi scroll horizontal untuk mobile -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 23; $i <= 31; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-40" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 23; $i <= 31; $i++)
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
                                    
                                    @for($j = 23; $j <= 31; $j++)
                                        <td class="border border-gray-300 p-1 h-10">
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}">{{ $symbol }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan">
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 23; $j <= 31; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: false, userName: '' }">
                                            <div class="mt-1 mb-1" x-show="selected">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ $user->username }}'; 
                                                        $refs.user{{ $j }}.value = userName;
                                                        $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                    } else {
                                                        userName = '';
                                                        $refs.user{{ $j }}.value = '';
                                                        $refs.checkNum{{ $j }}.value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- catatan pemeriksaan --}}
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-6 rounded-xl shadow-md mb-8 border-l-4 border-blue-500">
                <h5 class="text-xl font-bold text-blue-700 mb-5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Pemeriksaan
                </h5>
                
                <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0 items-center justify-center">
                    <!-- Kriteria Pemeriksaan -->
                    <div class="bg-white p-6 rounded-lg border border-blue-200 shadow-sm w-full lg:w-2/3">
                        <h6 class="text-lg font-semibold text-blue-600 mb-4">Standar Kriteria Pemeriksaan:</h6>
                        <ul class="space-y-4 text-gray-800 text-sm">
                            @foreach ([
                                ['Filter', 'Kebersihan'],
                                ['Selang', 'Tidak bocor'],
                                ['Panel Kelistrikan', 'Berfungsi'],
                                ['Kontraktor', 'Baik'],
                                ['Temperatur Kontrol', 'Baik'],
                                ['MCB', 'Baik']
                            ] as [$title, $desc])
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 mr-2 text-green-500 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span><strong>{{ $title }}:</strong> {{ $desc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <!-- Keterangan Status -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-blue-200 w-full lg:w-1/3">
                        <p class="text-lg font-semibold text-blue-800 mb-4">Keterangan Status:</p>
                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-800">
                            @foreach ([
                                ['V', 'Baik/Normal', 'green'],
                                ['X', 'Tidak Baik/Abnormal', 'red'],
                                ['-', 'Tidak Diisi', 'gray'],
                                ['OFF', 'Mesin Mati', 'gray']
                            ] as [$symbol, $label, $color])
                                <div class="flex items-center">
                                    <span class="inline-block w-7 h-7 bg-{{ $color }}-100 text-{{ $color }}-700 text-center font-bold mr-3 rounded">{{ $symbol }}</span>
                                    <span>{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            @include('components.create-form-buttons', ['backRoute' => route('autoloader.index')])
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
                document.getElementById("hari").value = hari;
            });
        }
    });
</script>
@endsection