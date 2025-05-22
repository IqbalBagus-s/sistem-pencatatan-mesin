<!-- resources/views/dehum/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Dehum Bahan')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Dehum Bahan</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('dehum-bahan.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Dropdown Pilih No Dehum - With Required Validation -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    },
                }" class="relative w-full">
                    <!-- Label with Required Indicator -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        Pilih No Dehum Bahan:
                    </label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" 
                        @click="open = !open" 
                        class="w-full h-10 px-3 py-2 bg-white border-blue-400 border rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Dehum ' + selected : 'Pilih dehum bahan'"></span>
                        
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
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-1 w-full bg-white border border-blue-400 shadow-lg rounded-md p-2 z-50 max-h-60 overflow-y-auto">
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="i in 9" :key="i">
                                <div @click.stop>
                                    <button type="button" @click="selected = i; open = false;" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'DB ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server (required) -->
                    <input type="hidden" name="nomer_dehum_bahan" x-model="selected">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                        Pilih Bulan:
                    </label>
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>                  
            
            @php
                // Items yang perlu di-check
                $items = [
                    1 => 'Filter',
                    2 => 'Selang',
                    3 => 'Kontraktor',
                    4 => 'Temperatur Control',
                    5 => 'MCB',
                    6 => 'Dew Point',
                ];
                
                // Mengubah semua opsi menjadi V, X, -, dan OFF untuk semua item
                $options = [
                    1 => ['V', 'X', '-', 'OFF'],
                    2 => ['V', 'X', '-', 'OFF'],
                    3 => ['V', 'X', '-', 'OFF'],
                    4 => ['V', 'X', '-', 'OFF'],
                    5 => ['V', 'X', '-', 'OFF'],
                    6 => ['V', 'X', '-', 'OFF'],
                ];
            @endphp
            
            <!-- Input untuk menyimpan semua checked items -->
            @foreach($items as $i => $item)
                <input type="hidden" name="checked_items[{{ $i }}]" value="{{ $item }}">
            @endforeach
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <div class="md:hidden text-sm text-gray-500 italic mb-2">
                        ← Geser ke kanan untuk melihat semua kolom →
                    </div>
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 text-sm sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10" colspan="1">Minggu</th>
                                
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" colspan="1">01</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 text-sm" rowspan="2">Keterangan</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" colspan="1">02</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 text-sm" rowspan="2">Keterangan</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" colspan="1">03</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 text-sm" rowspan="2">Keterangan</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" colspan="1">04</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 text-sm" rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10">Item Terperiksa</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                @if($i == 3)
                                <tr>
                                    <td colspan="10" class="border border-gray-300 text-center p-2 bg-gray-100 font-medium text-xs">
                                        Panel Kelistrikan
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                    </td>
                                    
                                    <!-- Minggu 1 -->
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check_1[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options[$i] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10">
                                        <input type="text" name="keterangan_1[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                    
                                    <!-- Minggu 2 -->
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check_2[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options[$i] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10">
                                        <input type="text" name="keterangan_2[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                    
                                    <!-- Minggu 3 -->
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check_3[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options[$i] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10">
                                        <input type="text" name="keterangan_3[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                    
                                    <!-- Minggu 4 -->
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check_4[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options[$i] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10">
                                        <input type="text" name="keterangan_4[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10 w-24">Dibuat Oleh</td>
                                
                                <!-- Minggu 1 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 w-32">
                                    <div x-data="{ selected: false, userName: '', tanggal: '' }" class="w-full">
                                        <div class="mt-1" x-show="selected">
                                            <input type="text" name="created_by_1" x-ref="user1" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" x-ref="displayDate1" x-bind:value="tanggal"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="created_date_1" x-ref="date1">
                                        </div>
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user1.value = userName;
                                                    
                                                    // Format tanggal untuk tampilan: DD Bulan YYYY
                                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    const today = new Date();
                                                    const day = today.getDate();
                                                    const month = monthNames[today.getMonth()];
                                                    const year = today.getFullYear();
                                                    tanggal = day + ' ' + month + ' ' + year;
                                                    
                                                    // Format tanggal untuk database: YYYY-MM-DD
                                                    const dbMonth = String(today.getMonth() + 1).padStart(2, '0');
                                                    const dbDay = String(today.getDate()).padStart(2, '0');
                                                    const dbDate = `${year}-${dbMonth}-${dbDay}`;
                                                    
                                                    $refs.displayDate1.value = tanggal;
                                                    $refs.date1.value = dbDate;
                                                } else {
                                                    userName = '';
                                                    tanggal = '';
                                                    $refs.user1.value = '';
                                                    $refs.displayDate1.value = '';
                                                    $refs.date1.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1 max-w-full"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- Minggu 2 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 w-32">
                                    <div x-data="{ selected: false, userName: '', tanggal: '' }" class="w-full">
                                        <div class="mt-1" x-show="selected">
                                            <input type="text" name="created_by_2" x-ref="user2" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" x-ref="displayDate2" x-bind:value="tanggal"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="created_date_2" x-ref="date2">
                                        </div>
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user2.value = userName;
                                                    
                                                    // Format tanggal untuk tampilan: DD Bulan YYYY
                                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    const today = new Date();
                                                    const day = today.getDate();
                                                    const month = monthNames[today.getMonth()];
                                                    const year = today.getFullYear();
                                                    tanggal = day + ' ' + month + ' ' + year;
                                                    
                                                    // Format tanggal untuk database: YYYY-MM-DD
                                                    const dbMonth = String(today.getMonth() + 1).padStart(2, '0');
                                                    const dbDay = String(today.getDate()).padStart(2, '0');
                                                    const dbDate = `${year}-${dbMonth}-${dbDay}`;
                                                    
                                                    $refs.displayDate2.value = tanggal;
                                                    $refs.date2.value = dbDate;
                                                } else {
                                                    userName = '';
                                                    tanggal = '';
                                                    $refs.user2.value = '';
                                                    $refs.displayDate2.value = '';
                                                    $refs.date2.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1 max-w-full"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- Minggu 3 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 w-32">
                                    <div x-data="{ selected: false, userName: '', tanggal: '' }" class="w-full">
                                        <div class="mt-1" x-show="selected">
                                            <input type="text" name="created_by_3" x-ref="user3" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" x-ref="displayDate3" x-bind:value="tanggal"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="created_date_3" x-ref="date3">
                                        </div>
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user3.value = userName;
                                                    
                                                    // Format tanggal untuk tampilan: DD Bulan YYYY
                                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    const today = new Date();
                                                    const day = today.getDate();
                                                    const month = monthNames[today.getMonth()];
                                                    const year = today.getFullYear();
                                                    tanggal = day + ' ' + month + ' ' + year;
                                                    
                                                    // Format tanggal untuk database: YYYY-MM-DD
                                                    const dbMonth = String(today.getMonth() + 1).padStart(2, '0');
                                                    const dbDay = String(today.getDate()).padStart(2, '0');
                                                    const dbDate = `${year}-${dbMonth}-${dbDay}`;
                                                    
                                                    $refs.displayDate3.value = tanggal;
                                                    $refs.date3.value = dbDate;
                                                } else {
                                                    userName = '';
                                                    tanggal = '';
                                                    $refs.user3.value = '';
                                                    $refs.displayDate3.value = '';
                                                    $refs.date3.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1 max-w-full"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- Minggu 4 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 w-32">
                                    <div x-data="{ selected: false, userName: '', tanggal: '' }" class="w-full">
                                        <div class="mt-1" x-show="selected">
                                            <input type="text" name="created_by_4" x-ref="user4" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" x-ref="displayDate4" x-bind:value="tanggal"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="created_date_4" x-ref="date4">
                                        </div>
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user4.value = userName;
                                                    
                                                    // Format tanggal untuk tampilan: DD Bulan YYYY
                                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    const today = new Date();
                                                    const day = today.getDate();
                                                    const month = monthNames[today.getMonth()];
                                                    const year = today.getFullYear();
                                                    tanggal = day + ' ' + month + ' ' + year;
                                                    
                                                    // Format tanggal untuk database: YYYY-MM-DD
                                                    const dbMonth = String(today.getMonth() + 1).padStart(2, '0');
                                                    const dbDay = String(today.getDate()).padStart(2, '0');
                                                    const dbDate = `${year}-${dbMonth}-${dbDay}`;
                                                    
                                                    $refs.displayDate4.value = tanggal;
                                                    $refs.date4.value = dbDate;
                                                } else {
                                                    userName = '';
                                                    tanggal = '';
                                                    $refs.user4.value = '';
                                                    $refs.displayDate4.value = '';
                                                    $refs.date4.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1 max-w-full"
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
            
            {{-- catatan pemeriksaan --}}
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-5 rounded-lg shadow-sm mb-6 border-l-4 border-blue-400">
                <h5 class="text-lg font-semibold text-blue-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Pemeriksaan
                </h5>
                
                <div class="p-3 bg-blue-50 rounded-lg col-span-1 md:col-span-2 lg:col-span-3 mb-4">
                    <p class="font-semibold text-blue-800 mb-1">Keterangan Status:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-700">
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-green-100 text-green-700 text-center font-bold mr-2 rounded">V</span>
                            <span>Baik/Normal</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-red-100 text-red-700 text-center font-bold mr-2 rounded">X</span>
                            <span>Tidak Baik/Abnormal</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-gray-100 text-gray-700 text-center font-bold mr-2 rounded">-</span>
                            <span>Tidak Diisi</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-gray-100 text-gray-700 text-center font-bold mr-2 rounded">OFF</span>
                            <span>Mesin Mati</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-lg border border-blue-100">
                    <h6 class="font-medium text-blue-600 mb-2">Standar Kriteria Pemeriksaan:</h6>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">Filter:</span> Kebersihan</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">Selang:</span> Tidak bocor</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">Kontraktor:</span> Baik</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">Temperatur Control:</span> Baik</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">MCB:</span> Baik</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">Dew Point:</span> Berfungsi</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Tombol Submit dan Kembali -->
            @include('components.create-form-buttons', ['backRoute' => route('dehum-bahan.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>

</script>
@endsection