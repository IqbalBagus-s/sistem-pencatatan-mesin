@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Hopper')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Hopper</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('hopper.approve', $hopperRecord->hashid) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    {{ $hopperRecord->unique_checkers ?: 'Belum ada checker' }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- No Hopper Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Hopper:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Hopper Nomor {{ $hopperRecord->nomer_hopper }}
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($hopperRecord->bulan)->translatedFormat('F Y') }}
                    </div>
                </div>
            </div>
            
            @php
                // Items yang perlu di-check
                $items = [
                    1 => 'Filter',
                    2 => 'Selang', 
                    3 => 'Kontraktor',
                    4 => 'Temperatur Kontrol',
                    5 => 'MCB'
                ];

                // Opsi check dengan ikon (seperti di mesin giling)
                $options = [
                    'V' => '<span class="text-green-600 font-bold">V</span>',
                    'X' => '<span class="text-red-600 font-bold">X</span>',
                    '-' => '<span class="text-gray-600">—</span>',
                    'OFF' => '<span class="text-gray-600">OFF</span>'
                ];
            @endphp
            
            <!-- Tabel Inspeksi Mingguan -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <div class="overflow-x-auto border border-gray-300 rounded-lg">
                        <div class="md:hidden text-sm text-gray-500 italic mb-2">
                        ← Geser ke kanan untuk melihat semua kolom →
                    </div>
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
                                            $checkerId = $hopperRecord->{'checker_id_minggu'.$j} ?? null;
                                            $checkValue = $hopperRecord->{'check_'.$j}[$i] ?? '';
                                            $keteranganValue = $hopperRecord->{'keterangan_'.$j}[$i] ?? '';
                                            $checkedDate = $hopperRecord->{'tanggal_minggu'.$j} ? 
                                                \Carbon\Carbon::parse($hopperRecord->{'tanggal_minggu'.$j})->locale('id')->format('d').' '.
                                                \Carbon\Carbon::parse($hopperRecord->{'tanggal_minggu'.$j})->locale('id')->isoFormat('MMMM').' '.
                                                \Carbon\Carbon::parse($hopperRecord->{'tanggal_minggu'.$j})->format('Y') : '-';
                                        @endphp

                                        <!-- Minggu {{ $j }} Check -->
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @if (!$checkerId)
                                                <span class="text-gray-600">-</span>
                                            @else
                                                {!! isset($options[$checkValue]) ? $options[$checkValue] : $checkValue !!}
                                            @endif
                                        </td>
                                        
                                        <!-- Minggu {{ $j }} Keterangan -->
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            @if (!$checkerId)
                                                <span class="text-gray-600 italic text-xs">Belum ada data</span>
                                            @else
                                                {{ $keteranganValue }}
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                                @if($i == 2)
                                    <tr>
                                        <td colspan="10" class="border border-gray-300 text-center p-2 h-12 bg-white font-medium">
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
                                        $checkerUsername = $hopperRecord->{'checker_username_minggu'.$j} ?? '';
                                        $checkedDate = $hopperRecord->{'tanggal_minggu'.$j} ? 
                                            \Carbon\Carbon::parse($hopperRecord->{'tanggal_minggu'.$j})->locale('id')->format('d').' '.
                                            \Carbon\Carbon::parse($hopperRecord->{'tanggal_minggu'.$j})->locale('id')->isoFormat('MMMM').' '.
                                            \Carbon\Carbon::parse($hopperRecord->{'tanggal_minggu'.$j})->format('Y') : '-';
                                    @endphp
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm h-10">
                                        <div class="w-full h-full flex flex-col items-center justify-center">
                                            <div>{{ $checkerUsername ?: '-' }}</div>
                                            @if($checkerUsername)
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
                                            $approverUsername = $hopperRecord->{'approver_username_minggu'.$j} ?? '';
                                        @endphp
                                        
                                        @if($approverUsername)
                                            <div class="w-full h-10 flex items-center justify-center text-sm">
                                                {{ $approverUsername }}
                                            </div>
                                        @else
                                            <!-- Form untuk memilih approver tetap sama seperti sebelumnya -->
                                            <div x-data="{ selected: false, userName: '' }" class="w-full">
                                                <div x-show="!selected" class="w-full flex justify-center py-1">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.approver{{ $j }}.value = userName;
                                                            $refs.approverId{{ $j }}.value = '{{ $user->id }}';"
                                                        class="w-full max-w-xs px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div x-show="selected" class="w-full py-1">
                                                    <div class="flex flex-col items-center">
                                                        <input type="text" name="approver_username_minggu{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userName"
                                                            class="w-full max-w-xs px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                            readonly>
                                                        <input type="hidden" name="approver_id_minggu{{ $j }}" x-ref="approverId{{ $j }}" value="">
                                                        <button type="button" 
                                                            @click="selected = false; 
                                                                userName = ''; 
                                                                $refs.approver{{ $j }}.value = '';
                                                                $refs.approverId{{ $j }}.value = '';"
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
            
            <!-- Button Controls -->
            <div class="mt-8 bg-white rounded-lg p-2 sm:p-4">
                <div class="flex flex-row flex-wrap items-center justify-between gap-2">
                    <!-- Back Button - Left Side -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('hopper.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                    
                    <!-- Action Buttons - Right Side -->
                    <div class="flex flex-row flex-wrap gap-2 justify-end">
                        @php
                            // Check if all 4 weeks have approver_id_minggu filled
                            $allApproved = true;
                            for (
                                $j = 1; $j <= 4; $j++) {
                                if (empty($hopperRecord->{'approver_id_minggu'.$j})) {
                                    $allApproved = false;
                                    break;
                                }
                            }
                        @endphp
                        
                        @if (!$allApproved)
                            <!-- Not all weeks are approved, show "Setujui" button -->
                            <button type="submit" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Setujui
                            </button>
                        @endif
                        
                        @if ($allApproved)
                            <!-- All weeks are approved, show PDF Preview and Download buttons -->
                            <!-- PDF Preview Button -->
                            <a href="{{ route('hopper.pdf', $hopperRecord->hashid) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Preview PDF
                            </a>
                            
                            <!-- Download PDF Button -->
                            <a href="{{ route('hopper.downloadPdf', $hopperRecord->hashid) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
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