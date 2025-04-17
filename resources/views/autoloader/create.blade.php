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
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('autoloader.store') }}" method="POST">
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
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
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
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-1 w-full bg-white border border-gray-300 shadow-lg rounded-md p-2 z-10 max-h-60 overflow-y-auto">
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
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
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
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                </div>
            </div>                    

            <!-- Tabel Inspeksi -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 w-10" rowspan="2">No.</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28" colspan="1">Tanggal</th>
                            
                            <?php
                            // Generate header columns from 01 to 11
                            for ($i = 1; $i <= 11; $i++) {
                                $num = str_pad($i, 2, '0', STR_PAD_LEFT); // Format number as 01, 02, etc.
                                echo '<th class="border border-gray-300 bg-sky-50 p-2" colspan="1">' . $num . '</th>';
                                echo '<th class="border border-gray-300 bg-sky-50 p-2 w-32" rowspan="2">Keterangan</th>';
                            }
                            ?>
                        </tr>
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28">Checked Items</th>
                            <?php
                            // Generate "Check" cells for each column
                            for ($i = 1; $i <= 11; $i++) {
                                echo '<th class="border border-gray-300 bg-sky-50 p-2">Check</th>';
                            }
                            ?>
                        </tr>
                    </thead>                        
                    <tbody>
                        @php
                            $items = [
                                1 => 'Filter',
                                2 => 'Selang',
                                3 => 'Panel Kelistrikan',
                                4 => 'Kontaktor',
                                5 => 'Thermal Overload',
                                6 => 'MCB',
                            ];
                            
                            $options = [
                                'V' => '✓',
                                'X' => '✗',
                                '-' => '—',
                                'OFF' => 'OFF'
                            ];
                        @endphp
                        
                        @foreach($items as $i => $item)
                            <tr>
                                <td class="border border-gray-300 text-center p-1 h-10 text-xs">{{ $i }}</td>
                                <td class="border border-gray-300 p-1 h-10">
                                    <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                </td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-1 py-0 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10">
                                        <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                            class="w-full h-8 px-1 py-0 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- badan tabel kedua untuk mencatat checker --}}
                    <tbody class="bg-white">
                        <tr class="bg-sky-50">
                            <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs" rowspan="1">-</td>
                            <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs">Dibuat Oleh</td>
                            
                            <!-- Check 1 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user1.value = userName;
                                                $refs.checkNum1.value = '1';
                                            } else {
                                                userName = '';
                                                $refs.user1.value = '';
                                                $refs.checkNum1.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_1" x-ref="user1" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_1" x-ref="checkNum1" value="1">
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Check 2 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user2.value = userName;
                                                $refs.checkNum2.value = '2';
                                            } else {
                                                userName = '';
                                                $refs.user2.value = '';
                                                $refs.checkNum2.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_2" x-ref="user2" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_2" x-ref="checkNum2" value="2">
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Check 3 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user3.value = userName;
                                                $refs.checkNum3.value = '3';
                                            } else {
                                                userName = '';
                                                $refs.user3.value = '';
                                                $refs.checkNum3.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_3" x-ref="user3" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_3" x-ref="checkNum3" value="3">
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Check 4 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user4.value = userName;
                                                $refs.checkNum4.value = '4';
                                            } else {
                                                userName = '';
                                                $refs.user4.value = '';
                                                $refs.checkNum4.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_4" x-ref="user4" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_4" x-ref="checkNum4" value="4">
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Check 5 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user5.value = userName;
                                                $refs.checkNum5.value = '5';
                                            } else {
                                                userName = '';
                                                $refs.user5.value = '';
                                                $refs.checkNum5.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_5" x-ref="user5" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_5" x-ref="checkNum5" value="5">
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Check 6 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user6.value = userName;
                                                $refs.checkNum6.value = '6';
                                            } else {
                                                userName = '';
                                                $refs.user6.value = '';
                                                $refs.checkNum6.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_6" x-ref="user6" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_6" x-ref="checkNum6" value="6">
                                    </div>
                                </div> 
                            </td>
                            
                            <!-- Check 7 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user7.value = userName;
                                                $refs.checkNum7.value = '7';
                                            } else {
                                                userName = '';
                                                $refs.user7.value = '';
                                                $refs.checkNum7.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_7" x-ref="user7" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_7" x-ref="checkNum7" value="7">
                                    </div>
                                </div>
                            </td>
                            <!-- Check 8 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user8.value = userName;
                                                $refs.checkNum8.value = '8';
                                            } else {
                                                userName = '';
                                                $refs.user8.value = '';
                                                $refs.checkNum8.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_8" x-ref="user8" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_8" x-ref="checkNum8" value="8">
                                    </div>
                                </div>
                            </td>

                            <!-- Check 9 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user9.value = userName;
                                                $refs.checkNum9.value = '9';
                                            } else {
                                                userName = '';
                                                $refs.user9.value = '';
                                                $refs.checkNum9.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_9" x-ref="user9" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_9" x-ref="checkNum9" value="9">
                                    </div>
                                </div>
                            </td>

                            <!-- Check 10 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user10.value = userName;
                                                $refs.checkNum10.value = '10';
                                            } else {
                                                userName = '';
                                                $refs.user10.value = '';
                                                $refs.checkNum10.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_10" x-ref="user10" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_10" x-ref="checkNum10" value="10">
                                    </div>
                                </div>
                            </td>

                            <!-- Check 11 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }">
                                    <button type="button" 
                                        @click="selected = !selected; 
                                            if(selected) {
                                                userName = '{{ Auth::user()->username }}'; 
                                                $refs.user11.value = userName;
                                                $refs.checkNum11.value = '11';
                                            } else {
                                                userName = '';
                                                $refs.user11.value = '';
                                                $refs.checkNum11.value = '';
                                            }"
                                        class="w-full px-1 py-0 text-xs border border-gray-300 rounded text-center"
                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                    </button>
                                    <div class="mt-1" x-show="selected">
                                        <input type="text" name="checked_by_11" x-ref="user11" x-bind:value="userName"
                                            class="w-full px-1 py-0 text-xs bg-gray-100 border border-gray-300 rounded"
                                            readonly>
                                        <input type="hidden" name="check_num_11" x-ref="checkNum11" value="11">
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between mt-6">
                <a href="{{ route('autoloader.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Kembali
                </a>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan
                </button>
            </div>
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