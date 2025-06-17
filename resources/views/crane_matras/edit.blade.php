<!-- resources/views/crane-matras/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Pencatatan Mesin Crane Matras')

@section('page-title', 'Edit Pencatatan Mesin Crane Matras')

@section('show-checker', true)

@section('content')
    <!-- Form Input -->
    <form action="{{ route('crane-matras.update', $check->hashid) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <!-- No Crane Matras (Read Only) -->
            <div class="relative w-full">
                <label class="block mb-2 text-sm font-medium text-gray-700">
                    No Crane Matras: <span class="text-red-500">*</span>
                </label>
                
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    Crane Matras Nomor {{ $checkerData['nomer_crane_matras'] }}
                </div>
                
                <!-- Hidden Input untuk dikirim ke server -->
                <input type="hidden" name="nomer_crane_matras" value="{{ $checkerData['nomer_crane_matras'] }}" required>
            </div>
        
            <div>
                <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                    Bulan: <span class="text-red-500">*</span>
                </label>
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    {{ \Carbon\Carbon::parse($checkerData['bulan'])->translatedFormat('F Y') }}
                </div>
                <input type="hidden" id="bulan" name="bulan" value="{{ $checkerData['bulan'] }}" required>
            </div>
        </div>                  
        @php
            // Items yang perlu di-check (ambil dari data yang ada)
            $displayItems = [];
            foreach ($items as $index => $item) {
                $displayItems[$index + 1] = $item;
            }

            // Opsi check
            $options = [
                'V' => 'V',
                'X' => 'X',
                '-' => '-',
                'OFF' => 'OFF'
            ];
        @endphp
        
        <!-- Input untuk menyimpan semua checked items -->
        @foreach($items as $index => $item)
            <input type="hidden" name="checked_items[{{ $index }}]" value="{{ $item }}">
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
                        @foreach($formattedResults as $index => $result)
                            <tr>
                                <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10" style="width: 40px;">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10" style="width: 180px; max-width: 180px;">
                                    <div class="w-full h-8 px-1 py-0 text-xs flex items-center overflow-hidden text-ellipsis">{{ $result['item'] }}</div>
                                </td>
                                
                                <!-- Check -->
                                <td class="border border-gray-300 p-1 h-10">
                                    <select name="check[{{ $index }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        @foreach($options as $value => $symbol)
                                            <option value="{{ $value }}" {{ $result['check'] == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-1 h-10">
                                    <input type="text" name="keterangan[{{ $index }}]" 
                                        value="{{ $result['keterangan'] }}"
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
                                <div x-data="{ 
                                    selected: {{ !empty($checkerData['checked_by_1']) ? 'true' : 'false' }}, 
                                    userName: '{{ $checkerData['checked_by_1'] ?? '' }}', 
                                    tanggal: '{{ $checkerData['tanggal_1'] ?? '' }}',
                                    isPrefilledData: {{ !empty($checkerData['checked_by_1']) && !empty($checkerData['tanggal_1']) ? 'true' : 'false' }}
                                }">
                                    <div class="mt-1" x-show="selected || isPrefilledData">
                                        <input type="text" name="checked_by_1" x-ref="user1" value="{{ $checkerData['checked_by_1'] ?? '' }}"
                                            class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded mb-1 text-center">
                                        <input type="text" name="tanggal_1" x-ref="date1" value="{{ $checkerData['tanggal_1'] ?? '' }}"
                                            class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center">
                                        <!-- Hidden input untuk menyimpan checker_id -->
                                        <input type="hidden" name="checker_id" value="{{ $user->id }}">
                                        <input type="hidden" name="check_num_1" x-ref="checkNum1" value="1">
                                    </div>
                                    
                                    <!-- Show button only if there's no prefilled data -->
                                    <template x-if="!isPrefilledData">
                                        <button type="button" 
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ $user->username }}'; 
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
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($approvalStatus)
        <!-- Status Approval Information -->
        <div class="p-3 mb-4 bg-yellow-50 border border-yellow-200 rounded-md">
            <p class="text-yellow-700 font-medium">
                <i class="fas fa-info-circle mr-2"></i>
                Data ini telah diapprove oleh supervisor. Perubahan yang dilakukan akan memerlukan approval ulang.
            </p>
        </div>
        @endif

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