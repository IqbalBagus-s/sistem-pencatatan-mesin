<!-- resources/views/crane_matras/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Crane Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Crane Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('crane-matras.approve', $check->id) }}" id="approveForm">
            @csrf
            @php
                // Periksa apakah ada checker
                $hasChecker = !empty($checkerData['checker_name']);
            @endphp

            @if($hasChecker)
                <!-- Menampilkan Nama Checker hanya jika ada data -->
                <div class="bg-sky-50 p-4 rounded-md mb-5">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">{{ $checkerData['checker_name'] }}</span>
                </div>

                <!-- Info Display -->
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <!-- No Crane Matras Display -->
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-700">No Crane Matras:</label>
                        <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                            Crane Matras nomor {{ $checkerData['nomer_crane_matras'] }}
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                        <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                            {{ \Carbon\Carbon::parse($checkerData['bulan'])->translatedFormat('F Y') }}
                        </div>
                    </div>
                </div>
            @else
                <!-- Tampilan jika belum ada checker -->
                <div class="bg-yellow-50 p-4 rounded-md mb-5 border border-yellow-300">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <span class="text-yellow-800 font-medium">Belum ada data pemeriksaan untuk crane matras ini</span>
                    </div>
                </div>

                <!-- Info Display untuk data dasar -->
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div class="w-full">
                        <label class="block mb-2 text-sm font-medium text-gray-700">No Crane Matras:</label>
                        <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center text-gray-500">
                            Crane Matras nomor {{ $checkerData['nomer_crane_matras'] ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                        <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center text-gray-500">
                            {{ isset($checkerData['bulan']) ? \Carbon\Carbon::parse($checkerData['bulan'])->translatedFormat('F Y') : '-' }}
                        </div>
                    </div>
                </div>
            @endif             
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
                    'V' => '<span class="text-green-600 font-bold">V</span>',
                    'X' => '<span class="text-red-600 font-bold">X</span>',
                    '-' => '<span class="text-gray-600">—</span>',
                    'OFF' => '<span class="text-gray-600">OFF</span>'
                ];
            @endphp
            
            <!-- Tabel Inspeksi Crane Matras -->
            <div class="mb-6">
                <!-- Notifikasi scroll horizontal untuk mobile -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
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
                            @if($hasChecker)
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
                            @else
                                @foreach($items as $i => $item)
                                    <tr>
                                        <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10" style="width: 40px;">{{ $i }}</td>
                                        <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10" style="width: 180px; max-width: 180px;">
                                            <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                        </td>
                                        
                                        <!-- Check Value - kosong jika belum ada checker -->
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            <span class="text-gray-600">—</span>
                                        </td>
                                        
                                        <!-- Keterangan - kosong jika belum ada checker -->
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            <span class="text-gray-500 italic text-xs">Belum ada data</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm h-10">
                                    <div class="w-full h-full flex flex-col items-center justify-center">
                                        @if($hasChecker)
                                            <span>{{ $checkerData['checker_name'] }}</span>
                                            <span class="text-xs text-gray-600">{{ $checkerData['tanggal'] }}</span>
                                        @else
                                            <span class="text-gray-500 italic">-</span>
                                        @endif
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
                                    @if($checkerData['approver_name'])
                                        <!-- Jika sudah ada penanggung jawab, tampilkan saja namanya -->
                                        <div class="w-full h-10 flex items-center justify-center text-sm">
                                            {{ $checkerData['approver_name'] }}
                                        </div>
                                    @elseif($hasChecker)
                                        <!-- Jika ada checker tapi belum ada penanggung jawab, tampilkan tombol pilih -->
                                        <div x-data="{ selected: false, userName: '' }">
                                            <div class="mt-1" x-show="selected">
                                                <input type="text" name="approver_id" x-ref="approver" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded mb-1 text-center"
                                                    readonly>
                                                <input type="hidden" name="check_approver" x-ref="checkApprover" value="1">
                                            </div>
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ $user->username }}'; 
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
                                    @else
                                        <!-- Jika belum ada checker dan belum ada penanggung jawab -->
                                        <div class="w-full h-10 flex items-center justify-center text-sm text-gray-500 italic">
                                            Belum dapat memilih penanggung jawab
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Catatan Pemeriksaan -->
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-5 rounded-lg shadow-sm mb-6 border-l-4 border-blue-400">
                <h5 class="text-lg font-semibold text-blue-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Pemeriksaan Crane Matras
                </h5>
                
                <div class="p-3 bg-blue-50 rounded-lg col-span-1 md:col-span-2 lg:col-span-3 mb-4">
                    <p class="font-semibold text-blue-800 mb-1">Keterangan Status:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-700">
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-green-100 text-green-700 text-center font-bold mr-2 rounded">V</span>
                            <span>Baik/Normal</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-red-100 text-red-700 text-center font-bold mr-2 rounded">X</span>
                            <span>Tidak Baik/Abnormal</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-white text-gray-700 text-center font-bold mr-2 rounded">-</span>
                            <span>Tidak Diisi</span>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-block w-5 h-5 bg-white text-gray-700 text-center font-bold mr-2 rounded">OFF</span>
                            <span>Mesin Mati</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-blue-100">
                    <h6 class="font-medium text-blue-600 mb-2">Standar Kriteria Pemeriksaan Crane Matras:</h6>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">INVERTER:</span> Berfungsi normal</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">KONTAKTOR:</span> Koneksi baik</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">THERMAL OVERLOAD:</span> Tidak trip</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">PUSH BOTTOM:</span> Responsif</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">MOTOR:</span> Beroperasi normal</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">BREAKER:</span> Tidak trip</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">TRAFO:</span> Tegangan stabil</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">CONECTOR BUSBAR:</span> Koneksi kuat</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">REL BUSBAR:</span> Tidak longgar</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">GREASE:</span> Cukup dan bersih</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">RODA:</span> Tidak aus/retak</span>
                        </li>
                        <li class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><span class="font-medium">RANTAI:</span> Tidak kendor/aus</span>
                        </li>
                    </ul>
                </div>

                <!-- Tambahan Petunjuk Keselamatan -->
                <div class="bg-red-50 p-4 rounded-lg border border-red-400 mt-4">
                    <h6 class="font-medium text-red-600 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Petunjuk Keselamatan:
                    </h6>
                    <ul class="space-y-1 text-sm text-red-700">
                        <li>• Pastikan area kerja aman sebelum mengoperasikan crane</li>
                        <li>• Jangan melebihi beban maksimum yang diizinkan</li>
                        <li>• Lakukan pemeriksaan visual sebelum setiap penggunaan</li>
                        <li>• Segera hentikan operasi jika ditemukan ketidaknormalan</li>
                        <li>• Laporkan setiap kerusakan atau masalah kepada supervisor</li>
                    </ul>
                </div>

                <!-- Informasi Tambahan -->
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 mt-4">
                    <h6 class="font-medium text-yellow-600 mb-2 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Penting:
                    </h6>
                    <ul class="space-y-1 text-sm text-yellow-700">
                        <li>• Pemeriksaan dilakukan secara berkala sesuai jadwal maintenance</li>
                        <li>• Dokumentasi pemeriksaan harus lengkap dan akurat</li>
                        <li>• Setiap temuan abnormal harus dicatat dengan detail di kolom keterangan</li>
                        <li>• Pemeriksaan harus dilakukan oleh personel yang kompeten</li>
                    </ul>
                </div>
            </div>
            
            <!-- Button Controls -->
            <div class="flex flex-row flex-wrap items-center justify-between gap-2 mt-6">
                <!-- Tombol Kembali - Sisi Kiri -->
                <div class="flex-shrink-0">
                    <a href="{{ route('crane-matras.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
                
                <!-- Tombol Aksi - Sisi Kanan -->
                <div class="flex flex-row flex-wrap gap-2 justify-end">
                    @if (!$check->isApproved())
                        <!-- Belum disetujui, tampilkan tombol "Setujui" -->
                        <button type="submit" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui
                        </button>
                    @endif
                    
                    @if ($check->isApproved())
                        <!-- Sudah disetujui, tampilkan tombol Preview dan Download PDF -->
                        <!-- Tombol Preview PDF -->
                        <a href="{{ route('crane-matras.pdf', $check->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>
                        
                        <!-- Tombol Download PDF -->
                        <a href="{{ route('crane-matras.downloadPdf', $check->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection