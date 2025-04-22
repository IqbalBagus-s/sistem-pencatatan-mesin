<!-- resources/views/hopper/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Hopper')

@section('content')


<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Hopper</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('hopper.store') }}" method="POST">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Dropdown Pilih No Hopper -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    }
                }" class="relative w-full">
                    <!-- Label -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">Pilih No Hopper:</label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Hopper ' + selected : 'Pilih Hopper'"></span>
                        
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
                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="i in 15" :key="i">
                                <div @click.stop>
                                    <button type="button" @click="selected = i; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'Hopper ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server -->
                    <input type="hidden" name="nomer_hopper" x-model="selected">
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
                            <th class="border border-gray-300 bg-sky-50 p-2 w-10 text-sm" rowspan="2">No.</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm" colspan="1">Minggu</th>
                            
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
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm">Checked Items</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
                        </tr>
                    </thead>                          
                    <tbody>
                        @php
                            $items = [
                                1 => 'Filter',
                                2 => 'Selang',
                                3 => 'Kontraktor',
                                4 => 'Temperatur Kontrol',
                                5 => 'MCB'
                            ];
                            
                            $options = [
                                1 => ['Bersih', 'Kotor', 'OFF'],
                                2 => ['Tidak Bocor', 'Bocor', 'OFF'],
                                3 => ['Baik', 'Buruk', 'OFF'],
                                4 => ['Baik', 'Buruk', 'OFF'],
                                5 => ['Baik', 'Buruk', 'OFF']
                            ];
                        @endphp
                        
                        @foreach($items as $i => $item)
                            <tr>
                                <td class="border border-gray-300 text-center p-2 h-12 text-xs">{{ $i }}</td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <input type="text" name="checked_items[{{ $i }}]" 
                                        class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded text-center" 
                                        value="{{ $item }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <select name="check_1[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        <option value="">Pilih</option>
                                        @foreach($options[$i] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <input type="text" name="keterangan_1[{{ $i }}]" 
                                        class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                        placeholder="Keterangan">
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <select name="check_2[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        <option value="">Pilih</option>
                                        @foreach($options[$i] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <input type="text" name="keterangan_2[{{ $i }}]" 
                                        class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                        placeholder="Keterangan">
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <select name="check_3[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        <option value="">Pilih</option>
                                        @foreach($options[$i] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <input type="text" name="keterangan_3[{{ $i }}]" 
                                        class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                        placeholder="Keterangan">
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <select name="check_4[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        <option value="">Pilih</option>
                                        @foreach($options[$i] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 h-12 text-xs">
                                    <input type="text" name="keterangan_4[{{ $i }}]" 
                                        class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                        placeholder="Keterangan">
                                </td>
                            </tr>
                            @if($i == 2)
                            <tr>
                                <td colspan="10" class="border border-gray-300 text-center p-2 h-12 bg-gray-100 font-medium text-xs">
                                    Panel Kelistrikan
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tbody class="bg-white">
                        <tr class="bg-sky-50">
                            <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs" rowspan="1">-</td>
                            <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs">Dibuat Oleh</td>
                            
                            <!-- Week 1 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }" class="h-20">
                                    <!-- Form fields dengan fixed height -->
                                    <div class="space-y-1 h-12">
                                        <input type="text" name="created_by_1" x-ref="user1" 
                                            x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                        <input type="text" name="created_date_1" x-ref="date1" 
                                            x-bind:value="selected ? '{{ date('Y-m-d') }}' : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                    </div>
                                    
                                    <!-- Tombol di tengah div -->
                                    <div class="flex items-center justify-center mt-1">
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user1.value = userName; 
                                                    $refs.date1.value = '{{ date('Y-m-d') }}';
                                                } else {
                                                    userName = '';
                                                    $refs.user1.value = '';
                                                    $refs.date1.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Week 2 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }" class="h-20">
                                    <!-- Form fields dengan fixed height -->
                                    <div class="space-y-1 h-12">
                                        <input type="text" name="created_by_2" x-ref="user2" 
                                            x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                        <input type="text" name="created_date_2" x-ref="date2" 
                                            x-bind:value="selected ? '{{ date('Y-m-d') }}' : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                    </div>
                                    
                                    <!-- Tombol di tengah div -->
                                    <div class="flex items-center justify-center mt-1">
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user2.value = userName; 
                                                    $refs.date2.value = '{{ date('Y-m-d') }}';
                                                } else {
                                                    userName = '';
                                                    $refs.user2.value = '';
                                                    $refs.date2.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Week 3 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }" class="h-20">
                                    <!-- Form fields dengan fixed height -->
                                    <div class="space-y-1 h-12">
                                        <input type="text" name="created_by_3" x-ref="user3" 
                                            x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                        <input type="text" name="created_date_3" x-ref="date3" 
                                            x-bind:value="selected ? '{{ date('Y-m-d') }}' : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                    </div>
                                    
                                    <!-- Tombol di tengah div -->
                                    <div class="flex items-center justify-center mt-1">
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user3.value = userName; 
                                                    $refs.date3.value = '{{ date('Y-m-d') }}';
                                                } else {
                                                    userName = '';
                                                    $refs.user3.value = '';
                                                    $refs.date3.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Week 4 -->
                            <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <div x-data="{ selected: false, userName: '' }" class="h-20">
                                    <!-- Form fields dengan fixed height -->
                                    <div class="space-y-1 h-12">
                                        <input type="text" name="created_by_4" x-ref="user4" 
                                            x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                        <input type="text" name="created_date_4" x-ref="date4" 
                                            x-bind:value="selected ? '{{ date('Y-m-d') }}' : ''"
                                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                            x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                            readonly>
                                    </div>
                                    
                                    <!-- Tombol di tengah div -->
                                    <div class="flex items-center justify-center mt-1">
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user4.value = userName; 
                                                    $refs.date4.value = '{{ date('Y-m-d') }}';
                                                } else {
                                                    userName = '';
                                                    $refs.user4.value = '';
                                                    $refs.date4.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @include('components.create-form-buttons', ['backRoute' => route('hopper.index')])
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