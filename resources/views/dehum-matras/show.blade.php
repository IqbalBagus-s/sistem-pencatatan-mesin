<!-- resources/views/dehum-matras/show.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Detail Pencatatan Mesin Dehum Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Dehum Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('dehum-matras.approve', $check->id) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    @php
                        // Extract all unique checker names from the results collection
                        $checkers = $results->pluck('checked_by')->filter()->unique()->implode(', ');
                    @endphp
                    {{ $checkers ?: 'Belum ada checker' }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- No Dehum Matras Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Dehum Matras:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Dehum Matras {{ $check->nomer_dehum_matras }}
                    </div>
                </div>
                
                <!-- Shift Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Shift:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Shift {{ $check->shift }}
                    </div>
                </div>

                <!-- Bulan Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($check->bulan)->translatedFormat('F Y') }}
                    </div>
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
                
                // Helper function untuk mendapatkan hasil check berdasarkan tanggal dan item
                function getCheckResult($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal dan item_id
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    
                    // Cek apakah ada checker untuk tanggal tersebut
                    $checkerExists = $results->where('tanggal', $date)->where('checked_by', '!=', null)->where('checked_by', '!=', '')->first();
                    
                    // Jika tidak ada checker, return '-'
                    if (!$checkerExists) {
                        return '-';
                    }
                    
                    // Jika ada checker dan ada result untuk item ini, return hasilnya
                    return $result && isset($result['result']) ? $result['result'] : '-';
                }

                // Helper function untuk mendapatkan keterangan berdasarkan tanggal dan item
                function getKeterangan($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal dan item_id
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    
                    // Cek apakah ada checker untuk tanggal tersebut
                    $checkerExists = $results->where('tanggal', $date)->where('checked_by', '!=', null)->where('checked_by', '!=', '')->first();
                    
                    // Jika tidak ada checker, return kosong
                    if (!$checkerExists) {
                        return '';
                    }
                    
                    return $result && isset($result['keterangan']) ? $result['keterangan'] : '';
                }

                // Helper function untuk mendapatkan nama checker berdasarkan tanggal
                function getCheckerName($results, $date) {
                    // Filter hasil berdasarkan tanggal
                    $result = $results->where('tanggal', $date)->where('checked_by', '!=', null)->where('checked_by', '!=', '')->first();
                    return $result && isset($result['checked_by']) ? $result['checked_by'] : 'Belum dibuat';
                }
                
                // Helper function untuk mendapatkan nama penanggung jawab berdasarkan tanggal
                function getApprovedBy($results, $date) {
                    // Filter hasil berdasarkan tanggal
                    $result = $results->where('tanggal', $date)->first();
                    return $result && isset($result['approved_by']) ? $result['approved_by'] : '';
                }
            @endphp
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <!-- Tabel untuk tanggal 1-11 -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 1; $i <= 11; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 1; $i <= 11; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-20">Hasil</th>
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
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            {{ getCheckResult($results, $j, $i) }}
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
                                    <td class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        {{ getCheckerName($results, $j) }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 1--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
                                        @php
                                            $approvedBy = getApprovedBy($results, $j);
                                        @endphp
                                        
                                        @if($approvedBy)
                                            <!-- Jika sudah ada penanggung jawab, tampilkan saja namanya -->
                                            <div class="w-full px-2 py-1 text-sm">
                                                <input type="text" name="approved_by_{{ $j }}" value="{{ $approvedBy }}"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="approve_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                        @else
                                            <!-- Jika belum ada penanggung jawab, tampilkan tombol pilih -->
                                            <div x-data="{ selected: false, userName: '' }">
                                                <div x-show="!selected">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.approver{{ $j }}.value = userName;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div class="mt-1" x-show="selected">
                                                    <input type="text" name="approved_by_{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userName"
                                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                        readonly>
                                                    <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="{{ $j }}">
                                                    <button type="button" 
                                                        @click="selected = false; 
                                                            userName = ''; 
                                                            $refs.approver{{ $j }}.value = '';
                                                            $refs.approveNum{{ $j }}.value = '';"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                        Batal Pilih
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Tabel untuk tanggal 12-22 -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 12; $i <= 22; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 12; $i <= 22; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-20">Hasil</th>
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
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            {{ getCheckResult($results, $j, $i) }}
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
                                    <td class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        {{ getCheckerName($results, $j) }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 2--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 12; $j <= 22; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
                                        @php
                                            $approvedBy = getApprovedBy($results, $j);
                                        @endphp
                                        
                                        @if($approvedBy)
                                            <!-- Jika sudah ada penanggung jawab, tampilkan saja namanya -->
                                            <div class="w-full px-2 py-1 text-sm">
                                                <input type="text" name="approved_by_{{ $j }}" value="{{ $approvedBy }}"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="approve_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                        @else
                                            <!-- Jika belum ada penanggung jawab, tampilkan tombol pilih -->
                                            <div x-data="{ selected: false, userName: '' }">
                                                <div x-show="!selected">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.approver{{ $j }}.value = userName;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div class="mt-1" x-show="selected">
                                                    <input type="text" name="approved_by_{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userName"
                                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                        readonly>
                                                    <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="{{ $j }}">
                                                    <button type="button" 
                                                        @click="selected = false; 
                                                            userName = ''; 
                                                            $refs.approver{{ $j }}.value = '';
                                                            $refs.approveNum{{ $j }}.value = '';"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                        Batal Pilih
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Tabel untuk tanggal 23-31 -->
                <div class="overflow-x-auto mb-6">
                    <table class="border-collapse table-fixed" style="min-width: max-content;">
                        <thead>
                            <tr>
                                <!-- Kolom No. -->
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>

                                <!-- Kolom Tanggal -->
                                <th class="border border-gray-300 bg-sky-50 p-2 w-40 sticky left-10 z-10" colspan="1">Tanggal</th>

                                <!-- Kolom tanggal 23-31 -->
                                @for ($i = 23; $i <= 31; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                @endfor
                            </tr>
                            <tr>
                                <!-- Kolom Item Terperiksa -->
                                <th class="border border-gray-300 bg-sky-50 p-2 w-40 sticky left-10 z-10">Item Terperiksa</th>

                                <!-- Kolom hasil tiap hari -->
                                @for ($i = 23; $i <= 31; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24">Hasil</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                <tr>
                                    <!-- No. -->
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10 w-10">{{ $i }}</td>

                                    <!-- Item -->
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10 w-40">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                    </td>

                                    <!-- Hasil per tanggal -->
                                    @for($j = 23; $j <= 31; $j++)
                                        <td class="border border-gray-300 p-1 h-10 text-center w-24">
                                            {{ getCheckResult($results, $j, $i) }}
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>

                        <!-- Dibuat Oleh -->
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10 w-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10 w-40">Dibuat Oleh</td>

                                @for($j = 23; $j <= 31; $j++)
                                    <td class="border border-gray-300 p-1 bg-sky-50 text-center text-sm w-24">
                                        {{ getCheckerName($results, $j) }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>

                        <!-- Penanggung Jawab -->
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10 w-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10 w-40">Penanggung Jawab</td>

                                @for($j = 23; $j <= 31; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50 w-24">
                                        @php
                                            $approvedBy = getApprovedBy($results, $j);
                                        @endphp

                                        @if($approvedBy)
                                            <div class="w-full px-2 py-1 text-sm">
                                                <input type="text" name="approved_by_{{ $j }}" value="{{ $approvedBy }}"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="approve_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                        @else
                                            <div x-data="{ selected: false, userName: '' }">
                                                <div x-show="!selected">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.approver{{ $j }}.value = userName;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div class="mt-1" x-show="selected">
                                                    <input type="text" name="approved_by_{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userName"
                                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                        readonly>
                                                    <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="{{ $j }}">
                                                    <button type="button" 
                                                        @click="selected = false; 
                                                            userName = ''; 
                                                            $refs.approver{{ $j }}.value = '';
                                                            $refs.approveNum{{ $j }}.value = '';"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                        Batal Pilih
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
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

            <!-- Button Controls -->
        <div class="mt-8 bg-white rounded-lg p-2 sm:p-4">
            <div class="flex flex-row flex-wrap items-center justify-between gap-2">
                <!-- Back Button - Left Side -->
                <div class="flex-shrink-0">
                    <a href="{{ route('dehum-matras.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
                
                <!-- Action Buttons - Right Side -->
                <div class="flex flex-row flex-wrap gap-2 justify-end">
                    <!-- Hitung jumlah hari dalam bulan -->
                    @php
                        $year = substr($check->bulan, 0, 4);
                        $month = substr($check->bulan, 5, 2);
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
                        
                        // Hitung jumlah tanggal yang sudah disetujui
                        $approvedDatesCount = $results->where('approved_by', '!=', null)->where('approved_by', '!=', '')->unique('tanggal')->count();
                    @endphp
                    
                    <!-- Conditional rendering based on approval status -->
                    @if($approvedDatesCount < $daysInMonth)
                        <!-- Tombol Setujui untuk yang belum disetujui atau disetujui sebagian -->
                        <button type="submit" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui
                        </button>
                    @else
                        <!-- PDF Preview Button -->
                        <a href="{{ route('dehum-matras.pdf', $check->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>
                        
                        <!-- Download PDF Button -->
                        <a href="{{ route('dehum-matras.downloadPdf', $check->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection