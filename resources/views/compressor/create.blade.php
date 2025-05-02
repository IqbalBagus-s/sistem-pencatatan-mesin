<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pencatatan Compressor</title>

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
    <!-- Notification Popup -->
    <div id="error-notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 hidden">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md flex items-center max-w-md">
            <div class="mr-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-6 text-sm" id="error-message">There was a problem sending your mail. Please try again.</p>
            <button type="button" id="close-notification" class="ml-auto">
                <svg class="w-4 h-4 text-red-500 hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="container mx-auto mt-4 px-4">
        <h2 class="mb-4 text-xl font-bold">Form Pencatatan Compressor</h2>

        <div class="bg-white rounded-lg shadow-md mb-5">
            <div class="p-4">
                <!-- Menampilkan Nama Checker -->
                <div class="bg-sky-50 p-4 rounded-md mb-5">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
                </div>

                <!-- Form Input -->
                <form action="{{ route('compressor.store') }}" method="POST" id="compressor-form" autocomplete="off">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Hari:</label>
                            <input type="text" id="hari" name="hari" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                        </div>
                        <div>
                            <label class="block mb-2">Tanggal:</label>
                            <input type="date" id="tanggal" name="tanggal" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" required>
                        </div>
                    </div>

                    <!-- Form tambahan -->
                    <div x-data="{
                            shiftSelected: null,
                            username: '{{ Auth::user()->username }}',
                            
                            selectShift(shift) {
                                if (this.shiftSelected === shift) {
                                    // If the same shift is clicked again, cancel the selection
                                    this.cancelSelection();
                                } else {
                                    // Select the shift
                                    this.shiftSelected = shift;
                                    
                                    if (shift === 1) {
                                        this.$refs.shift1.value = this.username;
                                        this.$refs.shift2.value = '';
                                        
                                        this.$refs.tempShift1.disabled = false;
                                        this.$refs.tempShift1.classList.remove('bg-gray-300');
                                        this.$refs.tempShift1.classList.add('bg-gray-100');
                                        this.$refs.humidityShift1.disabled = false;
                                        this.$refs.humidityShift1.classList.remove('bg-gray-300');
                                        this.$refs.humidityShift1.classList.add('bg-gray-100');
                                        
                                        this.$refs.tempShift2.disabled = true;
                                        this.$refs.tempShift2.classList.remove('bg-gray-100');
                                        this.$refs.tempShift2.classList.add('bg-gray-300');
                                        this.$refs.humidityShift2.disabled = true;
                                        this.$refs.humidityShift2.classList.remove('bg-gray-100');
                                        this.$refs.humidityShift2.classList.add('bg-gray-300');
                                    } else if (shift === 2) {
                                        this.$refs.shift2.value = this.username;
                                        this.$refs.shift1.value = '';
                                        
                                        this.$refs.tempShift2.disabled = false;
                                        this.$refs.tempShift2.classList.remove('bg-gray-300');
                                        this.$refs.tempShift2.classList.add('bg-gray-100');
                                        this.$refs.humidityShift2.disabled = false;
                                        this.$refs.humidityShift2.classList.remove('bg-gray-300');
                                        this.$refs.humidityShift2.classList.add('bg-gray-100');
                                        
                                        this.$refs.tempShift1.disabled = true;
                                        this.$refs.tempShift1.classList.remove('bg-gray-100');
                                        this.$refs.tempShift1.classList.add('bg-gray-300');
                                        this.$refs.humidityShift1.disabled = true;
                                        this.$refs.humidityShift1.classList.remove('bg-gray-100');
                                        this.$refs.humidityShift1.classList.add('bg-gray-300');
                                    }
                                }
                            },
                            
                            cancelSelection() {
                                // Reset the shift selection
                                this.shiftSelected = null;
                                
                                // Clear the input fields
                                this.$refs.shift1.value = '';
                                this.$refs.shift2.value = '';
                                
                                // Disable all input fields
                                this.$refs.tempShift1.disabled = true;
                                this.$refs.tempShift1.classList.remove('bg-gray-100');
                                this.$refs.tempShift1.classList.add('bg-gray-300');
                                this.$refs.humidityShift1.disabled = true;
                                this.$refs.humidityShift1.classList.remove('bg-gray-100');
                                this.$refs.humidityShift1.classList.add('bg-gray-300');
                                
                                this.$refs.tempShift2.disabled = true;
                                this.$refs.tempShift2.classList.remove('bg-gray-100');
                                this.$refs.tempShift2.classList.add('bg-gray-300');
                                this.$refs.humidityShift2.disabled = true;
                                this.$refs.humidityShift2.classList.remove('bg-gray-100');
                                this.$refs.humidityShift2.classList.add('bg-gray-300');
                            }
                        }">
                    <!-- Pilih Shift Checker -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Shift 1:</label>
                            <input type="text" id="shift1" name="checked_by_shift1" x-ref="shift1" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                            <button type="button" @click="selectShift(1)" class="mt-2 w-full text-white py-2 rounded cursor-pointer" 
                                :class="shiftSelected === 1 ? 'bg-red-600' : 'bg-blue-600'"
                                x-text="shiftSelected === 1 ? 'Batal' : 'Pilih'">
                                Pilih
                            </button>
                        </div>
                        <div>
                            <label class="block mb-2">Shift 2:</label>
                            <input type="text" id="shift2" name="checked_by_shift2" x-ref="shift2" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                            <button type="button" @click="selectShift(2)" class="mt-2 w-full text-white py-2 rounded cursor-pointer"
                                :class="shiftSelected === 2 ? 'bg-red-600' : 'bg-blue-600'"
                                x-text="shiftSelected === 2 ? 'Batal' : 'Pilih'">
                                Pilih
                            </button>
                        </div>
                    </div>

                    <!-- Independent menu items that don't need Alpine.js functionality -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Jumlah Kompresor ON KL:</label>
                            <input type="text" name="kompressor_on_kl" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block mb-2">Jumlah Kompresor ON KH:</label>
                            <input type="text" name="kompressor_on_kh" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <!-- Mesin ON/OFF -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Mesin ON:</label>
                            <input type="text" name="mesin_on" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block mb-2">Mesin OFF:</label>
                            <input type="text" name="mesin_off" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                        </div>
                    </div>

                    <!-- Kelembapan Udara -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Temperatur Shift 1:</label>
                            <input type="text" id="temp-shift-1" x-ref="tempShift1" name="temperatur_shift1" class="w-full px-3 py-2 bg-gray-300 border border-gray-300 rounded-md" placeholder="Masukkan suhu..." disabled>
                        </div>
                        <div>
                            <label class="block mb-2">Temperatur Shift 2:</label>
                            <input type="text" id="temp-shift-2" x-ref="tempShift2" name="temperatur_shift2" class="w-full px-3 py-2 bg-gray-300 border border-gray-300 rounded-md" placeholder="Masukkan suhu..." disabled>
                        </div>
                        <div>
                            <label class="block mb-2">Humidity Shift 1:</label>
                            <input type="text" id="humidity-shift-1" x-ref="humidityShift1" name="humidity_shift1" class="w-full px-3 py-2 bg-gray-300 border border-gray-300 rounded-md" placeholder="Masukkan kelembapan..." disabled>
                        </div>
                        <div>
                            <label class="block mb-2">Humidity Shift 2:</label>
                            <input type="text" id="humidity-shift-2" x-ref="humidityShift2" name="humidity_shift2" class="w-full px-3 py-2 bg-gray-300 border border-gray-300 rounded-md" placeholder="Masukkan kelembapan..." disabled>
                        </div>
                    </div>
                    </div>

                    <!-- Low Kompressor Table -->
                    <div class="overflow-x-auto mb-4">
                        <!-- Notifikasi scroll horizontal untuk mobile -->
                        <div class="md:hidden text-sm text-gray-500 italic mb-2">
                            ← Geser ke kanan untuk melihat semua kolom →
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Form Pengisian Low Kompressor</h3>
                        <div class="table-container">
                            <!-- Lebar minimum pada mobile agar bisa scroll horizontal -->
                            <div class="min-w-[1200px]">
                                <table class="w-full border border-gray-300 bg-white rounded-lg">
                                    <thead class="bg-gray-200 text-center">
                                        <tr>
                                            <th class="border border-gray-300 p-2 w-10" rowspan="3">No.</th>
                                            <th class="border border-gray-300 p-2 min-w-32 w-36" rowspan="3">Checked Items</th>
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
                                                <th class="border border-gray-300 p-2 w-16">I</th>
                                                <th class="border border-gray-300 p-2 w-16">II</th>
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
                                        @endphp

                                        @foreach ($checkedItems as $index => $item)
                                            <tr class="hover:bg-gray-100">
                                                <td class="border border-gray-300 p-2 w-10">{{ $index + 1 }}</td>
                                                <td class="border border-gray-300 p-2 min-w-32 w-36 text-left">{{ $item }}</td>

                                                <!-- Ganti format nama input di tabel Low Kompressor -->
                                                @foreach ($klColumns as $kl)
                                                <td class="border border-gray-300 p-2 w-16">
                                                    @if (isset($customOptions[$index]))
                                                        <select name="kl_{{ str_replace(' ', '_', $kl) }}[]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                            @foreach ($customOptions[$index] as $option)
                                                                <option value="{{ $option }}">{{ $option }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" name="kl_{{ str_replace(' ', '_', $kl) }}[]" class="w-full border border-gray-300 p-1 rounded text-center" placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}">
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

                    <!-- High Kompressor Table -->
                    <div class="text-lg font-semibold mb-4 mt-4">
                        Form Pengisian High Kompressor
                    </div>

                    <div class="overflow-x-auto">
                        <!-- Notifikasi scroll horizontal untuk mobile -->
                        <div class="md:hidden text-sm text-gray-500 italic mb-2">
                            ← Geser ke kanan untuk melihat semua kolom →
                        </div>
                        <div class="table-container">
                            <!-- Lebar minimum pada mobile agar bisa scroll horizontal -->
                            <div class="min-w-[1200px]">
                                <table class="w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
                                    <thead class="bg-gray-200 text-center">
                                        <tr>
                                            <th class="border border-gray-300 p-2 w-10" rowspan="3">No.</th>
                                            <th class="border border-gray-300 p-2 min-w-32 w-36" rowspan="3">Checked Items</th>
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
                                                <th class="border border-gray-300 p-2 w-16">I</th>
                                                <th class="border border-gray-300 p-2 w-16">II</th>
                                            @endfor
                                        </tr>
                                    </thead>
                            
                                    <tbody id="table-body-high" class="text-sm text-center">
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
                                        
                                            // Sesuai dengan header, hanya ada 10 kolom KH
                                            $khColumns = ['KH 7I', 'KH 7II', 'KH 8I', 'KH 8II', 'KH 9I', 'KH 9II', 'KH 10I', 'KH 10II', 'KH 11I', 'KH 11II'];
                                        @endphp
                            
                                        @foreach ($checkedItems as $index => $item)
                                            <tr class="hover:bg-gray-100">
                                                <td class="border border-gray-300 p-2 w-10">{{ $index + 1 }}</td>
                                                <td class="border border-gray-300 p-2 min-w-32 w-36 text-left">{{ $item }}</td>
                            
                                                <!-- Ganti format nama input di tabel High Kompressor -->
                                                @foreach ($khColumns as $kh)
                                                <td class="border border-gray-300 p-2 w-16">
                                                    @if (isset($customOptions[$index]))
                                                        <select name="kh_{{ str_replace(' ', '_', $kh) }}[]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                            @foreach ($customOptions[$index] as $option)
                                                                <option value="{{ $option }}">{{ $option }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="text" name="kh_{{ str_replace(' ', '_', $kh) }}[]" class="w-full border border-gray-400 p-1 rounded text-center" placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}">
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

                    @include('components.create-form-buttons', ['backRoute' => route('compressor.index')])
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing code for date handling - only run if tanggal element exists
            const tanggalEl = document.getElementById("tanggal");
            const hariEl = document.getElementById("hari");
            
            if (tanggalEl && hariEl) {
                tanggalEl.addEventListener("change", function() {
                    let tanggal = new Date(this.value);
                    let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
                    hariEl.value = hari;
                });
            }
            
            // Error notification handling
            const errorNotification = document.getElementById('error-notification');
            const closeNotification = document.getElementById('close-notification');
            const errorMessage = document.getElementById('error-message');
            
            // Check if there's an error or warning message from the session
            const sessionError = "{{ session('error') }}";
            const sessionWarning = "{{ session('warning') }}";
            
            if (sessionError && sessionError.trim() !== '') {
                // Update the error message text
                errorMessage.textContent = sessionError;
                
                // Show the notification
                errorNotification.classList.remove('hidden');
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    errorNotification.classList.add('hidden');
                }, 5000);
            } else if (sessionWarning && sessionWarning.trim() !== '') {
                // Update the error message text
                errorMessage.textContent = sessionWarning;
                
                // Show the notification
                errorNotification.classList.remove('hidden');
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    errorNotification.classList.add('hidden');
                }, 5000);
            }
            
            // Close button functionality
            if (closeNotification) {
                closeNotification.addEventListener('click', function() {
                    errorNotification.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>