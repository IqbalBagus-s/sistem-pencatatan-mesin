@extends('layouts.edit-layout-2')

@section('title', 'Edit Form Pencatatan Compressor')
@section('page-title', 'Edit Form Pencatatan Compressor')

@section('styles')
<style>
    .table-container {
        max-height: 500px;
        overflow-y: auto;
    }
    .table-container table thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endsection

@section('content')
    <!-- Form Input -->
    <form action="{{ route('compressor.update', $check->hashid) }}" method="POST" id="compressor-form" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Hari:</label>
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400  rounded-md text-sm flex items-center">
                    {{ $check->hari }}
                </div>
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal:</label>
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    {{ \Carbon\Carbon::parse($check->tanggal)->translatedFormat('d F Y') }}
                </div>
            <input type="hidden" name="bulan" value="{{ $check->tanggal }}">
            </div>
        </div>

        <!-- Form tambahan -->
        <div x-data="{
            shift1Selected: {{ !empty($check->checker_shift1_id) ? 'true' : 'false' }},
            shift2Selected: {{ !empty($check->checker_shift2_id) ? 'true' : 'false' }},
            username: '{{ $user->username }}',
            userId: '{{ $user->id }}',
            
            toggleShift(shift) {
                if (shift === 1) {
                    this.shift1Selected = !this.shift1Selected;
                    
                    if (this.shift1Selected) {
                        this.$refs.shift1.value = this.userId;
                        this.$refs.shift1Display.value = this.username;
                        
                        // Enable Shift 1 input fields
                        this.$refs.tempShift1.disabled = false;
                        this.$refs.tempShift1.classList.remove('bg-blue-300');
                        this.$refs.tempShift1.classList.add('bg-white');
                        this.$refs.humidityShift1.disabled = false;
                        this.$refs.humidityShift1.classList.remove('bg-blue-300');
                        this.$refs.humidityShift1.classList.add('bg-white');
                    } else {
                        this.$refs.shift1.value = '';
                        this.$refs.shift1Display.value = '';
                        
                        // Disable Shift 1 input fields
                        this.$refs.tempShift1.disabled = true;
                        this.$refs.tempShift1.classList.remove('bg-white');
                        this.$refs.tempShift1.classList.add('bg-blue-300');
                        this.$refs.humidityShift1.disabled = true;
                        this.$refs.humidityShift1.classList.remove('bg-white');
                        this.$refs.humidityShift1.classList.add('bg-blue-300');
                    }
                } else if (shift === 2) {
                    this.shift2Selected = !this.shift2Selected;
                    
                    if (this.shift2Selected) {
                        this.$refs.shift2.value = this.userId;
                        this.$refs.shift2Display.value = this.username;
                        
                        // Enable Shift 2 input fields
                        this.$refs.tempShift2.disabled = false;
                        this.$refs.tempShift2.classList.remove('bg-blue-300');
                        this.$refs.tempShift2.classList.add('bg-white');
                        this.$refs.humidityShift2.disabled = false;
                        this.$refs.humidityShift2.classList.remove('bg-blue-300');
                        this.$refs.humidityShift2.classList.add('bg-white');
                    } else {
                        this.$refs.shift2.value = '';
                        this.$refs.shift2Display.value = '';
                        
                        // Disable Shift 2 input fields
                        this.$refs.tempShift2.disabled = true;
                        this.$refs.tempShift2.classList.remove('bg-white');
                        this.$refs.tempShift2.classList.add('bg-blue-300');
                        this.$refs.humidityShift2.disabled = true;
                        this.$refs.humidityShift2.classList.remove('bg-white');
                        this.$refs.humidityShift2.classList.add('bg-blue-300');
                    }
                }
            },
            
            // Initialize the form with existing data
            initForm() {
                // Wait for DOM to be ready
                this.$nextTick(() => {
                    // If data exists for shift 1
                    if ('{{ $check->checker_shift1_id }}') {
                        this.shift1Selected = true;
                        this.$refs.shift1.value = '{{ $check->checker_shift1_id }}';
                        this.$refs.shift1Display.value = '{{ $check->checkerShift1->username ?? '' }}';  // Tampilkan username
                        this.$refs.tempShift1.disabled = false;
                        this.$refs.tempShift1.classList.remove('bg-blue-300');
                        this.$refs.tempShift1.classList.add('bg-white');
                        this.$refs.humidityShift1.disabled = false;
                        this.$refs.humidityShift1.classList.remove('bg-blue-300');
                        this.$refs.humidityShift1.classList.add('bg-white');
                    }
                    
                    // If data exists for shift 2
                    if ('{{ $check->checker_shift2_id }}') {
                        this.shift2Selected = true;
                        this.$refs.shift2.value = '{{ $check->checker_shift2_id }}';
                        this.$refs.shift2Display.value = '{{ $check->checkerShift2->username ?? '' }}';  // Tampilkan username
                        this.$refs.tempShift2.disabled = false;
                        this.$refs.tempShift2.classList.remove('bg-blue-300');
                        this.$refs.tempShift2.classList.add('bg-white');
                        this.$refs.humidityShift2.disabled = false;
                        this.$refs.humidityShift2.classList.remove('bg-blue-300');
                        this.$refs.humidityShift2.classList.add('bg-white');
                    }
                });
            }
            }" x-init="initForm()">
        <!-- Pilih Shift Checker -->
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2">Shift 1:</label>
                <!-- Hidden input untuk ID -->
                <input type="hidden" id="shift1" name="checker_shift1_id" x-ref="shift1" value="{{ $check->checker_shift1_id }}">
                <!-- Visible input untuk display username -->
                <input type="text" x-ref="shift1Display" value="{{ $check->checkerShift1->username ?? '' }}" class="w-full px-3 py-2 bg-white border-blue-400 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" readonly placeholder="Pilih checker untuk shift 1">
                <button type="button" @click="toggleShift(1)" class="mt-2 w-full text-white py-2 rounded cursor-pointer" 
                    :class="shift1Selected ? 'bg-red-600' : 'bg-blue-600'"
                    x-text="shift1Selected ? 'Batal' : 'Pilih'">
                    Pilih
                </button>
            </div>
            <div>
                <label class="block mb-2">Shift 2:</label>
                <!-- Hidden input untuk ID -->
                <input type="hidden" id="shift2" name="checker_shift2_id" x-ref="shift2" value="{{ $check->checker_shift2_id }}">
                <!-- Visible input untuk display username -->
                <input type="text" x-ref="shift2Display" value="{{ $check->checkerShift2->username ?? '' }}" class="w-full px-3 py-2 bg-white border-blue-400 border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" readonly placeholder="Pilih checker untuk shift 2">
                <button type="button" @click="toggleShift(2)" class="mt-2 w-full text-white py-2 rounded cursor-pointer"
                    :class="shift2Selected ? 'bg-red-600' : 'bg-blue-600'"
                    x-text="shift2Selected ? 'Batal' : 'Pilih'">
                    Pilih
                </button>
            </div>
        </div>

        <!-- Independent menu items that don't need Alpine.js functionality -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2">Jumlah Compressor ON KL:</label>
                <input type="text" name="kompressor_on_kl" value="{{ $check->kompressor_on_kl }}" class="w-full px-3 py-2 bg-white border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block mb-2">Jumlah Compressor ON KH:</label>
                <input type="text" name="kompressor_on_kh" value="{{ $check->kompressor_on_kh }}" class="w-full px-3 py-2 bg-white border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        <!-- Mesin ON/OFF -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2">Mesin ON:</label>
                <input type="text" name="mesin_on" value="{{ $check->mesin_on }}" class="w-full px-3 py-2 bg-white border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block mb-2">Mesin OFF:</label>
                <input type="text" name="mesin_off" value="{{ $check->mesin_off }}" class="w-full px-3 py-2 bg-white border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        <!-- Kelembapan Udara -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2">Temperatur Shift 1:</label>
                <input type="text" id="temp-shift-1" x-ref="tempShift1" name="temperatur_shift1" value="{{ $check->temperatur_shift1 }}" 
                    class="w-full px-3 py-2 {{ !empty($check->checker_shift1_id) ? 'bg-white' : 'bg-blue-300' }} border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                    placeholder="Masukkan suhu..." {{ !empty($check->checker_shift1_id) ? '' : 'disabled' }}>
            </div>
            <div>
                <label class="block mb-2">Temperatur Shift 2:</label>
                <input type="text" id="temp-shift-2" x-ref="tempShift2" name="temperatur_shift2" value="{{ $check->temperatur_shift2 }}" 
                    class="w-full px-3 py-2 {{ !empty($check->checker_shift2_id) ? 'bg-white' : 'bg-blue-300' }} border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                    placeholder="Masukkan suhu..." {{ !empty($check->checker_shift2_id) ? '' : 'disabled' }}>
            </div>
            <div>
                <label class="block mb-2">Humidity Shift 1:</label>
                <input type="text" id="humidity-shift-1" x-ref="humidityShift1" name="humidity_shift1" value="{{ $check->humidity_shift1 }}" 
                    class="w-full px-3 py-2 {{ !empty($check->checker_shift1_id) ? 'bg-white' : 'bg-blue-300' }} border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                    placeholder="Masukkan kelembapan..." {{ !empty($check->checker_shift1_id) ? '' : 'disabled' }}>
            </div>
            <div>
                <label class="block mb-2">Humidity Shift 2:</label>
                <input type="text" id="humidity-shift-2" x-ref="humidityShift2" name="humidity_shift2" value="{{ $check->humidity_shift2 }}" 
                    class="w-full px-3 py-2 {{ !empty($check->checker_shift2_id) ? 'bg-white' : 'bg-blue-300' }} border border-blue-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500" 
                    placeholder="Masukkan kelembapan..." {{ !empty($check->checker_shift2_id) ? '' : 'disabled' }}>
            </div>
        </div>

        <!-- Low Compressor Table -->
        <div class="overflow-x-auto mb-4">
            <!-- Notifikasi scroll horizontal untuk mobile -->
            <div class="md:hidden text-sm text-gray-500 italic mb-2">
                ← Geser ke kanan untuk melihat semua kolom →
            </div>
            <h3 class="text-lg font-semibold mb-2">Form Pengisian Low Compressor</h3>
            <div class="table-container max-h-500 overflow-y-auto">
                <!-- Lebar minimum pada mobile agar bisa scroll horizontal -->
                <div class="min-w-[1200px]">
                    <table class="w-full border border-gray-300 bg-white rounded-lg">
                        <thead class="bg-gray-200 text-center sticky top-0 z-10">
                            <tr>
                                <th class="border bg-sky-50 border-gray-300 p-2" rowspan="3">No.</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" rowspan="3">Checked Items</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="12">Hasil Pemeriksaan</th>
                            </tr>
                            <tr>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KL 10</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KL 5</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KL 6</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KL 7</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KL 8</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KL 9</th>
                            </tr>
                            <tr>
                                @for ($i = 0; $i < 6; $i++)
                                    <th class="border bg-sky-50 border-gray-300 p-2">I</th>
                                    <th class="border bg-sky-50 border-gray-300 p-2">II</th>
                                @endfor
                            </tr>
                        </thead>

                        <tbody id="table-body" class="text-sm text-center">
                            @php
                                $checkedItems = [
                                    "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                                    "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                                    "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                                    "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
                                ];
                            
                                // Indeks dengan dropdown, tapi memiliki opsi berbeda
                                $customOptions = [
                                    5  => ['Penuh', 'Ditambah', 'OFF', '-'],
                                    6  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    7  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    8  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    9  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    10 => ['Halus', 'Kasar', 'OFF', '-'],
                                    16 => ['Kencang', 'Kendor', 'OFF', '-'],
                                ];
                            
                                // Placeholder khusus untuk tiap indeks
                                $placeholders = [
                                    0  => "50°C - 75°C / OFF / -",
                                    1  => "60°C - 90°C / OFF / -",
                                    2  => "80°C - 105°C / OFF / -",
                                    3  => "30°C - 55°C / OFF / -",
                                    4  => "30°C - 50°C / OFF / -",
                                    11 => "- / OFF",
                                    12 => "- / OFF",
                                    13 => "30°C - 55°C / OFF / -",
                                    14 => "> 380V / OFF / -",
                                    15 => "- / OFF",
                                    17 => "- / OFF",
                                    18 => "- / OFF",
                                    19 => "80°C - 50°C / OFF / -",
                                ];
                            
                                // Sesuai dengan header, hanya ada 12 kolom KL
                                $klColumns = ['KL 10I', 'KL 10II', 'KL 5I', 'KL 5II', 'KL 6I', 'KL 6II', 'KL 7I', 'KL 7II', 'KL 8I', 'KL 8II', 'KL 9I', 'KL 9II'];
                                $klDbColumns = ['kl_10I', 'kl_10II', 'kl_5I', 'kl_5II', 'kl_6I', 'kl_6II', 'kl_7I', 'kl_7II', 'kl_8I', 'kl_8II', 'kl_9I', 'kl_9II'];
                            @endphp

                            @foreach ($checkedItems as $index => $item)
                                @php
                                    $result = $lowResults->where('checked_items', $item)->first();
                                @endphp
                                <tr class="hover:bg-sky-50">
                                    <td class="border border-gray-300 p-2">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 p-2 w-1">{{ $item }}</td>

                                    <!-- Tampilkan data dari database jika ada -->
                                    @foreach ($klColumns as $i => $kl)
                                        <td class="border border-gray-300 p-2 w-auto">
                                            @if (isset($customOptions[$index]))
                                                <select name="kl_{{ $kl }}[]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                    @foreach ($customOptions[$index] as $option)
                                                        <option value="{{ $option }}" {{ $result && $result->{$klDbColumns[$i]} == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" name="kl_{{ $kl }}[]" 
                                                    value="{{ $result ? $result->{$klDbColumns[$i]} : '' }}" 
                                                    class="w-full border border-gray-300 p-1 rounded text-center" 
                                                    placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai / OFF / -' }}"
                                                    list="input-options-kl">
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- High Compressor Table -->
        <div class="overflow-x-auto mb-4">
            <!-- Notifikasi scroll horizontal untuk mobile -->
            <div class="md:hidden text-sm text-gray-500 italic mb-2">
                ← Geser ke kanan untuk melihat semua kolom →
            </div>
            <h3 class="text-lg font-semibold mb-2">Form Pengisian High Compressor</h3>
            <div class="table-container max-h-500 overflow-y-auto">
                <!-- Lebar minimum pada mobile agar bisa scroll horizontal -->
                <div class="min-w-[1200px]">
                    <table class="w-full border border-gray-300 bg-white rounded-lg">
                        <thead class="bg-gray-200 text-center sticky top-0 z-10">
                            <tr>
                                <th class="border bg-sky-50 border-gray-300 p-2" rowspan="3">No.</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" rowspan="3">Checked Items</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="10">Hasil Pemeriksaan</th>
                            </tr>
                            <tr>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KH 7</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KH 8</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KH 9</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KH 10</th>
                                <th class="border bg-sky-50 border-gray-300 p-2" colspan="2">KH 11</th>
                            </tr>
                            <tr>
                                @for ($i = 0; $i < 5; $i++)
                                    <th class="border bg-sky-50 border-gray-300 p-2">I</th>
                                    <th class="border bg-sky-50 border-gray-300 p-2">II</th>
                                @endfor
                            </tr>
                        </thead>

                        <tbody id="table-body" class="text-sm text-center">
                            @php
                                $checkedItems = [
                                    "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                                    "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                                    "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                                    "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
                                ];
                            
                                // Indeks dengan dropdown, tapi memiliki opsi berbeda
                                $customOptions = [
                                    5  => ['Penuh', 'Ditambah', 'OFF', '-'],
                                    6  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    7  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    8  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    9  => ['Bersih', 'Kotor', 'OFF', '-'],
                                    10 => ['Halus', 'Kasar', 'OFF', '-'],
                                    16 => ['Kencang', 'Kendor', 'OFF', '-'],
                                ];
                            
                                // Placeholder khusus untuk tiap indeks
                                $placeholders = [
                                    0  => "50°C - 70°C / OFF / -",
                                    1  => "80°C - 105°C / OFF / -",
                                    2  => "80°C - 100°C / OFF / -",
                                    3  => "30°C - 55°C / OFF / -",
                                    4  => "30°C - 50°C / OFF / -",
                                    11 => "- / OFF",
                                    12 => "- / OFF",
                                    13 => "30°C - 55°C / OFF / -",
                                    14 => "> 380V / OFF / -",
                                    15 => "- / OFF",
                                    17 => "- / OFF",
                                    18 => "- / OFF",
                                    19 => "8Bar - 9Bar / OFF / -",
                                    20 => "22Bar - 30Bar / OFF / -",
                                ];
                            
                                // Sesuai dengan header, hanya ada 10 kolom KH
                                $khColumns = ['KH 7I', 'KH 7II', 'KH 8I', 'KH 8II', 'KH 9I', 'KH 9II', 'KH 10I', 'KH 10II', 'KH 11I', 'KH 11II'];
                                $khDbColumns = ['kh_7I', 'kh_7II', 'kh_8I', 'kh_8II', 'kh_9I', 'kh_9II', 'kh_10I', 'kh_10II', 'kh_11I', 'kh_11II'];
                            @endphp

                            @foreach ($checkedItems as $index => $item)
                                @php
                                    $result = $highResults->where('checked_items', $item)->first();
                                @endphp
                                <tr class="hover:bg-sky-50">
                                    <td class="border border-gray-300 p-2">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 p-2 w-1">{{ $item }}</td>

                                    <!-- Tampilkan data dari database jika ada -->
                                    @foreach ($khColumns as $i => $kh)
                                        <td class="border border-gray-300 p-2 w-auto">
                                            @if (isset($customOptions[$index]))
                                                <select name="kh_{{ $kh }}[]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                    @foreach ($customOptions[$index] as $option)
                                                        <option value="{{ $option }}" {{ $result && $result->{$khDbColumns[$i]} == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" name="kh_{{ $kh }}[]" 
                                                    value="{{ $result ? $result->{$khDbColumns[$i]} : '' }}" 
                                                    class="w-full border border-gray-300 p-1 rounded text-center" 
                                                    placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai / OFF / -' }}"
                                                    list="input-options-kh">
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informasi Mesin dan Kriteria -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h5 class="text-lg font-semibold text-blue-700 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informasi Standar Pemeriksaan
            </h5>

            <div class="grid grid-cols-1 gap-4">
                <!-- Compressor Low -->
                <div class="bg-white p-4 rounded-lg border border-blue-200">
                    <h6 class="font-medium text-blue-600 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Standar Kriteria Pemeriksaan Low Compressor:
                    </h6>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2 text-gray-700 text-sm">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Motor:</strong> 50°C - 75°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Screw:</strong> 60°C - 90°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Oil:</strong> 80°C - 105°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Outlet:</strong> 30°C - 55°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur MCB:</strong> 30°C - 50°C</span>
                            </div>
                        </div>
                        <div class="space-y-2 text-gray-700 text-sm">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Oil Compressor:</strong> Penuh/Ditambah</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Filter (Air/Oil/Separator):</strong> Bersih/Kotor</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Suara Mesin:</strong> Halus/Kasar</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Kabel:</strong> 30°C - 55°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Voltage:</strong> > 380V</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur ADT:</strong> 80°C - 50°C</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Compressor High -->
                <div class="bg-white p-4 rounded-lg border border-blue-200">
                    <h6 class="font-medium text-blue-600 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Standar Kriteria Pemeriksaan High Compressor:
                    </h6>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2 text-gray-700 text-sm">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Motor:</strong> 50°C - 70°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Piston:</strong> 80°C - 105°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Oil:</strong> 80°C - 100°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Outlet:</strong> 30°C - 55°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur MCB:</strong> 30°C - 50°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Temperatur Kabel:</strong> 30°C - 55°C</span>
                            </div>
                        </div>
                        <div class="space-y-2 text-gray-700 text-sm">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Oil Compressor:</strong> Penuh/Ditambah</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Filter (Air/Oil/Separator):</strong> Bersih/Kotor</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Suara Mesin:</strong> Halus/Kasar</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Voltage:</strong> > 380V</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Inlet Pressure:</strong> 8Bar - 9Bar</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span><strong>Outlet Pressure:</strong> 22Bar - 30Bar</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kode Mesin dan Keterangan Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Detail Mesin -->
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h6 class="font-medium text-blue-600 mb-3">Kode Mesin:</h6>
                        <div class="grid grid-cols-2 gap-2 text-sm text-gray-800">
                            <div>
                                <p><strong>KL 5:</strong> Low Compressor 5</p>
                                <p><strong>KL 6:</strong> Low Compressor 6</p>
                                <p><strong>KL 7:</strong> Low Compressor 7</p>
                                <p><strong>KL 8:</strong> Low Compressor 8</p>
                                <p><strong>KL 9:</strong> Low Compressor 9</p>
                                <p><strong>KL 10:</strong> Low Compressor 10</p>
                            </div>
                            <div>
                                <p><strong>KH 7:</strong> High Compressor 7</p>
                                <p><strong>KH 8:</strong> High Compressor 8</p>
                                <p><strong>KH 9:</strong> High Compressor 9</p>
                                <p><strong>KH 10:</strong> High Compressor 10</p>
                                <p><strong>KH 11:</strong> High Compressor 11</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Keterangan Status -->
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h6 class="font-medium text-blue-600 mb-3">Keterangan Status:</h6>
                        <div class="grid grid-cols-1 gap-2 text-sm text-gray-800">
                            <div class="flex items-center">
                                <span class="inline-block w-6 h-6 border border-gray-300 text-gray-500 text-center font-bold mr-3 rounded">-</span>
                                <span>Tidak Diisi</span>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-block w-6 h-6 border border-gray-300 text-gray-500 text-center font-bold mr-3 rounded">OFF</span>
                                <span>Mesin Mati</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('components.edit-form-buttons', ['backRoute' => route('compressor.index')])
    </form>
@endsection

@section('scripts')
<script>

</script>
@endsection