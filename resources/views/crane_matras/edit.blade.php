<!-- resources/views/crane-matras/edit.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Edit Pencatatan Mesin Crane Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Edit Pencatatan Mesin Crane Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('crane-matras.update', $check->id) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- No Crane Matras (Read Only) -->
                <div class="relative w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        No Crane Matras: <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Crane Matras {{ $checkerData['nomer_crane_matras'] }}
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server -->
                    <input type="hidden" name="nomer_crane_matras" value="{{ $checkerData['nomer_crane_matras'] }}" required>
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                        Bulan: <span class="text-red-500">*</span>
                    </label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        {{ date('F Y', strtotime($checkerData['bulan'])) }}
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
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];
            @endphp
            
            <!-- Input untuk menyimpan semua checked items -->
            @foreach($items as $index => $item)
                <input type="hidden" name="checked_items[{{ $index }}]" value="{{ $item }}">
            @endforeach

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
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" name="tanggal_1" x-ref="date1" value="{{ $checkerData['tanggal_1'] ?? '' }}"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_1" x-ref="checkNum1" value="1">
                                        </div>
                                        
                                        <!-- Show button only if there's no prefilled data -->
                                        <template x-if="!isPrefilledData">
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

            <!-- Tombol Submit dan Kembali -->
            @include('components.create-form-buttons', ['backRoute' => route('crane-matras.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Menambahkan validasi sebelum form dikirim
        document.querySelector('form').addEventListener('submit', function(e) {
            // Mendapatkan komponen Alpine dari dropdown
            const dropdown = Alpine.evaluate(document.querySelector('[name="nomer_crane_matras"]').closest('[x-data]'), 'validate()');
            
            // Jika validasi gagal, hentikan pengiriman form
            if (!dropdown) {
                e.preventDefault();
            }
        });
        
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