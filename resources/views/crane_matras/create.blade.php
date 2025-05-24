<!-- resources/views/crane-matras/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Crane Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Crane Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('crane-matras.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Dropdown Pilih No Crane Matras - With Required Validation -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    },
                }" class="relative w-full">
                    <!-- Label with Required Indicator -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        Pilih No Crane Matras:
                    </label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" 
                        @click="open = !open" 
                        class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Crane Matras nomor ' + selected : 'Pilih Crane Matras'"></span>
                        
                        <!-- Selection Indicator -->
                        <div class="absolute right-3 top-1/2 -translate-y-1/2">
                            <!-- Checkmark when selected -->
                            <svg x-show="selected" @click.stop="reset()" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            
                            <!-- Dropdown Arrow when not selected -->
                            <svg x-show="!selected" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    
                    <!-- Dropdown List -->
                    <div x-show="open" @click.away="open = false" class="absolute left-0 mt-1 w-full bg-white border border-blue-400 shadow-lg rounded-md p-2 z-50 max-h-60 overflow-y-auto">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="i in 3" :key="i">
                                <div @click.stop>
                                    <button type="button" @click="selected = i; open = false;" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'Crane Matras ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server (required) -->
                    <input type="hidden" name="nomer_crane_matras" x-model="selected">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                        Pilih Bulan:
                    </label>
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                </div>
            </div>                  
            @php
                // Items yang perlu di-check
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

                // Opsi check
                $options = [
                    'V' => 'V',
                    'X' => 'X',
                    '-' => '-',
                    'OFF' => 'OFF'
                ];
            @endphp
            
            <!-- Input untuk menyimpan semua checked items -->
            @foreach($items as $i => $item)
                <input type="hidden" name="checked_items[{{ $i-1 }}]" value="{{ $item }}">
            @endforeach

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
                            @foreach($items as $i => $item)
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10" style="width: 40px;">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10" style="width: 180px; max-width: 180px;">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center overflow-hidden text-ellipsis">{{ $item }}</div>
                                    </td>
                                    
                                    <!-- Check - DIUBAH MENJADI FORMAT ARRAY -->
                                    <td class="border border-gray-300 p-1 h-10">
                                        <select name="check[{{ $i-1 }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10">
                                        <input type="text" name="keterangan[{{ $i-1 }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1" style="width: 40px;">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10" style="width: 150px; max-width: 150px;">Dibuat Oleh</td>
                                
                                <td colspan="4" class="border border-gray-300 p-1 bg-sky-50">
                                    <div x-data="{ selected: false, userName: '', tanggal: '' }">
                                        <div class="mt-1" x-show="selected">
                                            <input type="text" name="checked_by_1" x-ref="user1" x-bind:value="userName"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" name="tanggal_1" x-ref="date1" x-bind:value="tanggal"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_1" x-ref="checkNum1" value="1">
                                        </div>
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user1.value = userName;
                                                    
                                                    // Format tanggal: DD Bulan YYYY
                                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    const today = new Date();
                                                    const day = today.getDate();
                                                    const month = monthNames[today.getMonth()];
                                                    const year = today.getFullYear();
                                                    tanggal = day + ' ' + month + ' ' + year;
                                                    
                                                    $refs.date1.value = tanggal;
                                                    $refs.checkNum1.value = '1';
                                                } else {
                                                    userName = '';
                                                    tanggal = '';
                                                    $refs.user1.value = '';
                                                    $refs.date1.value = '';
                                                    $refs.checkNum1.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
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

            <!-- Tombol Submit dan Kembali -->
            @include('components.create-form-buttons', ['backRoute' => route('crane-matras.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Fungsi untuk format tanggal Indonesia
        Alpine.data('dateFormatter', () => ({
            formatDate() {
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const today = new Date();
                const day = today.getDate();
                const month = monthNames[today.getMonth()];
                const year = today.getFullYear();
                return day + ' ' + month + ' ' + year;
            }
        }));
    });
</script>
@endsection