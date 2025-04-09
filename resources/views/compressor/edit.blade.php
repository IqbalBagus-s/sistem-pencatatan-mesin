<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Form Pencatatan Compressor</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    <style>
        /* Notification Popup Styles */
        #notification-popup {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            padding: 15px 30px;
            border-radius: 8px;
            color: white;
            display: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: opacity 0.3s ease;
        }
        .notification-success {
            background-color: #28a745;
        }
        .notification-warning {
            background-color: #dc3545;
            color: white;
        }

        /* Sticky Table Header Styles */
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
</head>
<body class="bg-sky-50 font-sans">
    <!-- Notification Popup -->
    <div id="notification-popup" class="notification-success">
        <span id="notification-message"></span>
    </div>

    <div class="container mx-auto mt-4 px-4">
        <h2 class="mb-4 text-xl font-bold">Edit Form Pencatatan Compressor</h2>

        <div class="bg-white rounded-lg shadow-md mb-5">
            <div class="p-4">
                <!-- Form Input -->
                <form action="{{ route('compressor.update', $check->id) }}" method="POST" id="compressor-form">
                    @csrf
                    @method('PUT')
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Hari:</label>
                            <input type="text" id="hari" name="hari" value="{{ $check->hari }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                        </div>
                        <div>
                            <label class="block mb-2">Tanggal:</label>
                            <input type="date" id="tanggal" name="tanggal" value="{{ $check->tanggal }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" required>
                        </div>
                    </div>

                    <!-- Form tambahan -->
                    <div x-data="{
                        shift1Selected: {{ !empty($check->checked_by_shift1) ? 'true' : 'false' }},
                        shift2Selected: {{ !empty($check->checked_by_shift2) ? 'true' : 'false' }},
                        username: '{{ Auth::user()->username }}',
                        
                        toggleShift(shift) {
                            if (shift === 1) {
                                this.shift1Selected = !this.shift1Selected;
                                
                                if (this.shift1Selected) {
                                    this.$refs.shift1.value = this.username;
                                    
                                    // Enable Shift 1 input fields
                                    this.$refs.tempShift1.disabled = false;
                                    this.$refs.tempShift1.classList.remove('bg-gray-300');
                                    this.$refs.tempShift1.classList.add('bg-gray-100');
                                    this.$refs.humidityShift1.disabled = false;
                                    this.$refs.humidityShift1.classList.remove('bg-gray-300');
                                    this.$refs.humidityShift1.classList.add('bg-gray-100');
                                } else {
                                    this.$refs.shift1.value = '';
                                    
                                    // Disable Shift 1 input fields
                                    this.$refs.tempShift1.disabled = true;
                                    this.$refs.tempShift1.classList.remove('bg-gray-100');
                                    this.$refs.tempShift1.classList.add('bg-gray-300');
                                    this.$refs.humidityShift1.disabled = true;
                                    this.$refs.humidityShift1.classList.remove('bg-gray-100');
                                    this.$refs.humidityShift1.classList.add('bg-gray-300');
                                }
                            } else if (shift === 2) {
                                this.shift2Selected = !this.shift2Selected;
                                
                                if (this.shift2Selected) {
                                    this.$refs.shift2.value = this.username;
                                    
                                    // Enable Shift 2 input fields
                                    this.$refs.tempShift2.disabled = false;
                                    this.$refs.tempShift2.classList.remove('bg-gray-300');
                                    this.$refs.tempShift2.classList.add('bg-gray-100');
                                    this.$refs.humidityShift2.disabled = false;
                                    this.$refs.humidityShift2.classList.remove('bg-gray-300');
                                    this.$refs.humidityShift2.classList.add('bg-gray-100');
                                } else {
                                    this.$refs.shift2.value = '';
                                    
                                    // Disable Shift 2 input fields
                                    this.$refs.tempShift2.disabled = true;
                                    this.$refs.tempShift2.classList.remove('bg-gray-100');
                                    this.$refs.tempShift2.classList.add('bg-gray-300');
                                    this.$refs.humidityShift2.disabled = true;
                                    this.$refs.humidityShift2.classList.remove('bg-gray-100');
                                    this.$refs.humidityShift2.classList.add('bg-gray-300');
                                }
                            }
                        },
                        
                        // Initialize the form with existing data
                        initForm() {
                            // If data exists for shift 1
                            if ('{{ $check->checked_by_shift1 }}') {
                                this.shift1Selected = true;
                                this.$refs.shift1.value = '{{ $check->checked_by_shift1 }}';
                                this.$refs.tempShift1.disabled = false;
                                this.$refs.tempShift1.classList.remove('bg-gray-300');
                                this.$refs.tempShift1.classList.add('bg-gray-100');
                                this.$refs.humidityShift1.disabled = false;
                                this.$refs.humidityShift1.classList.remove('bg-gray-300');
                                this.$refs.humidityShift1.classList.add('bg-gray-100');
                            }
                            
                            // If data exists for shift 2
                            if ('{{ $check->checked_by_shift2 }}') {
                                this.shift2Selected = true;
                                this.$refs.shift2.value = '{{ $check->checked_by_shift2 }}';
                                this.$refs.tempShift2.disabled = false;
                                this.$refs.tempShift2.classList.remove('bg-gray-300');
                                this.$refs.tempShift2.classList.add('bg-gray-100');
                                this.$refs.humidityShift2.disabled = false;
                                this.$refs.humidityShift2.classList.remove('bg-gray-300');
                                this.$refs.humidityShift2.classList.add('bg-gray-100');
                            }
                        }
                    }" x-init="initForm()">
                    <!-- Pilih Shift Checker -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Shift 1:</label>
                            <input type="text" id="shift1" name="checked_by_shift1" x-ref="shift1" value="{{ $check->checked_by_shift1 }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                            <button type="button" @click="toggleShift(1)" class="mt-2 w-full text-white py-2 rounded cursor-pointer" 
                                :class="shift1Selected ? 'bg-red-600' : 'bg-blue-600'"
                                x-text="shift1Selected ? 'Batal' : 'Pilih'">
                                Pilih
                            </button>
                        </div>
                    <div>
                        <label class="block mb-2">Shift 2:</label>
                        <input type="text" id="shift2" name="checked_by_shift2" x-ref="shift2" value="{{ $check->checked_by_shift2 }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
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
                        <label class="block mb-2">Jumlah Kompresor ON KL:</label>
                        <input type="text" name="kompressor_on_kl" value="{{ $check->kompressor_on_kl }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block mb-2">Jumlah Kompresor ON KH:</label>
                        <input type="text" name="kompressor_on_kh" value="{{ $check->kompressor_on_kh }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    </div>
                    </div>

                    <!-- Mesin ON/OFF -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2">Mesin ON:</label>
                        <input type="text" name="mesin_on" value="{{ $check->mesin_on }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    </div>
                    <div>
                        <label class="block mb-2">Mesin OFF:</label>
                        <input type="text" name="mesin_off" value="{{ $check->mesin_off }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    </div>
                    </div>

                    <!-- Kelembapan Udara -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2">Temperatur Shift 1:</label>
                        <input type="text" id="temp-shift-1" x-ref="tempShift1" name="temperatur_shift1" value="{{ $check->temperatur_shift1 }}" 
                            class="w-full px-3 py-2 {{ !empty($check->checked_by_shift1) ? 'bg-gray-100' : 'bg-gray-300' }} border border-gray-300 rounded-md" 
                            placeholder="Masukkan suhu..." {{ !empty($check->checked_by_shift1) ? '' : 'disabled' }}>
                    </div>
                    <div>
                        <label class="block mb-2">Temperatur Shift 2:</label>
                        <input type="text" id="temp-shift-2" x-ref="tempShift2" name="temperatur_shift2" value="{{ $check->temperatur_shift2 }}" 
                            class="w-full px-3 py-2 {{ !empty($check->checked_by_shift2) ? 'bg-gray-100' : 'bg-gray-300' }} border border-gray-300 rounded-md" 
                            placeholder="Masukkan suhu..." {{ !empty($check->checked_by_shift2) ? '' : 'disabled' }}>
                    </div>
                    <div>
                        <label class="block mb-2">Humidity Shift 1:</label>
                        <input type="text" id="humidity-shift-1" x-ref="humidityShift1" name="humidity_shift1" value="{{ $check->humidity_shift1 }}" 
                            class="w-full px-3 py-2 {{ !empty($check->checked_by_shift1) ? 'bg-gray-100' : 'bg-gray-300' }} border border-gray-300 rounded-md" 
                            placeholder="Masukkan kelembapan..." {{ !empty($check->checked_by_shift1) ? '' : 'disabled' }}>
                    </div>
                    <div>
                        <label class="block mb-2">Humidity Shift 2:</label>
                        <input type="text" id="humidity-shift-2" x-ref="humidityShift2" name="humidity_shift2" value="{{ $check->humidity_shift2 }}" 
                            class="w-full px-3 py-2 {{ !empty($check->checked_by_shift2) ? 'bg-gray-100' : 'bg-gray-300' }} border border-gray-300 rounded-md" 
                            placeholder="Masukkan kelembapan..." {{ !empty($check->checked_by_shift2) ? '' : 'disabled' }}>
                    </div>
                    </div>
                    </div>

                    <!-- Low Kompressor Table -->
                    <div class="overflow-x-auto mb-4">
                        <h3 class="text-lg font-semibold mb-2">Form Pengisian Low Kompressor</h3>
                        <div class="table-container">
                            <table class="w-full border border-gray-300 bg-white rounded-lg">
                                <thead class="bg-gray-200 text-center">
                                    <tr>
                                        <th class="border border-gray-300 p-2" rowspan="3">No.</th>
                                        <th class="border border-gray-300 p-2" rowspan="3">Checked Items</th>
                                        <th class="border border-gray-300 p-2" colspan="12">Hasil Pemeriksaan</th>
                                    </tr>
                                    <tr>
                                        <th class="border border-gray-300 p-2" colspan="2">KL 10</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KL 5</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KL 6</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KL 7</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KL 8</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KL 9</th>
                                    </tr>
                                    <tr>
                                        @for ($i = 0; $i < 6; $i++)
                                            <th class="border border-gray-300 p-2">I</th>
                                            <th class="border border-gray-300 p-2">II</th>
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
                                            5  => ['Penuh', 'Ditambah'],
                                            6  => ['Bersih', 'Kotor'],
                                            7  => ['Bersih', 'Kotor'],
                                            8  => ['Bersih', 'Kotor'],
                                            9  => ['Bersih', 'Kotor'],
                                            10 => ['Halus', 'Kasar'],
                                            16 => ['Kencang', 'Kendor'],
                                        ];
                                    
                                        // Placeholder khusus untuk tiap indeks
                                        $placeholders = [
                                            0  => "50°C - 75°C",
                                            1  => "60°C - 90°C",
                                            2  => "80°C - 105°C",
                                            3  => "30°C - 55°C",
                                            4  => "30°C - 50°C",
                                            11 => "-",
                                            12 => "-",
                                            13 => "30°C - 55°C",
                                            14 => "> 380V",
                                            15 => "-",
                                            17 => "-",
                                            18 => "-",
                                            19 => "80°C - 50°C",
                                        ];
                                    
                                        // Sesuai dengan header, hanya ada 12 kolom KL
                                        $klColumns = ['KL 10I', 'KL 10II', 'KL 5I', 'KL 5II', 'KL 6I', 'KL 6II', 'KL 7I', 'KL 7II', 'KL 8I', 'KL 8II', 'KL 9I', 'KL 9II'];
                                        $klDbColumns = ['kl_10I', 'kl_10II', 'kl_5I', 'kl_5II', 'kl_6I', 'kl_6II', 'kl_7I', 'kl_7II', 'kl_8I', 'kl_8II', 'kl_9I', 'kl_9II'];
                                    @endphp

                                    @foreach ($checkedItems as $index => $item)
                                        @php
                                            $result = $lowResults->where('checked_items', $item)->first();
                                        @endphp
                                        <tr class="hover:bg-gray-100">
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
                                                            placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- High Kompressor Table -->
                    <div class="overflow-x-auto mb-4">
                        <h3 class="text-lg font-semibold mb-2">Form Pengisian High Kompressor</h3>
                        <div class="table-container">
                            <table class="w-full border border-gray-300 bg-white rounded-lg">
                                <thead class="bg-gray-200 text-center">
                                    <tr>
                                        <th class="border border-gray-300 p-2" rowspan="3">No.</th>
                                        <th class="border border-gray-300 p-2" rowspan="3">Checked Items</th>
                                        <th class="border border-gray-300 p-2" colspan="10">Hasil Pemeriksaan</th>
                                    </tr>
                                    <tr>
                                        <th class="border border-gray-300 p-2" colspan="2">KH 7</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KH 8</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KH 9</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KH 10</th>
                                        <th class="border border-gray-300 p-2" colspan="2">KH 11</th>
                                    </tr>
                                    <tr>
                                        @for ($i = 0; $i < 5; $i++)
                                            <th class="border border-gray-300 p-2">I</th>
                                            <th class="border border-gray-300 p-2">II</th>
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
                                            5  => ['Penuh', 'Ditambah'],
                                            6  => ['Bersih', 'Kotor'],
                                            7  => ['Bersih', 'Kotor'],
                                            8  => ['Bersih', 'Kotor'],
                                            9  => ['Bersih', 'Kotor'],
                                            10 => ['Halus', 'Kasar'],
                                            16 => ['Kencang', 'Kendor'],
                                        ];
                                    
                                        // Placeholder khusus untuk tiap indeks
                                        $placeholders = [
                                            0  => "50°C - 70°C",
                                            1  => "80°C - 105°C",
                                            2  => "80°C - 100°C",
                                            3  => "30°C - 55°C",
                                            4  => "30°C - 50°C",
                                            11 => "-",
                                            12 => "-",
                                            13 => "30°C - 55°C",
                                            14 => "> 380V",
                                            15 => "-",
                                            17 => "-",
                                            18 => "-",
                                            19 => "8Bar - 9Bar",
                                            20 => "22Bar - 30Bar",
                                        ];
                                    
                                        // Sesuai dengan header, hanya ada 10 kolom KH (KH 12 dihapus)
                                        $khColumns = ['KH 7I', 'KH 7II', 'KH 8I', 'KH 8II', 'KH 9I', 'KH 9II', 'KH 10I', 'KH 10II', 'KH 11I', 'KH 11II'];
                                        $khDbColumns = ['kh_7I', 'kh_7II', 'kh_8I', 'kh_8II', 'kh_9I', 'kh_9II', 'kh_10I', 'kh_10II', 'kh_11I', 'kh_11II'];
                                    @endphp

                                    @foreach ($checkedItems as $index => $item)
                                        @php
                                            $result = $highResults->where('checked_items', $item)->first();
                                        @endphp
                                        <tr class="hover:bg-gray-100">
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
                                                            placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}">
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('compressor.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>