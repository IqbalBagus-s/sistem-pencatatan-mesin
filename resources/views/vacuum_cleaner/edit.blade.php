<!-- resources/views/vacuum-cleaner/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Form Pencatatan Mesin Vacuum Cleaner')

@section('page-title', 'Edit Pencatatan Mesin Vacuum Cleaner')

@section('content')

    <!-- Menampilkan Nama Checker -->
    <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ $user->username }}</span>
    </div>

    <!-- Form Input -->
    <form action="{{ route('vacuum-cleaner.update', $check->id) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Info Vacuum Cleaner yang dipilih -->
                <div class="relative w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">
                        No Vacuum cleaner: 
                    </label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm text-left flex items-center">
                        <span>Vacuum cleaner nomor {{ $check->nomer_vacum_cleaner }}</span>
                    </div>
                    <!-- Hidden input untuk nomer vacuum cleaner -->
                    <input type="hidden" name="nomer_vacuum_cleaner" value="{{ $check->nomer_vacum_cleaner }}">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                        Bulan:
                    </label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm text-left flex items-center">
                        <span>{{ \Carbon\Carbon::parse($check->bulan)->locale('id')->isoFormat('MMMM YYYY') }}</span>
                    </div>
                    <!-- Hidden input untuk bulan -->
                    <input type="hidden" name="bulan" value="{{ $check->bulan }}">
                </div>
            </div>                    
            @php
                // Items yang perlu di-check
                $items = $itemsMap;

                // Opsi check
                $options = [
                    'V' => 'V',
                    'X' => 'X',
                    '-' => '-',
                    'OFF' => 'OFF'
                ];
                
                // Fungsi untuk format tanggal ke bahasa Indonesia
                function formatTanggalIndonesia($tanggal) {
                    if (!$tanggal) return '';
                    
                    $bulanIndo = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    
                    $timestamp = strtotime($tanggal);
                    $hari = date('j', $timestamp); // hari tanpa leading zero
                    $bulan = $bulanIndo[date('n', $timestamp)]; // bulan dalam bahasa Indonesia
                    $tahun = date('Y', $timestamp);
                    
                    return "$hari $bulan $tahun";
                }
                
                // Format tanggal untuk tampilan
                $tanggal_minggu2 = $check->tanggal_dibuat_minggu2 ? formatTanggalIndonesia($check->tanggal_dibuat_minggu2) : '';
                $tanggal_minggu4 = $check->tanggal_dibuat_minggu4 ? formatTanggalIndonesia($check->tanggal_dibuat_minggu4) : '';
                
                // Check if approvers exist to set readonly status
                $isReadonlyMinggu2 = !empty($check->approver_minggu2) && $check->approver_minggu2 != '-';
                $isReadonlyMinggu4 = !empty($check->approver_minggu4) && $check->approver_minggu4 != '-';
            @endphp
            
            <style>
                /* Hide dropdown arrow for readonly selects */
                select.readonly-select {
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    appearance: none;
                    background-image: none;
                    cursor: default;
                }
                
                /* Prevent focus styles on readonly elements */
                select.readonly-select:focus,
                input.readonly-input:focus {
                    outline: none !important;
                    ring: 0 !important;
                    box-shadow: none !important;
                    background-color: #dcfce7 !important;
                }
            </style>
            
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <div x-data="{ 
                        selectedWeeks: {
                            week1: false,
                            week2: false
                        },
                        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                        getFormattedDate() {
                            const now = new Date();
                            const day = now.getDate();
                            const month = this.monthNames[now.getMonth()];
                            const year = now.getFullYear();
                            return `${day} ${month} ${year}`;
                        },
                        toggleWeek(week) {
                            this.selectedWeeks[week] = !this.selectedWeeks[week];
                            
                            if(week === 'week1') {
                                if(this.selectedWeeks.week1) {
                                    $refs.user1.value = '{{ $user->username }}';
                                    $refs.checkNum1.value = '1';
                                    $refs.date1.value = this.getFormattedDate();
                                } else {
                                    $refs.user1.value = '';
                                    $refs.checkNum1.value = '';
                                    $refs.date1.value = '';
                                }
                            } else if(week === 'week2') {
                                if(this.selectedWeeks.week2) {
                                    $refs.user2.value = '{{ $user->username }}';
                                    $refs.checkNum2.value = '2';
                                    $refs.date2.value = this.getFormattedDate();
                                } else {
                                    $refs.user2.value = '';
                                    $refs.checkNum2.value = '';
                                    $refs.date2.value = '';
                                }
                            }
                        }
                    }">
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
                                @php
                                    $result = null;
                                    $keterangan = null;
                                    
                                    // Get data for this item if it exists in minggu 2
                                    if(isset($groupedResults[2])) {
                                        $minggu2Data = $groupedResults[2]->where('item_id', $i)->first();
                                        if($minggu2Data) {
                                            $result = $minggu2Data['result'];
                                            $keterangan = $minggu2Data['keterangan'];
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 bg-white z-10 w-12">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-12 bg-white z-10 w-32">
                                        <div class="w-full h-8 px-1 py-0 text-sm flex items-center">{{ $item }}</div>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-20">
                                        <select name="check_1[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-center text-sm border border-gray-300 rounded {{ $isReadonlyMinggu2 ? 'bg-green-100 readonly-select' : 'bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white' }}" 
                                            {{ $isReadonlyMinggu2 ? 'disabled' : '' }}>
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}" {{ $result == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                        @if($isReadonlyMinggu2)
                                            <input type="hidden" name="check_1[{{ $i }}]" value="{{ $result }}">
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-64">
                                        <input type="text" name="keterangan_1[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm border border-gray-300 rounded {{ $isReadonlyMinggu2 ? 'bg-green-100 readonly-input' : 'bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white' }}"
                                            placeholder="Keterangan" value="{{ $keterangan }}" {{ $isReadonlyMinggu2 ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris checker minggu 2 --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 z-10 w-12">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center text-sm sticky left-12 z-10 w-32">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                                    @if($isReadonlyMinggu2)
                                        <div class="flex flex-col gap-1 w-full">
                                            <div class="w-full text-sm text-center bg-green-100 py-1 rounded">{{ $check->checker_minggu2 }}</div>
                                            <div class="w-full text-sm text-center bg-green-100 py-1 rounded">{{ $tanggal_minggu2 }}</div>
                                            @if(!empty($check->approver_minggu2) && $check->approver_minggu2 != '-')
                                                <div class="text-xs text-green-600 text-center">Disetujui oleh: {{ $check->approver_minggu2 }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-2 w-full">
                                            <input type="text" name="checked_by_1" x-ref="user1"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center readonly-input"
                                                readonly value="{{ old('checked_by_1', $check->checker_minggu2) }}">

                                            <input type="text" name="check_date_1" x-ref="date1"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center readonly-input"
                                                readonly value="{{ old('check_date_1', $tanggal_minggu2 ? formatTanggalIndonesia($tanggal_minggu2) : '') }}">

                                            <input type="hidden" name="check_num_1" x-ref="checkNum1" value="{{ $check_num_1 }}">

                                            @if(empty($check->checker_minggu2))
                                                <div x-data="{ 
                                                    selected: false,
                                                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                                                    getFormattedDate() {
                                                        const now = new Date();
                                                        const day = now.getDate();
                                                        const month = this.monthNames[now.getMonth()];
                                                        const year = now.getFullYear();
                                                        return `${day} ${month} ${year}`;
                                                    }
                                                }" class="w-full">
                                                    <button type="button" 
                                                        @click="selected = !selected; 
                                                            if(selected) {
                                                                $refs.user1.value = '{{ $user->username }}';
                                                                $refs.checkNum1.value = '1';
                                                                $refs.date1.value = getFormattedDate();
                                                            } else {
                                                                $refs.user1.value = '';
                                                                $refs.checkNum1.value = '';
                                                                $refs.date1.value = '';
                                                            }"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                                    </button>
                                                </div>
                                            @endif
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
                                @php
                                    $result = null;
                                    $keterangan = null;
                                    
                                    // Get data for this item if it exists in minggu 4
                                    if(isset($groupedResults[4])) {
                                        $minggu4Data = $groupedResults[4]->where('item_id', $i)->first();
                                        if($minggu4Data) {
                                            $result = $minggu4Data['result'];
                                            $keterangan = $minggu4Data['keterangan'];
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 bg-white z-10 w-12">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-12 bg-white z-10 w-32">
                                        <div class="w-full h-8 px-1 py-0 text-sm flex items-center">{{ $item }}</div>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-20">
                                        <select name="check_2[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-center text-sm border border-gray-300 rounded {{ $isReadonlyMinggu4 ? 'bg-green-100 readonly-select' : 'bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white' }}" 
                                            {{ $isReadonlyMinggu4 ? 'disabled' : '' }}>
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}" {{ $result == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                        @if($isReadonlyMinggu4)
                                            <input type="hidden" name="check_2[{{ $i }}]" value="{{ $result }}">
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-64">
                                        <input type="text" name="keterangan_2[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm border border-gray-300 rounded {{ $isReadonlyMinggu4 ? 'bg-gray-100 readonly-input' : 'bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white' }}"
                                            placeholder="Keterangan" value="{{ $keterangan }}" {{ $isReadonlyMinggu4 ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris checker minggu 4 --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 z-10 w-12">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center text-sm sticky left-12 z-10 w-32">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                                    @if($isReadonlyMinggu4)
                                        <div class="flex flex-col gap-1 w-full">
                                            <div class="w-full text-sm text-center bg-green-100 py-1 rounded">{{ $check->checker_minggu4 }}</div>
                                            <div class="w-full text-sm text-center bg-green-100 py-1 rounded">{{ $tanggal_minggu4 }}</div>
                                            @if(!empty($check->approver_minggu4) && $check->approver_minggu4 != '-')
                                                <div class="text-xs text-green-600 text-center">Disetujui oleh: {{ $check->approver_minggu4 }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-2 w-full">
                                            <input type="text" name="checked_by_2" x-ref="user2"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center readonly-input"
                                                readonly value="{{ old('checked_by_2', $check->checker_minggu4) }}">
                                            
                                            <input type="text" name="check_date_2" x-ref="date2"
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center readonly-input"
                                                readonly value="{{ old('check_date_2', $tanggal_minggu4 ? formatTanggalIndonesia($tanggal_minggu4) : '') }}">
                                            
                                            <input type="hidden" name="check_num_2" x-ref="checkNum2" value="{{ $check_num_2 }}">

                                            @if(empty($check->checker_minggu4))
                                                <div x-data="{ 
                                                    selected: false,
                                                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                                                    getFormattedDate() {
                                                        const now = new Date();
                                                        const day = now.getDate();
                                                        const month = this.monthNames[now.getMonth()];
                                                        const year = now.getFullYear();
                                                        return `${day} ${month} ${year}`;
                                                    }
                                                }" class="w-full">
                                                    <button type="button" 
                                                        @click="selected = !selected; 
                                                            if(selected) {
                                                                $refs.user2.value = '{{ $user->username }}';
                                                                $refs.checkNum2.value = '2';
                                                                $refs.date2.value = getFormattedDate();
                                                            } else {
                                                                $refs.user2.value = '';
                                                                $refs.checkNum2.value = '';
                                                                $refs.date2.value = '';
                                                            }"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                        :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                        <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
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

            @include('components.edit-form-buttons', ['backRoute' => route('vacuum-cleaner.index')])
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Additional JavaScript if needed
    });
</script>
@endsection