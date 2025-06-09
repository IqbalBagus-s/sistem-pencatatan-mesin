<!-- resources/views/slitting/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Slitting')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Slitting</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('slitting.approve', $check->hashid) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    @php
                        // Extract all unique checker names menggunakan relasi yang benar
                        $checkers_names = [];
                        for ($i = 1; $i <= 4; $i++) {
                            $checkerRelation = 'checkerMinggu' . $i;
                            if ($check->$checkerRelation && $check->$checkerRelation->username) {
                                $checkers_names[] = $check->$checkerRelation->username;
                            }
                        }
                        $checkersText = !empty($checkers_names) ? implode(', ', array_unique($checkers_names)) : 'Belum ada checker';
                    @endphp
                    {{ $checkersText }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- No Slitting Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Slitting:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Slitting nomor {{ $check->nomer_slitting }}
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($check->bulan)->translatedFormat('F Y') }}
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
                    'V' => '<span class="text-green-600 font-bold">V</span>',
                    'X' => '<span class="text-red-600 font-bold">X</span>',
                    '-' => '<span class="text-gray-600">—</span>',
                    'OFF' => '<span class="text-gray-600">OFF</span>'
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
                                            // Periksa apakah ada checker untuk minggu ini menggunakan data yang sudah disiapkan di controller
                                            $hasChecker = $checkers[$j]['has_data'] ?? false;
                                            
                                            if (!$hasChecker) {
                                                // Jika tidak ada checker, tampilkan tanda "-" dan "Belum ada data"
                                                $resultValue = '-';
                                                $keteranganValue = '';
                                            } else {
                                                // Jika ada checker, tampilkan data yang ada
                                                $resultValue = isset($formattedResults[$i]['minggu'.$j]) ? $formattedResults[$i]['minggu'.$j] : '-';
                                                $keteranganValue = isset($formattedResults[$i]['keterangan_minggu'.$j]) ? $formattedResults[$i]['keterangan_minggu'.$j] : '';
                                            }
                                        @endphp
                                    
                                        <!-- Minggu {{ $j }} Check -->
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @if ($hasChecker)
                                                {!! isset($options[$resultValue]) ? $options[$resultValue] : '—' !!}
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
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 4; $j++)
                                    @php
                                        // Gunakan data checker yang sudah disiapkan di controller
                                        $checkedBy = $checkers[$j]['name'] ?? '';
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
                                            // Gunakan data approver yang sudah disiapkan di controller
                                            $approvedBy = $approvers[$j]['name'] ?? '';
                                            $hasApprover = $approvers[$j]['has_data'] ?? false;
                                        @endphp
                                        
                                        @if($hasApprover)
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
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.approver{{ $j }}.value = userName;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full max-w-xs px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div x-show="selected" class="w-full py-1">
                                                    <div class="flex flex-col items-center">
                                                        <input type="text" name="approved_by_minggu{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userName"
                                                            class="w-full max-w-xs px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
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
<div class="mt-8 bg-white rounded-lg p-2 sm:p-4">
    <div class="flex flex-row flex-wrap items-center justify-between gap-2">
        <!-- Back Button - Left Side -->
        <div class="flex-shrink-0">
            <a href="{{ route('slitting.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
        
        <!-- Action Buttons - Right Side -->
        <div class="flex flex-row flex-wrap gap-2 justify-end">
            @php
                // Check if approver_id1-4 have filled (sesuai model slittingCheck)
                $allApproved = true;
                for ($j = 1; $j <= 4; $j++) {
                    if (empty($check->{'approver_minggu'.$j.'_id'})) {
                        $allApproved = false;
                        break;
                    }
                }
            @endphp
            
            @if (!$allApproved)
                <!-- Not all checks are approved, show "Setujui" button -->
                <button type="submit" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Setujui
                </button>
            @endif
            
            @if ($allApproved)
                <!-- All checks are approved, show PDF Preview and Download buttons -->
                <!-- PDF Preview Button -->
                <a href="{{ route('slitting.pdf', $check->hashid) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview PDF
                </a>
                
                <!-- Download PDF Button -->
                <a href="{{ route('slitting.downloadPdf', $check->hashid) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const downloadPdfBtn = document.querySelector('.download-pdf-btn');
        
        if (downloadPdfBtn) {
            downloadPdfBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the review URL from the href attribute
            const reviewUrl = this.getAttribute('href');
            
            // Extract the ID from the URL
            const urlParts = reviewUrl.split('/');
            const id = urlParts[urlParts.length - 1];
            
            // Construct the download URL properly
            const downloadUrl = `/slitting/download-pdf/${id}`;
            
            // Open the review PDF in a new tab
            window.open(reviewUrl, '_blank');
            
            // Trigger the download directly
            setTimeout(function() {
                // Buat elemen <a> tersembunyi untuk trigger download
                const downloadLink = document.createElement('a');
                    downloadLink.href = downloadUrl;
                    downloadLink.download = `Dokumen_Slitting_${id}.pdf`;
                    downloadLink.style.display = 'none';
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }, 1000);
            });
        }
    });
</script>
@endsection