@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Dehum Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Dehum Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ $user->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('dehum-matras.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- Dropdown Pilih No Dehum Matras -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    }
                    }" class="relative w-full">
                    <!-- Label -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">Pilih No Dehum Matras:</label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Dehum Matras ' + selected : 'Pilih Dehum Matras'"></span>
                        
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
                            <div @click.stop>
                                <button type="button" @click="selected = 1; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                    <span>1</span>
                                </button>
                            </div>
                            <div @click.stop>
                                <button type="button" @click="selected = 2; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                    <span>2</span>
                                </button>
                            </div>
                            <div @click.stop>
                                <button type="button" @click="selected = 3; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                    <span>3</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server -->
                    <input type="hidden" name="nomer_dehum_matras" x-model="selected">
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
                    // Items yang perlu di-check untuk Dehum Matras
                    $items = [
                        1 => 'Kompressor',
                        2 => 'Kabel',
                        3 => 'NFB',
                        4 => 'Motor',
                        5 => 'Water Cooler in',
                        6 => 'Water Cooler Out',
                        7 => 'Temperatur Output Udara',
                    ];
                
                    // Placeholder text untuk setiap item
                    $placeholders = [
                        1 => '50° C - 70° C',
                        2 => '35° C - 45° C',
                        3 => '35° C - 50° C',
                        4 => '40° C - 55° C',
                        5 => '31° C - 33° C',
                        6 => '32° C - 36° C',
                        7 => '18° C - 28° C',
                    ];
                @endphp
                <!-- Tabel Inspeksi -->
                <div class="mb-6">
                    <!-- Tabel untuk tanggal 1-11 -->
                    <!-- Notifikasi scroll horizontal untuk mobile -->
                    <div class="md:hidden text-sm text-gray-500 italic mb-2">
                        ← Geser ke kanan untuk melihat semua kolom →
                    </div>
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10">No.</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                    
                                    @for ($i = 1; $i <= 11; $i++)
                                        @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <th class="border border-gray-300 bg-sky-50 p-2 w-24">{{ $num }}</th>
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
                                                <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                    placeholder="{{ $placeholders[$i] }}"
                                                    class="w-full h-8 px-2 py-0 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400">
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody class="bg-white">
                                <tr class="bg-sky-50">
                                    <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                    <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                    
                                    @for($j = 1; $j <= 11; $j++)
                                        <td class="border border-gray-300 p-1 bg-sky-50">
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
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10">No.</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                    
                                    @for ($i = 12; $i <= 22; $i++)
                                        @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <th class="border border-gray-300 bg-sky-50 p-2 w-24">{{ $num }}</th>
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
                                                <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                    placeholder="{{ $placeholders[$i] }}"
                                                    class="w-full h-8 px-2 py-0 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400">
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody class="bg-white">
                                <tr class="bg-sky-50">
                                    <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                    <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                    
                                    @for($j = 12; $j <= 22; $j++)
                                        <td class="border border-gray-300 p-1 bg-sky-50">
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
                    <div class="overflow-x-auto mb-6">
                        <table class="border-collapse table-fixed" style="width: max-content;">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 bg-sky-50 p-2 sticky left-0 z-10" style="width: 40px;">No.</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 sticky left-10 z-10" style="width: 160px;">Item Terperiksa</th>
                                    
                                    @for ($i = 23; $i <= 31; $i++)
                                        @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <th class="border border-gray-300 bg-sky-50 p-2" style="width: 90px;">{{ $num }}</th>
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
                                            <td class="border border-gray-300 p-1 h-10" style="width: 90px;">
                                                <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                    placeholder="{{ $placeholders[$i] }}"
                                                    class="w-full h-8 px-2 py-0 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400">
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody class="bg-white">
                                <tr class="bg-sky-50">
                                    <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                    <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                    
                                    @for($j = 23; $j <= 31; $j++)
                                        <td class="border border-gray-300 p-1 bg-sky-50" style="width: 90px;">
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
                <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-5 rounded-lg shadow-sm mb-6 border-l-4 border-blue-400">
                    <h5 class="text-lg font-semibold text-blue-700 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Catatan Pemeriksaan
                    </h5>

                    <div class="bg-white p-6 rounded-lg border border-blue-200 shadow-sm">
                        <h6 class="font-medium text-blue-600 mb-4 text-lg">Standar Kriteria Pemeriksaan:</h6>
                        <ul class="space-y-3 text-gray-700 text-sm leading-relaxed">
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Kompressor:</strong> 50°C - 70°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Kabel:</strong> 35°C - 45°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>NFB:</strong> 35°C - 50°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Motor:</strong> 40°C - 55°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Water Cooler in:</strong> 31°C - 33°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Water Cooler Out:</strong> 32°C - 36°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Temperatur Output Udara:</strong> 18°C - 28°C</span>
                            </li>
                        </ul>
                    </div>
                </div>

            @include('components.create-form-buttons', ['backRoute' => route('dehum-matras.index')])
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