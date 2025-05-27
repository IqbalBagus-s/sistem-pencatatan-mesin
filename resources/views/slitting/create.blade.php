<!-- resources/views/slitting/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Slitting')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Slitting</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('slitting.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Dropdown Pilih No Slitting - With Required Validation -->
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
                        Pilih No Slitting:
                    </label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" 
                        @click="open = !open" 
                        class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Slitting ' + selected : 'Pilih Slitting'"></span>
                        
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
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="i in 3" :key="i">
                                <div @click.stop>
                                    <button type="button" @click="selected = i; open = false;" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'Slitting ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server (required) -->
                    <input type="hidden" name="nomer_slitting" x-model="selected">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                        Pilih Bulan:
                    </label>
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                </div>
            </div>                  
            @php
                // Items yang perlu di-check
                $items = [
                    1 => 'Conveyor',
                    2 => 'Motor Conveyor',
                    3 => 'Kelistrikan',
                    4 => 'Kontaktor',
                    5 => 'Inverter',
                    6 => 'Vibrator',
                    7 => 'Motor Vibrator',
                    8 => 'Motor Blower',
                    9 => 'Selang angin',
                    10 => 'Flow Control',
                    11 => 'Sensor',
                    12 => 'Limit Switch',
                    13 => 'Pisau Cutting',
                    14 => 'Motor Cutting',
                    15 => 'Elemen ',
                    16 => 'Regulator',
                    17 => 'Air Filter',
                ];

                // Opsi check
                $options = [
                    'V' => 'V',
                    'X' => 'X',
                    '-' => '-',
                    'OFF' => 'OFF'
                ];
            @endphp
            
            <!-- Input untuk menyimpan semua checked items -->
            @foreach($items as $i => $item)
                <input type="hidden" name="checked_items[{{ $i }}]" value="{{ $item }}">
            @endforeach

            <!-- Tabel Inspeksi Mingguan -->
            <div class="mb-6">
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6 border border-gray-300">
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
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                    </td>
                                    
                                    <!-- Minggu 1 -->
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check_1[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
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
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
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
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
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
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
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
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                <!-- Minggu 1 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <div x-data="{ selected: false, userName: '' }">
                                        <div class="mt-1 mb-1" x-show="selected">
                                            <input type="text" name="checked_by_1" x-ref="user1" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_1" x-ref="checkNum1" value="1">
                                        </div>
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
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- Minggu 2 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <div x-data="{ selected: false, userName: '' }">
                                        <div class="mt-1 mb-1" x-show="selected">
                                            <input type="text" name="checked_by_2" x-ref="user2" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_2" x-ref="checkNum2" value="2">
                                        </div>
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
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- Minggu 3 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <div x-data="{ selected: false, userName: '' }">
                                        <div class="mt-1 mb-1" x-show="selected">
                                            <input type="text" name="checked_by_3" x-ref="user3" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_3" x-ref="checkNum3" value="3">
                                        </div>
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
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                                
                                <!-- Minggu 4 -->
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <div x-data="{ selected: false, userName: '' }">
                                        <div class="mt-1 mb-1" x-show="selected">
                                            <input type="text" name="checked_by_4" x-ref="user4" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_4" x-ref="checkNum4" value="4">
                                        </div>
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
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
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
            @include('components.create-form-buttons', ['backRoute' => route('slitting.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>

</script>
@endsection