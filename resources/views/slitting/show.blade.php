<!-- resources/views/slitting/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Slitting')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Slitting</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('slitting.approve', $check->id) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    @php
                        // Extract all unique checker names
                        $checkers = [];
                        for ($i = 1; $i <= 4; $i++) {
                            if (!empty($check->{'checked_by_minggu'.$i})) {
                                $checkers[] = $check->{'checked_by_minggu'.$i};
                            }
                        }
                        $checkersText = !empty($checkers) ? implode(', ', array_unique($checkers)) : 'Belum ada checker';
                    @endphp
                    {{ $checkersText }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- No Slitting Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Slitting:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Slitting {{ $check->nomer_slitting }}
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        {{ date('F Y', strtotime($check->bulan)) }}
                    </div>
                </div>
            </div>                 
            @php
                // Items yang perlu di-check (sesuai dengan halaman edit)
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

                // Opsi check dengan ikon
                $options = [
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];
            @endphp
            
            <!-- Tabel Inspeksi Mingguan -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 text-sm sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10" colspan="1">Minggu</th>
                                
                                @for ($i = 1; $i <= 4; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 text-sm" colspan="1">0{{ $i }}</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-32 text-sm" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 1; $i <= 4; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 text-sm">Check</th>
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
                                    
                                    @for($j = 1; $j <= 4; $j++)
                                        @php
                                            // Jika tidak ada penanggung jawab, kosongkan data
                                            if (!$hasApprovedBy[$j]) {
                                                $resultValue = '-';
                                                $keteranganValue = '';
                                            } else {
                                                $resultValue = isset($formattedResults[$i]['minggu'.$j]) ? $formattedResults[$i]['minggu'.$j] : '-';
                                                $keteranganValue = isset($formattedResults[$i]['keterangan_minggu'.$j]) ? $formattedResults[$i]['keterangan_minggu'.$j] : '';
                                            }
                                        @endphp
                                    
                                        <!-- Minggu {{ $j }} Check -->
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @if ($hasApprovedBy[$j])
                                                {!! isset($options[$resultValue]) ? $options[$resultValue] : '—' !!}
                                            @else
                                                <span class="text-gray-600">—</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Minggu {{ $j }} Keterangan -->
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            @if ($hasApprovedBy[$j])
                                                {{ $keteranganValue }}
                                            @else
                                                <span class="text-gray-600 italic text-xs">Belum ada data</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 4; $j++)
                                    @php
                                        $checkedBy = $check->{'checked_by_minggu'.$j} ?? '';
                                    @endphp
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm h-10">
                                        <div class="w-full h-full flex items-center justify-center">
                                            {{ $checkedBy ?: '-' }}
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 1; $j <= 4; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-green-50">
                                        @php
                                            $approvedBy = $check->{'approved_by_minggu'.$j} ?? '';
                                        @endphp
                                        
                                        @if($approvedBy)
                                            <!-- Jika sudah ada penanggung jawab, tampilkan saja namanya -->
                                            <div class="w-full h-10 flex items-center justify-center text-sm">
                                                {{ $approvedBy }}
                                            </div>
                                        @else
                                            <!-- Jika belum ada penanggung jawab, tampilkan tombol pilih -->
                                            <div x-data="{ selected: false, userName: '' }" class="w-full">
                                                <div x-show="!selected" class="w-full flex justify-center py-1">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userName = '{{ Auth::user()->username }}'; 
                                                            $refs.approver{{ $j }}.value = userName;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full max-w-xs px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div x-show="selected" class="w-full py-1">
                                                    <div class="flex flex-col items-center">
                                                        <input type="text" name="approved_by_minggu{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userName"
                                                            class="w-full max-w-xs px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center mb-1"
                                                            readonly>
                                                        <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="">
                                                        <button type="button" 
                                                            @click="selected = false; 
                                                                userName = ''; 
                                                                $refs.approver{{ $j }}.value = '';
                                                                $refs.approveNum{{ $j }}.value = '';"
                                                            class="w-full max-w-xs px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                            Batal Pilih
                                                        </button>
                                                    </div>
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
                        <span>Pengecekan mesin, dilakukan setiap minggu secara berkala.</span>
                    </li>
                </ul>
            </div>
            
            <!-- Button Controls -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('slitting.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Kembali
                </a>
                <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded hover:bg-blue-800">
                    Setujui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection