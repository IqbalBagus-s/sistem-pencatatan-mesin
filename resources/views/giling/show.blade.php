<!-- resources/views/giling/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pemeriksaan Mesin Giling')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pemeriksaan Mesin Giling</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Form wrapper with Alpine.js untuk persetujuan -->
        <form action="{{ route('giling.approve', $check->id) }}" method="POST" id="approvalForm"
              x-data="{
                dbApprover1: '{{ $check->approved_by1 }}',
                dbApprover2: '{{ $check->approved_by2 }}',
                dbApprovalDate1: '{{ $check->approval_date1 }}',
                approver1: '{{ $check->approved_by1 }}',
                approver2: '{{ $check->approved_by2 }}',
                approvalDate1: '{{ $check->approval_date1 }}',
                formChanged: false,
                
                pilihApprover(position) {
                    const user = '{{ Auth::user()->username }}';
                    const currentDate = new Date().toISOString().split('T')[0];
                    
                    if (position === 1) {
                        this.approver1 = user;
                        this.approvalDate1 = currentDate;
                    } else if (position === 2) {
                        this.approver2 = user;
                    }
                    this.updateFormChanged();
                },
                
                batalPilih(position) {
                    if (position === 1) {
                        this.approver1 = '';
                        this.approvalDate1 = '';
                    } else if (position === 2) {
                        this.approver2 = '';
                    }
                    this.updateFormChanged();
                },
                
                updateFormChanged() {
                    this.formChanged = (this.approver1 !== this.dbApprover1) || 
                                    (this.approver2 !== this.dbApprover2) ||
                                    (this.approvalDate1 !== this.dbApprovalDate1);
                },
                
                canSubmit() {
                    return this.formChanged && (this.approver1 !== '' || this.approver2 !== '');
                }
              }">
            @csrf
            <!-- Hidden fields for form submission -->
            <input type="hidden" name="approved_by1" x-model="approver1">
            <input type="hidden" name="approved_by2" x-model="approver2">
            <input type="hidden" name="approval_date1" x-model="approvalDate1">
        
            <!-- Header dengan Info Petugas -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="bg-sky-50 p-4 rounded-md">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">{{ $check->checked_by }}</span>
                </div>
                <div class="bg-sky-50 p-4 rounded-md">
                    <span class="text-gray-600 font-bold">Approver: </span>
                    <span class="font-bold text-blue-700">{{ $check->approved_by ?? Auth::user()->username }}</span>
                </div>
            </div>

            <!-- Minggu dan Bulan -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="w-full">
                    <label class="block mb-2">Minggu:</label>
                    <div class="px-3 py-2 bg-white border border-blue-400 rounded-md">
                        Minggu ke-{{ $check->minggu }}
                    </div>
                </div>
                <div class="w-full">
                    <label for="bulan" class="block mb-2">Bulan:</label>
                    <div class="px-3 py-2 bg-white border border-blue-400 rounded-md">
                        {{ \Carbon\Carbon::parse($check->bulan)->translatedFormat('F Y') }}
                    </div>
                </div>
            </div>
            
            <!-- Tabel Pemeriksaan Mesin Giling -->
            <div class="mb-6">
                <div class="overflow-x-auto border border-gray-300 rounded-lg">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-sky-50">
                                <th rowspan="2" class="border border-gray-300 p-2 text-center">No.</th>
                                <th rowspan="2" class="border border-gray-300 p-2 text-center">Checked Items</th>
                                <th colspan="10" class="border border-gray-300 p-2 text-center">HASIL PEMERIKSAAN GILINGAN</th>
                            </tr>
                            <tr class="bg-sky-50">
                                @for ($i = 1; $i <= 10; $i++)
                                    <th class="border border-gray-300 p-2 text-center">G{{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $items = [
                                    1 => 'Cek Motor Mesin Giling',
                                    2 => 'Cek Vanbelt',
                                    3 => 'Cek Dustcollector',
                                    4 => 'Cek Safety Switch',
                                    5 => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
                                ];
                            @endphp
                            
                            @foreach($items as $i => $item)
                                <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                    <td class="border border-gray-300 p-2">{{ $item }}</td>
                                    
                                    @for ($g = 1; $g <= 10; $g++)
                                        <td class="border border-gray-300 p-2 text-center">
                                            @php
                                                $result = isset($results[$item]) ? $results[$item] : null;
                                                $value = $result ? $result->{"g$g"} : '-';
                                            @endphp
                                            <span class="{{ $value == 'V' ? 'text-green-600 font-bold' : ($value == 'X' ? 'text-red-600 font-bold' : '') }}">
                                                {{ $value }}
                                            </span>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Informasi Standar dan Kriteria -->
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
                                ['Motor Mesin Giling', 'Suara halus, tidak panas berlebih'],
                                ['Vanbelt', 'Tidak pecah/retak, kekencangan sesuai standar'],
                                ['Dustcollector', 'Berfungsi normal, tidak tersumbat'],
                                ['Safety Switch', 'Berfungsi dengan baik saat diuji'],
                                ['Ketajaman Pisau', 'Tajam dan tidak tumpul, tidak ada kerusakan (Pemeriksaan pada minggu keempat setiap bulan)']
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
                                ['-', 'Tidak Diisi', 'white'],
                                ['OFF', 'Mesin Mati', 'white']
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

            <div class="mb-6">
                <div class="border bg-sky-50 border-gray-300 rounded-lg p-4">
                    <h3 class="font-semibold text-blue-700 mb-2">Catatan Tambahan:</h3>
                    <div class="bg-white p-3 rounded-md border border-blue-500">
                        {{ $check->keterangan }}
                    </div>
                </div>
            </div>

            <!-- Sistem Persetujuan -->
            <div class="mb-6">
                <div class="border border-gray-300 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Persetujuan Laporan:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <!-- Approver 1 with date -->
                        <div class="p-4 bg-white shadow rounded border border-blue-300">
                            <label class="block text-gray-700 font-semibold mb-3">Pelaksana Utility</label>
                            
                            <!-- Horizontal layout with two columns -->
                            <div class="grid grid-cols-2 gap-2 mb-3">
                                <!-- Username field -->
                                <div>
                                    <input type="text" 
                                        class="w-full p-2 border rounded border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                        x-model="approver1" readonly placeholder="Nama">
                                </div>
                                
                                <!-- Date field -->
                                <div>
                                    <input type="date" 
                                        class="w-full p-2 border rounded border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                        x-model="approvalDate1" readonly>
                                </div>
                            </div>
                            
                            <!-- Conditional buttons -->
                            <button type="button" 
                                x-show="!approver1" 
                                class="w-full bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-700 cursor-pointer" 
                                @click="pilihApprover(1)">
                                Pilih 
                            </button>
                            
                            <button type="button" 
                                x-show="approver1" 
                                class="w-full bg-red-500 text-white py-2 px-3 rounded hover:bg-red-600 cursor-pointer" 
                                @click="batalPilih(1)">
                                Batal Pilih
                            </button>
                        </div>

                        <!-- Approver 2 with full-width name field -->
                        <div class="p-4 bg-white shadow rounded border border-blue-300">
                            <label class="block text-gray-700 font-semibold mb-3">Koordinator Staff Utility</label>
                            
                            <!-- Full-width name field -->
                            <div class="mb-3">
                                <input type="text" 
                                    class="w-full p-2 border rounded border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    x-model="approver2" readonly placeholder="Nama">
                            </div>
                            
                            <!-- Conditional buttons with same height as first section -->
                            <button type="button" 
                                x-show="!approver2" 
                                class="w-full bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-700 cursor-pointer" 
                                @click="pilihApprover(2)">
                                Pilih
                            </button>
                            
                            <button type="button" 
                                x-show="approver2" 
                                class="w-full bg-red-500 text-white py-2 px-3 rounded hover:bg-red-600 cursor-pointer" 
                                @click="batalPilih(2)">
                                Batal Pilih
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-8 bg-white rounded-lg shadow p-2 sm:p-4">
                <div class="flex flex-row flex-wrap items-center justify-between gap-2">
                    <!-- Back Button - Left Side -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('giling.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                    
                    <!-- Action Buttons - Right Side -->
                    <div class="flex flex-row flex-wrap gap-2 justify-end">
                        <!-- Simpan Persetujuan Button - Only shown when at least one approval is missing -->
                        @if (empty($check->approved_by1) || empty($check->approved_by2))
                            <button type="submit" 
                                class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-300 ease-in-out"
                                :class="{ 'opacity-50 cursor-not-allowed': !formChanged }"
                                :disabled="!formChanged">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Persetujuan
                            </button>
                        @endif
                        
                        <!-- PDF Preview and Download Buttons - Only shown when both approvals are present -->
                        @if (!empty($check->approved_by1) && !empty($check->approved_by2))
                            <!-- PDF Preview Button -->
                            <a href="{{ route('giling.pdf', $check->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Preview PDF
                            </a>
                            
                            <!-- Download PDF Button -->
                            <a href="{{ route('giling.downloadPdf', $check->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
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