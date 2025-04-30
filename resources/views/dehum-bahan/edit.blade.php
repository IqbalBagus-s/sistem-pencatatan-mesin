@extends('layouts.edit-layout-2')

@section('title', 'Edit Pencatatan Mesin Dehum')
@section('page-title', 'Edit Pencatatan Mesin Dehum')
@section('show-checker')
<div></div>
@endsection

@section('content')
<!-- Form Edit -->
<form action="{{ route('dehum-bahan.update', $dehumCheck->id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PUT')
    <div class="grid md:grid-cols-2 gap-4 mb-4">
        <!-- Dropdown Pilih No Dehum (Read-Only) -->
        <div class="relative w-full">
            <label class="block mb-2 text-sm font-medium text-gray-700">Pilih No Dehum:</label>
            
            <input type="text" 
                value="Dehum {{ $dehumCheck->nomer_dehum_bahan }}" 
                class="w-full h-10 px-3 py-2 bg-gray-200 border border-gray-300 rounded-md text-sm cursor-not-allowed" 
                readonly>
            
            <input type="hidden" name="nomer_dehum_bahan" value="{{ $dehumCheck->nomer_dehum_bahan }}">
        </div>                      
    
        <div>
            <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">Pilih Bulan:</label>
            <input type="text" 
                value="{{ \Carbon\Carbon::parse($dehumCheck->bulan)->format('F Y') }}" 
                class="w-full h-10 px-3 py-2 bg-gray-200 border border-gray-300 rounded-md cursor-not-allowed" 
                readonly>
            <input type="hidden" id="bulan" name="bulan" value="{{ $dehumCheck->bulan }}">
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
                    4 => 'Temperatur Control',
                    5 => 'MCB',
                    6 => 'Dew Point'
                ];
                
                $options = [
                    1 => ['Baik', 'Bersih', 'Kotor', 'OFF', '-'],
                    2 => ['Baik', 'Tidak Bocor', 'Bocor', 'OFF', '-'],
                    3 => ['Baik', 'Buruk', 'OFF', '-'],
                    4 => ['Baik', 'Buruk', 'OFF', '-'],
                    5 => ['Baik', 'Buruk', 'OFF', '-'],
                    6 => ['Baik', 'Buruk', 'OFF', '-']
                ];
            @endphp

            @foreach($items as $i => $item)
                @php
                    $result = $dehumResults->firstWhere('checked_items', $item);
                @endphp
                <tr>
                    <td class="border border-gray-300 text-center p-2 h-12 text-xs">{{ $i }}</td>
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        <input type="text" name="checked_items[{{ $i }}]" 
                            class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded text-center" 
                            value="{{ $item }}" readonly>
                    </td>
                    
                    <!-- Minggu 1 -->
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu1 && $dehumCheck->approved_by_minggu1 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="check_1[{{ $i }}]" value="{{ $result ? $result->minggu1 : '' }}">
                            <select class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed" readonly disabled>
                                <option value="{{ $result ? $result->minggu1 : 'Baik' }}">{{ $result ? $result->minggu1 : 'Baik' }}</option>
                            </select>
                        @else
                            <select name="check_1[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ ($result && $result->minggu1 == $option) ? 'selected' : ($option == 'Baik' && (!$result || !$result->minggu1) ? 'selected' : '') }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu1 && $dehumCheck->approved_by_minggu1 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="keterangan_1[{{ $i }}]" value="{{ $result ? $result->keterangan_minggu1 : '' }}">
                            <input type="text" value="{{ $result ? $result->keterangan_minggu1 : '' }}" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed"
                                readonly disabled>
                        @else
                            <input type="text" name="keterangan_1[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan" 
                                value="{{ $result ? $result->keterangan_minggu1 : '' }}">
                        @endif
                    </td>
                    
                    <!-- Minggu 2 -->
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu2 && $dehumCheck->approved_by_minggu2 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="check_2[{{ $i }}]" value="{{ $result ? $result->minggu2 : '' }}">
                            <select class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed" readonly disabled>
                                <option value="{{ $result ? $result->minggu2 : 'Baik' }}">{{ $result ? $result->minggu2 : 'Baik' }}</option>
                            </select>
                        @else
                            <select name="check_2[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ ($result && $result->minggu2 == $option) ? 'selected' : ($option == 'Baik' && (!$result || !$result->minggu2) ? 'selected' : '') }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu2 && $dehumCheck->approved_by_minggu2 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="keterangan_2[{{ $i }}]" value="{{ $result ? $result->keterangan_minggu2 : '' }}">
                            <input type="text" value="{{ $result ? $result->keterangan_minggu2 : '' }}" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed"
                                readonly disabled>
                        @else
                            <input type="text" name="keterangan_2[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan" 
                                value="{{ $result ? $result->keterangan_minggu2 : '' }}">
                        @endif
                    </td>
                    
                    <!-- Minggu 3 -->
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu3 && $dehumCheck->approved_by_minggu3 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="check_3[{{ $i }}]" value="{{ $result ? $result->minggu3 : '' }}">
                            <select class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed" readonly disabled>
                                <option value="{{ $result ? $result->minggu3 : 'Baik' }}">{{ $result ? $result->minggu3 : 'Baik' }}</option>
                            </select>
                        @else
                            <select name="check_3[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ ($result && $result->minggu3 == $option) ? 'selected' : ($option == 'Baik' && (!$result || !$result->minggu3) ? 'selected' : '') }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu3 && $dehumCheck->approved_by_minggu3 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="keterangan_3[{{ $i }}]" value="{{ $result ? $result->keterangan_minggu3 : '' }}">
                            <input type="text" value="{{ $result ? $result->keterangan_minggu3 : '' }}" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed"
                                readonly disabled>
                        @else
                            <input type="text" name="keterangan_3[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan" 
                                value="{{ $result ? $result->keterangan_minggu3 : '' }}">
                        @endif
                    </td>
                    
                    <!-- Minggu 4 -->
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu4 && $dehumCheck->approved_by_minggu4 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="check_4[{{ $i }}]" value="{{ $result ? $result->minggu4 : '' }}">
                            <select class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed" readonly disabled>
                                <option value="{{ $result ? $result->minggu4 : 'Baik' }}">{{ $result ? $result->minggu4 : 'Baik' }}</option>
                            </select>
                        @else
                            <select name="check_4[{{ $i }}]" class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ ($result && $result->minggu4 == $option) ? 'selected' : ($option == 'Baik' && (!$result || !$result->minggu4) ? 'selected' : '') }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </td>
                    <td class="border border-gray-300 p-2 h-12 text-xs">
                        @if($dehumCheck->approved_by_minggu4 && $dehumCheck->approved_by_minggu4 != '-')
                            <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                            <input type="hidden" name="keterangan_4[{{ $i }}]" value="{{ $result ? $result->keterangan_minggu4 : '' }}">
                            <input type="text" value="{{ $result ? $result->keterangan_minggu4 : '' }}" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded cursor-not-allowed"
                                readonly disabled>
                        @else
                            <input type="text" name="keterangan_4[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan" 
                                value="{{ $result ? $result->keterangan_minggu4 : '' }}">
                        @endif
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
        <!-- Tbody untuk Checker dan approver-->
        <tbody class="bg-white">
            <tr class="bg-sky-50">
                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs" rowspan="1">-</td>
                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs">Dibuat Oleh</td>
                
                <!-- Week 1 - Updated handling for approved state -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div x-data="{ selected: '{{ $dehumCheck->checked_by_minggu1 }}' ? true : false, userName: '{{ $dehumCheck->checked_by_minggu1 }}' }" class="h-20">
                        <!-- Form fields selalu terlihat dengan fixed height -->
                        <div class="space-y-1 h-12">
                            @if($dehumCheck->approved_by_minggu1 && $dehumCheck->approved_by_minggu1 != '-')
                                <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                                <input type="hidden" name="created_by_1" value="{{ $dehumCheck->checked_by_minggu1 }}">
                                <input type="hidden" name="created_date_1" value="{{ $dehumCheck->tanggal_minggu1 }}">
                                
                                <input type="text" value="{{ $dehumCheck->checked_by_minggu1 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                                <input type="text" value="{{ $dehumCheck->tanggal_minggu1 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                            @else
                                <input type="text" name="created_by_1" x-ref="user1" 
                                    x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                                <input type="text" name="created_date_1" x-ref="date1" 
                                    x-bind:value="selected ? ('{{ $dehumCheck->tanggal_minggu1 }}' || '{{ date('Y-m-d') }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                            @endif
                        </div>
                        
                        <!-- Tombol di tengah div - hilang jika sudah disetujui -->
                        <div class="flex items-center justify-center mt-1" 
                            x-show="{{ $dehumCheck->approved_by_minggu1 && $dehumCheck->approved_by_minggu1 != '-' ? 'false' : 'true' }}">
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
                
                <!-- Week 2 - Updated handling for approved state -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div x-data="{ selected: '{{ $dehumCheck->checked_by_minggu2 }}' ? true : false, userName: '{{ $dehumCheck->checked_by_minggu2 }}' }" class="h-20">
                        <!-- Form fields selalu terlihat dengan fixed height -->
                        <div class="space-y-1 h-12">
                            @if($dehumCheck->approved_by_minggu2 && $dehumCheck->approved_by_minggu2 != '-')
                                <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                                <input type="hidden" name="created_by_2" value="{{ $dehumCheck->checked_by_minggu2 }}">
                                <input type="hidden" name="created_date_2" value="{{ $dehumCheck->tanggal_minggu2 }}">
                                
                                <input type="text" value="{{ $dehumCheck->checked_by_minggu2 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                                <input type="text" value="{{ $dehumCheck->tanggal_minggu2 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                            @else
                                <input type="text" name="created_by_2" x-ref="user2" 
                                    x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                                <input type="text" name="created_date_2" x-ref="date2" 
                                    x-bind:value="selected ? ('{{ $dehumCheck->tanggal_minggu2 }}' || '{{ date('Y-m-d') }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                            @endif
                        </div>
                        
                        <!-- Tombol di tengah div - hilang jika sudah disetujui -->
                        <div class="flex items-center justify-center mt-1" 
                            x-show="{{ $dehumCheck->approved_by_minggu2 && $dehumCheck->approved_by_minggu2 != '-' ? 'false' : 'true' }}">
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
                
                <!-- Week 3 - Updated handling for approved state -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div x-data="{ selected: '{{ $dehumCheck->checked_by_minggu3 }}' ? true : false, userName: '{{ $dehumCheck->checked_by_minggu3 }}' }" class="h-20">
                        <!-- Form fields selalu terlihat dengan fixed height -->
                        <div class="space-y-1 h-12">
                            @if($dehumCheck->approved_by_minggu3 && $dehumCheck->approved_by_minggu3 != '-')
                                <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                                <input type="hidden" name="created_by_3" value="{{ $dehumCheck->checked_by_minggu3 }}">
                                <input type="hidden" name="created_date_3" value="{{ $dehumCheck->tanggal_minggu3 }}">
                                
                                <input type="text" value="{{ $dehumCheck->checked_by_minggu3 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                                <input type="text" value="{{ $dehumCheck->tanggal_minggu3 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                            @else
                                <input type="text" name="created_by_3" x-ref="user3" 
                                    x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                                <input type="text" name="created_date_3" x-ref="date3" 
                                    x-bind:value="selected ? ('{{ $dehumCheck->tanggal_minggu3 }}' || '{{ date('Y-m-d') }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                            @endif
                        </div>
                        
                        <!-- Tombol di tengah div - hilang jika sudah disetujui -->
                        <div class="flex items-center justify-center mt-1" 
                            x-show="{{ $dehumCheck->approved_by_minggu3 && $dehumCheck->approved_by_minggu3 != '-' ? 'false' : 'true' }}">
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
                
                <!-- Week 4 - Updated handling for approved state -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div x-data="{ selected: '{{ $dehumCheck->checked_by_minggu4 }}' ? true : false, userName: '{{ $dehumCheck->checked_by_minggu4 }}' }" class="h-20">
                        <!-- Form fields selalu terlihat dengan fixed height -->
                        <div class="space-y-1 h-12">
                            @if($dehumCheck->approved_by_minggu4 && $dehumCheck->approved_by_minggu4 != '-')
                                <!-- Tambahkan hidden input untuk menyimpan nilai asli -->
                                <input type="hidden" name="created_by_4" value="{{ $dehumCheck->checked_by_minggu4 }}">
                                <input type="hidden" name="created_date_4" value="{{ $dehumCheck->tanggal_minggu4 }}">
                                
                                <input type="text" value="{{ $dehumCheck->checked_by_minggu4 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                                <input type="text" value="{{ $dehumCheck->tanggal_minggu4 }}" 
                                    class="w-full px-2 py-1 text-xs bg-gray-200 border border-gray-300 rounded"
                                    readonly disabled>
                            @else
                                <input type="text" name="created_by_4" x-ref="user4" 
                                    x-bind:value="selected ? (userName || '{{ Auth::user()->username }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                                <input type="text" name="created_date_4" x-ref="date4" 
                                    x-bind:value="selected ? ('{{ $dehumCheck->tanggal_minggu4 }}' || '{{ date('Y-m-d') }}') : ''"
                                    class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded"
                                    x-bind:style="selected ? 'visibility: visible;' : 'visibility: hidden; height: 0;'"
                                    readonly>
                            @endif
                        </div>
                        <!-- Tombol di tengah div - hilang jika sudah disetujui -->
                        <div class="flex items-center justify-center mt-1" 
                            x-show="{{ $dehumCheck->approved_by_minggu4 && $dehumCheck->approved_by_minggu4 != '-' ? 'false' : 'true' }}">
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
        <!-- Baris untuk approval -->
        <tbody class="bg-white">
            <tr class="bg-sky-50">
                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs" rowspan="1">-</td>
                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs">Disetujui Oleh</td>
                
                <!-- Week 1 Approval - Read Only -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div class="flex items-center justify-center h-10">
                        <input type="text" 
                            value="{{ $dehumCheck->approved_by_minggu1 ?? '-' }}"
                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded text-center"
                            readonly>
                    </div>
                </td>
                
                <!-- Week 2 Approval - Read Only -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div class="flex items-center justify-center h-10">
                        <input type="text" 
                            value="{{ $dehumCheck->approved_by_minggu2 ?? '-' }}"
                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded text-center"
                            readonly>
                    </div>
                </td>
                
                <!-- Week 3 Approval - Read Only -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div class="flex items-center justify-center h-10">
                        <input type="text" 
                            value="{{ $dehumCheck->approved_by_minggu3 ?? '-' }}"
                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded text-center"
                            readonly>
                    </div>
                </td>
                
                <!-- Week 4 Approval - Read Only -->
                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                    <div class="flex items-center justify-center h-10">
                        <input type="text" 
                            value="{{ $dehumCheck->approved_by_minggu4 ?? '-' }}"
                            class="w-full px-2 py-1 text-xs bg-gray-100 border border-gray-300 rounded text-center"
                            readonly>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

    @include('components.edit-form-buttons', ['backRoute' => route('dehum-bahan.index')])
</form>
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