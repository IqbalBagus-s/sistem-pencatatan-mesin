@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Dehum Bahan')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Dehum Bahan</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('dehum-bahan.approve', $dehumBahanRecord->id) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    @php
                        // Extract all unique checker names
                        $checkers = collect([
                            $dehumBahanRecord->created_by_1, 
                            $dehumBahanRecord->created_by_2, 
                            $dehumBahanRecord->created_by_3, 
                            $dehumBahanRecord->created_by_4
                        ])->filter()->unique()->values()->implode(', ') ?? 'Belum ada checker';
                    @endphp
                    {{ $checkers }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- No Dehum Bahan Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Dehum Bahan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        {{ $dehumBahanRecord->nomer_dehum_bahan }}
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($dehumBahanRecord->bulan)->translatedFormat('F Y') }}
                    </div>
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
                    6 => 'Dew Point'
                ];

                // Opsi check dengan ikon (seperti di hopper)
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
                                            // Periksa apakah ada checker untuk minggu ini
                                            $hasChecker = !empty($dehumBahanRecord->{'created_by_'.$j});
                                            
                                            if (!$hasChecker) {
                                                // Jika tidak ada checker, tampilkan tanda "-" dan "Belum ada data"
                                                $resultValue = '-';
                                                $keteranganValue = '';
                                            } else {
                                                // Jika ada checker, tampilkan data yang ada
                                                $resultValue = isset($dehumBahanRecord->{'check_'.$j}[$i]) ? $dehumBahanRecord->{'check_'.$j}[$i] : '-';
                                                $keteranganValue = isset($dehumBahanRecord->{'keterangan_'.$j}[$i]) ? $dehumBahanRecord->{'keterangan_'.$j}[$i] : '';
                                            }
                                        @endphp
                                    
                                        <!-- Minggu {{ $j }} Check -->
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @if ($hasChecker)
                                                {!! isset($options[$resultValue]) ? $options[$resultValue] : $resultValue !!}
                                            @else
                                                <span class="text-gray-600">—</span>
                                            @endif
                                        </td>
                                        
                                        <!-- Minggu {{ $j }} Keterangan -->
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            @if ($hasChecker)
                                                {{ $keteranganValue }}
                                            @else
                                                <span class="text-gray-600 italic text-xs">Belum ada data</span>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                                @if($i == 2)
                                    <tr>
                                        <td colspan="10" class="border border-gray-300 text-center p-2 h-12 bg-gray-100 font-medium">
                                            Panel Kelistrikan
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 4; $j++)
                                    @php
                                        $checkedBy = $dehumBahanRecord->{'created_by_'.$j} ?? '';
                                        $checkedDate = $dehumBahanRecord->{'created_date_'.$j} ? 
                                            \Carbon\Carbon::parse($dehumBahanRecord->{'created_date_'.$j})->locale('id')->format('d').' '.
                                            \Carbon\Carbon::parse($dehumBahanRecord->{'created_date_'.$j})->locale('id')->isoFormat('MMMM').' '.
                                            \Carbon\Carbon::parse($dehumBahanRecord->{'created_date_'.$j})->format('Y') : '-';
                                    @endphp
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm h-10">
                                        <div class="w-full h-full flex flex-col items-center justify-center">
                                            <div>{{ $checkedBy ?: '-' }}</div>
                                            @if($checkedBy)
                                                <div class="text-xs text-gray-600">{{ $checkedDate }}</div>
                                            @endif
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
                                            $approvedBy = $dehumBahanRecord->{'approved_by_minggu'.$j} ?? '';
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
                            <span><span class="font-medium">Dew Point:</span> Baik</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Button Controls -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('dehum-bahan.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </span>
                </a>
                
                @php
                    // Check if all 4 weeks have approved_by filled
                    $allApproved = true;
                    for ($j = 1; $j <= 4; $j++) {
                        if (empty($dehumBahanRecord->{'approved_by_minggu'.$j})) {
                            $allApproved = false;
                            break;
                        }
                    }
                @endphp
                
                @if ($allApproved)
                    <!-- All weeks are approved, show "Sudah Disetujui" button and "Download PDF" button -->
                    <div class="flex space-x-3">
                        <button type="button" disabled class="bg-green-600 text-white py-2 px-4 rounded opacity-75 cursor-not-allowed">
                            Sudah Disetujui
                        </button>
                        <!-- Tombol untuk review PDF -->
                        <a href="{{ route('dehum-bahan.pdf', $dehumBahanRecord->id) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>
                        <!-- Tombol untuk download PDF -->
                        <a href="{{ route('dehum-bahan.downloadPdf', $dehumBahanRecord->id) }}" class="download-pdf-btn bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    </div>
                @else
                    <!-- Not all weeks are approved, show "Setujui" button -->
                    <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded hover:bg-blue-800">
                        Setujui
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection