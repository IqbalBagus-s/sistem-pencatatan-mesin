<!-- resources/views/vacuum-cleaner/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Vacuum Cleaner')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Vacuum Cleaner</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ $user->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('vacuum-cleaner.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Dropdown Pilih No Vacuum Cleaner -->
                <div x-data="{ 
                    open: false, 
                    selected: null,
                    reset() {
                        this.selected = null;
                        this.open = false;
                    }
                }" class="relative w-full">
                    <!-- Label -->
                    <label class="block mb-2 text-sm font-medium text-gray-700">Pilih No Vacuum Cleaner:</label>
                    
                    <!-- Dropdown Button -->
                    <button type="button" @click="open = !open" class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm text-left focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white relative">
                        <span x-text="selected ? 'Vacuum Cleaner ' + selected : 'Pilih Vacuum Cleaner'"></span>
                        
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
                                    <button type="button" @click="selected = i; open = false" class="w-full px-3 py-2 text-sm text-gray-700 hover:bg-blue-500 hover:text-white rounded-md">
                                        <span x-text="'Vacuum Cleaner ' + i"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Hidden Input untuk dikirim ke server -->
                    <input type="hidden" name="nomer_vacuum_cleaner" x-model="selected">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">Pilih Bulan:</label>
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                </div>
            </div>                    
            @php
                // Items yang perlu di-check (updated list)
                $items = [
                    1 => 'Kebersihan Body',
                    2 => 'Motor',
                    3 => 'Selang',
                    4 => 'Aksesoris',
                    5 => 'Filter',
                    6 => 'Bostel',
                    7 => 'Kabel',
                ];

                // Opsi check
                $options = [
                    'V' => 'V',
                    'X' => 'X',
                    '-' => '-',
                    'OFF' => 'OFF'
                ];
            @endphp
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
                <!-- Tabel untuk minggu ke-2 -->
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
                                    <td class="border border-gray-300 p-1 h-10 w-20">
                                        <select name="check_1[{{ $i }}]" class="w-full h-8 px-2 py-0 text-center text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-64">
                                        <input type="text" name="keterangan_1[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris checker --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-sm sticky left-0 z-10 w-12" rowspan="3">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center bg-sky-50 text-sm sticky left-12 z-10 w-32">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <input type="text" name="checked_by_1" x-ref="user"
                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                        readonly>
                                    <input type="hidden" name="check_num_1" x-ref="checkNum" value="">
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm text-center sticky left-12 z-10 w-32">Tanggal</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <input type="text" name="check_date_1" x-ref="date"
                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                        readonly>
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm sticky left-12 z-10 w-32"></td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
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
                                    }">
                                        <button type="button" 
                                            @click="
                                                selected = !selected; 
                                                if(selected) {
                                                    $refs.user.value = '{{ $user->username }}';
                                                    $refs.checkNum.value = '1';
                                                    $refs.date.value = getFormattedDate();
                                                } else {
                                                    $refs.user.value = '';
                                                    $refs.checkNum.value = '';
                                                    $refs.date.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Tabel untuk minggu ke-4 -->
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
                                    <td class="border border-gray-300 p-1 h-10 w-20">
                                        <select name="check_2[{{ $i }}]" class="w-full h-8 px-2 py-0 text-center text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}">{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-64">
                                        <input type="text" name="keterangan_2[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris checker --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-sm sticky left-0 z-10 w-12" rowspan="3">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center bg-sky-50 text-sm sticky left-12 z-10 w-32">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <input type="text" name="checked_by_2" x-ref="user2"
                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                        readonly>
                                    <input type="hidden" name="check_num_2" x-ref="checkNum2" value="">
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm text-center sticky left-12 z-10 w-32">Tanggal</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <input type="text" name="check_date_2" x-ref="date2"
                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                        readonly>
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm sticky left-12 z-10 w-32"></td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
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
                                    }">
                                        <button type="button" 
                                            @click="
                                                selected = !selected; 
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

            @include('components.create-form-buttons', ['backRoute' => route('vacuum-cleaner.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById("tanggal")) {
            document.getElementById("tanggal").addEventListener("change", function() {
                let tanggal = new Date(this.value);
                let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
                document.getElementById("hari").value = hari;
            });
        }
    });
    
</script>
@endsection