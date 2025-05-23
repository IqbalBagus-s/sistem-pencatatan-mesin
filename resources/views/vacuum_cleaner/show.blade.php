<!-- resources/views/vacuum_cleaner/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Vacuum Cleaner')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Vacuum Cleaner</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('vacuum-cleaner.approve', $check->id) }}" id="approveForm" autocomplete="off"
            x-data="{ 
                minggu2Selected: {{ $check->approver_minggu2 ? 'true' : 'false' }}, 
                minggu4Selected: {{ $check->approver_minggu4 ? 'true' : 'false' }},
                atLeastOneSelected() {
                    return this.minggu2Selected || this.minggu4Selected;
                },
                submit() {
                    if (!this.atLeastOneSelected()) {
                        alert('Silakan pilih setidaknya satu penanggung jawab sebelum menyetujui.');
                        return false;
                    }
                    return true;
                }
            }">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    @php
                        // Extract all unique checker names from the results collection
                        $checkers = collect([$check->checker_minggu1, $check->checker_minggu2])->filter()->unique()->implode(', ');
                    @endphp
                    {{ $checkers ?: 'Belum ada checker' }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Info Vacuum Cleaner yang dipilih -->
                <div class="relative w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        No Vacuum cleaner: 
                    </label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm text-left flex items-center">
                        <span>Vacuum cleaner nomor {{ $check->nomer_vacum_cleaner }}</span>
                    </div>
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                        Bulan:
                    </label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm text-left flex items-center">
                        <span>{{ \Carbon\Carbon::parse($check->bulan)->locale('id')->isoFormat('MMMM YYYY') }}</span>
                    </div>
                </div>
            </div>  
            
            @php
                // Items yang perlu di-check sesuai dengan itemsMap dari controller
                $items = $itemsMap;

                // Opsi check
                $options = [
                    'V' => '<span class="text-green-600 font-bold">V</span>',
                    'X' => '<span class="text-red-600 font-bold">X</span>',
                    '-' => '<span class="text-gray-600">—</span>',
                    'OFF' => '<span class="text-gray-600">OFF</span>'
                ];
                
                // Helper function untuk mendapatkan nama checker berdasarkan minggu
                function getCheckerName($check, $minggu) {
                    if ($minggu == 2) {
                        return $check->checker_minggu2 ?: '';
                    } else if ($minggu == 4) {
                        return $check->checker_minggu4 ?: '';
                    }
                    return '';
                }

                // Helper function untuk mendapatkan tanggal pencatatan berdasarkan minggu
                function getCheckDate($check, $minggu) {
                    if ($minggu == 2) {
                        return $check->tanggal_dibuat_minggu2 ? date('d-m-Y', strtotime($check->tanggal_dibuat_minggu2)) : '';
                    } else if ($minggu == 4) {
                        return $check->tanggal_dibuat_minggu4 ? date('d-m-Y', strtotime($check->tanggal_dibuat_minggu4)) : '';
                    }
                    return '';
                }

                // Helper function untuk mendapatkan nama penanggung jawab berdasarkan minggu
                function getApprovedBy($check, $minggu) {
                    if ($minggu == 2) {
                        return $check->approver_minggu2 ?: '';
                    } else if ($minggu == 4) {
                        return $check->approver_minggu4 ?: '';
                    }
                    return '';
                }
                
                // Helper function untuk mendapatkan hasil berdasarkan minggu dan item_id
                function getCheckResult($groupedResults, $minggu, $itemId, $check) {
                    // Cek apakah checker sudah terisi
                    $checkerName = '';
                    if ($minggu == 2) {
                        $checkerName = $check->checker_minggu2 ?: '';
                    } else if ($minggu == 4) {
                        $checkerName = $check->checker_minggu4 ?: '';
                    }
                    
                    // Jika checker belum terisi, return '-'
                    if (empty($checkerName)) {
                        return '-';
                    }
                    
                    // Jika checker sudah terisi, ambil hasil dari database
                    if (isset($groupedResults[$minggu])) {
                        $result = $groupedResults[$minggu]->where('item_id', $itemId)->first();
                        return $result && isset($result['result']) ? $result['result'] : null;
                    }
                    return null;
                }
                
                // Helper function untuk mendapatkan keterangan berdasarkan minggu dan item_id
                function getKeterangan($groupedResults, $minggu, $itemId, $check) {
                    // Cek apakah checker sudah terisi
                    $checkerName = '';
                    if ($minggu == 2) {
                        $checkerName = $check->checker_minggu2 ?: '';
                    } else if ($minggu == 4) {
                        $checkerName = $check->checker_minggu4 ?: '';
                    }
                    
                    // Jika checker belum terisi, return 'data belum diisi'
                    if (empty($checkerName)) {
                        return 'data belum diisi';
                    }
                    
                    // Jika checker sudah terisi, ambil keterangan dari database
                    if (isset($groupedResults[$minggu])) {
                        $result = $groupedResults[$minggu]->where('item_id', $itemId)->first();
                        return $result && isset($result['keterangan']) ? $result['keterangan'] : '';
                    }
                    return '';
                }
            @endphp
            
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <!-- Tabel untuk minggu kedua -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-12 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10" colspan="1">Minggu</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16" colspan="1">02</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-64" rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10">Item Terperiksa</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16">Cek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 bg-white z-10 w-12">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-12 bg-white z-10 w-32">
                                        <div class="w-full h-8 px-1 py-0 text-sm flex items-center">{{ $item }}</div>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 text-center w-16">
                                        @php
                                            $result = getCheckResult($groupedResults, 2, $i, $check);
                                            echo isset($options[$result]) ? $options[$result] : '<span class="text-gray-400 italic">-</span>';
                                        @endphp
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 text-sm w-64">
                                        @php
                                            $keterangan = getKeterangan($groupedResults, 2, $i, $check);
                                        @endphp
                                        <span class="{{ $keterangan === 'data belum diisi' ? 'text-gray-400 italic' : '' }}">
                                            {{ $keterangan }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-12 z-10">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                    {{ getCheckerName($check, 2) ?: '-' }}
                                    <br>
                                    @php
                                        $checkDate = getCheckDate($check, 2);
                                    @endphp
                                    {{ $checkDate ? \Carbon\Carbon::parse($checkDate)->translatedFormat('d F Y') : '-' }}
                                </td>
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-12 z-10">Penanggung Jawab</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-green-50">
                                    @php
                                        $approvedBy = getApprovedBy($check, 2);
                                    @endphp
                                    
                                    @if($approvedBy)
                                        <!-- Jika sudah ada penanggung jawab, tampilkan namanya -->
                                        <div class="w-full px-2 py-1 text-sm">
                                            <input type="text" name="approved_by_minggu2" value="{{ $approvedBy }}"
                                                class="w-full px-2 py-1 text-sm bg-sky-50 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="approve_minggu2" value="2">
                                        </div>
                                    @else
                                        <!-- Jika belum ada penanggung jawab, tampilkan tombol pilih -->
                                        <div>
                                            <!-- Tombol Pilih -->
                                            <div x-show="minggu2Selected === false">
                                                <button type="button" 
                                                    @click="minggu2Selected = true"
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                    Pilih
                                                </button>
                                            </div>
                                            
                                            <!-- Form fields ketika dipilih -->
                                            <div class="mt-1" x-show="minggu2Selected === true">
                                                <input type="text" name="approved_by_minggu2" value="{{ Auth::user()->username }}"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                    x-bind:disabled="!minggu2Selected"
                                                    readonly>
                                                <input type="hidden" name="approve_minggu2" value="2" x-bind:disabled="!minggu2Selected">
                                                <button type="button" 
                                                    @click="minggu2Selected = false"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                    Batal Pilih
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Tabel untuk minggu keempat -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-12 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10" colspan="1">Minggu</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16" colspan="1">04</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-64" rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10">Item Terperiksa</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16">Cek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 bg-white z-10 w-12">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-12 bg-white z-10 w-32">
                                        <div class="w-full h-8 px-1 py-0 text-sm flex items-center">{{ $item }}</div>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 text-center w-16">
                                        @php
                                            $result = getCheckResult($groupedResults, 4, $i, $check);
                                            echo isset($options[$result]) ? $options[$result] : '<span class="text-gray-400 italic">-</span>';
                                        @endphp
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 text-sm w-64">
                                        @php
                                            $keterangan = getKeterangan($groupedResults, 4, $i, $check);
                                        @endphp
                                        <span class="{{ $keterangan === 'data belum diisi' ? 'text-gray-400 italic' : '' }}">
                                            {{ $keterangan }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-12 z-10">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                    {{ getCheckerName($check, 4) ?: '-' }}
                                    <br>
                                    @php
                                        $checkDate = getCheckDate($check, 4);
                                    @endphp
                                    {{ $checkDate ? \Carbon\Carbon::parse($checkDate)->translatedFormat('d F Y') : '-' }}
                                </td>
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-12 z-10">Penanggung Jawab</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-green-50">
                                    @php
                                        $approvedBy = getApprovedBy($check, 4);
                                    @endphp
                                    
                                    @if($approvedBy)
                                        <!-- Jika sudah ada penanggung jawab, tampilkan namanya -->
                                        <div class="w-full px-2 py-1 text-sm">
                                            <input type="text" name="approved_by_minggu4" value="{{ $approvedBy }}"
                                                class="w-full px-2 py-1 text-sm bg-sky-50 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="approve_minggu4" value="4">
                                        </div>
                                    @else
                                        <!-- Jika belum ada penanggung jawab, tampilkan tombol pilih -->
                                        <div>
                                            <!-- Tombol Pilih -->
                                            <div x-show="minggu4Selected === false">
                                                <button type="button" 
                                                    @click="minggu4Selected = true"
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                    Pilih
                                                </button>
                                            </div>
                                            
                                            <!-- Form fields ketika dipilih -->
                                            <div class="mt-1" x-show="minggu4Selected === true">
                                                <input type="text" name="approved_by_minggu4" value="{{ Auth::user()->username }}"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                    x-bind:disabled="!minggu4Selected"
                                                    readonly>
                                                <input type="hidden" name="approve_minggu4" value="4" x-bind:disabled="!minggu4Selected">
                                                <button type="button" 
                                                    @click="minggu4Selected = false"
                                                    class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                    Batal Pilih
                                                </button>
                                            </div>
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
                    Catatan Pemeriksaan
                </h5>
                
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span>Vacuum cleaner dibersihkan per 2 minggu sekali.</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span>Setiap pengecekan vacuum cleaner bergantian antara Maintenance.</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span>Setelah membersihkan, dimohon untuk mengisi FORM CHECKLIST VACUUM CLEANER yang sudah disediakan.</span>
                    </li>
                </ul>

                <div class="mt-4 p-3 bg-white rounded-lg col-span-1 md:col-span-2 lg:col-span-3 mb-4">
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
                
                <div class="mt-4 pt-3 border-t border-blue-100">
                    <h6 class="font-medium text-blue-600 mb-2">Daftar Vacuum Cleaner:</h6>
                    <div class="grid md:grid-cols-3 gap-2">
                        <div class="bg-white p-2 rounded shadow-sm border border-gray-100">
                            <span class="font-semibold">Vacuum Cleaner No 1:</span>
                            <span class="ml-1">Nilvis</span>
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm border border-gray-100">
                            <span class="font-semibold">Vacuum Cleaner No 2:</span>
                            <span class="ml-1">Modif</span>
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm border border-gray-100">
                            <span class="font-semibold">Vacuum Cleaner No 3:</span>
                            <span class="ml-1">Ransel</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Button Controls -->
            <div class="flex flex-row flex-wrap items-center justify-between gap-2 mt-6">
                <!-- Tombol Kembali -->
                <div class="flex-shrink-0">
                    <a href="{{ route('vacuum-cleaner.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex flex-row flex-wrap gap-2 justify-end">
                    @php
                        // Logika status persetujuan yang benar
                        $approver_minggu2_filled = !empty($check->approver_minggu2);
                        $approver_minggu4_filled = !empty($check->approver_minggu4);
                        
                        // Tentukan status berdasarkan kondisi
                        if ($approver_minggu2_filled && $approver_minggu4_filled) {
                            // Keduanya terisi = Disetujui (tampilkan PDF buttons)
                            $isFullyApproved = true;
                            $isPartiallyApproved = false;
                        } elseif ($approver_minggu2_filled || $approver_minggu4_filled) {
                            // Salah satu terisi = Disetujui Sebagian (tampilkan tombol Setujui)
                            $isFullyApproved = false;
                            $isPartiallyApproved = true;
                        } else {
                            // Keduanya kosong = Belum Disetujui (tampilkan tombol Setujui)
                            $isFullyApproved = false;
                            $isPartiallyApproved = false;
                        }
                    @endphp

                    @if ($isFullyApproved)
                        <!-- Tampilkan tombol PDF jika sudah disetujui penuh -->
                        <!-- Tombol Preview PDF -->
                        <a href="{{ route('vacuum-cleaner.pdf', $check->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>

                        <!-- Tombol Download PDF -->
                        <a href="{{ route('vacuum-cleaner.downloadPdf', $check->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    @else
                        <!-- Tampilkan tombol Setujui jika belum disetujui atau disetujui sebagian -->
                        <button type="submit" 
                                class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out"
                                @click="return submit()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui
                        </button>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection