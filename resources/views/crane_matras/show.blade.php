<!-- resources/views/crane_matras/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Crane Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Crane Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('crane-matras.approve', $check->id) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">{{ $checkerData['checked_by'] }}</span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- No Crane Matras Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Crane Matras:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Crane Matras {{ $checkerData['nomer_crane_matras'] }}
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        {{ date('F Y', strtotime($checkerData['bulan'])) }}
                    </div>
                </div>
            </div>                 
            @php
                // Items yang perlu di-check (sesuai dengan halaman create)
                $items = [
                    1 => 'INVERTER',
                    2 => 'KONTAKTOR',
                    3 => 'THERMAL OVERLOAD',
                    4 => 'PUSH BOTTOM',
                    5 => 'MOTOR',
                    6 => 'BREAKER',
                    7 => 'TRAFO',
                    8 => 'CONECTOR BUSBAR',
                    9 => 'REL BUSBAR',
                    10 => 'GREASE',
                    11 => 'RODA',
                    12 => 'RANTAI',
                ];

                // Opsi check dengan ikon
                $options = [
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];
            @endphp
            
            <!-- Tabel Inspeksi Crane Matras -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm sticky left-0 z-10" style="width: 40px;">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm sticky left-10 z-10" style="width: 180px; max-width: 180px;">Item Terperiksa</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" style="width: 80px;">Check</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 text-sm" style="width: auto; min-width: 220px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formattedResults as $i => $result)
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10" style="width: 40px;">{{ $i + 1 }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10" style="width: 180px; max-width: 180px;">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $result['item'] }}</div>
                                    </td>
                                    
                                    <!-- Check Value with Icon -->
                                    <td class="border border-gray-300 p-1 h-10 text-center">
                                        {!! isset($options[$result['check']]) ? $options[$result['check']] : '—' !!}
                                    </td>
                                    
                                    <!-- Keterangan -->
                                    <td class="border border-gray-300 p-1 h-10 text-sm">
                                        {{ $result['keterangan'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm h-10">
                                    <div class="w-full h-full flex flex-col items-center justify-center">
                                        <span>{{ $checkerData['checked_by'] }}</span>
                                        <span class="text-xs text-gray-600">{{ $checkerData['tanggal'] }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                <td colspan="4" class="border border-gray-300 p-1 bg-green-50">
                                    @if($check->approved_by)
                                        <!-- Jika sudah ada penanggung jawab, tampilkan saja namanya -->
                                        <div class="w-full h-10 flex items-center justify-center text-sm">
                                            {{ $check->approved_by }}
                                        </div>
                                    @else
                                        <!-- Jika belum ada penanggung jawab, tampilkan tombol pilih -->
                                        <div x-data="{ selected: false, userName: '' }">
                                            <div class="mt-1" x-show="selected">
                                                <input type="text" name="approved_by" x-ref="approver" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded mb-1 text-center"
                                                    readonly>
                                                <input type="hidden" name="check_approver" x-ref="checkApprover" value="1">
                                            </div>
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ Auth::user()->username }}'; 
                                                        $refs.approver.value = userName;
                                                        $refs.checkApprover.value = '1';
                                                    } else {
                                                        userName = '';
                                                        $refs.approver.value = '';
                                                        $refs.checkApprover.value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-green-100 hover:bg-green-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Button Controls -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('crane-matras.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Kembali
                </a>
                
                @if ($approvalStatus)
                    <!-- Already approved, show "Sudah Disetujui" button and "Download PDF" button -->
                    <div class="flex space-x-3">
                        <button type="button" disabled class="bg-green-600 text-white py-2 px-4 rounded opacity-75 cursor-not-allowed">
                            Sudah Disetujui
                        </button>
                        <!-- Tombol untuk review PDF -->
                        <a href="{{ route('crane-matras.pdf', $check->id) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>
                        <!-- Tombol untuk download PDF -->
                        <a href="{{ route('crane-matras.downloadPdf', $check->id) }}" class="download-pdf-btn bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    </div>
                @else
                    <!-- Not approved yet, show "Setujui" button -->
                    <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded hover:bg-blue-800">
                        Setujui
                    </button>
                @endif
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
            const downloadUrl = `/crane-matras/download-pdf/${id}`;
            
            // Open the review PDF in a new tab
            window.open(reviewUrl, '_blank');
            
            // Trigger the download directly
            setTimeout(function() {
                // Buat elemen <a> tersembunyi untuk trigger download
                const downloadLink = document.createElement('a');
                    downloadLink.href = downloadUrl;
                    downloadLink.download = `Dokumen_Crane_Matras_${id}.pdf`;
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