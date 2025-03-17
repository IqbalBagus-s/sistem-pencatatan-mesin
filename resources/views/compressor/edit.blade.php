<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Form Pencatatan Mesin Compressor</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Pencatatan Compressor</h2>
    
        <!-- Form Edit -->
        <form action="{{ route('compressor.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            {{-- Inputan Tanggal dan Hari --}}
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Hari:</label>
                    <input type="text" id="hari" name="hari" class="w-full p-2 border border-gray-300 rounded bg-gray-100" value="{{ $check->hari }}" readonly>
                </div>
                <div>
                    <label class="block text-gray-700">Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" class="w-full p-2 border border-gray-300 rounded" value="{{ $check->tanggal }}" required>
                </div>
            </div>
    
            <!-- Menampilkan Pilihan Shift -->
            <div class="mb-4 p-4 bg-gray-200 rounded">
                <p class="text-lg font-semibold text-gray-700">Pilih Shift Checker</p>
                
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <!-- Shift 1 -->
                    <div class="p-4 bg-white shadow rounded border border-gray-300">
                        <label class="block text-gray-700 font-semibold">Shift 1</label>
                        <input type="text" id="shift1" name="checked_by_shift1" class="mt-2 w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" value="{{ $check->checked_by_shift1 }}" readonly>
                        <button type="button" onclick="pilihShift(1)" class="mt-2 w-full bg-blue-600 text-white py-1 px-3 rounded hover:bg-blue-700 transition">Pilih</button>
                    </div>
    
                    <!-- Shift 2 -->
                    <div class="p-4 bg-white shadow rounded border border-gray-300">
                        <label class="block text-gray-700 font-semibold">Shift 2</label>
                        <input type="text" id="shift2" name="checked_by_shift2" class="mt-2 w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" value="{{ $check->checked_by_shift2 }}" readonly>
                        <button type="button" onclick="pilihShift(2)" class="mt-2 w-full bg-blue-600 text-white py-1 px-3 rounded hover:bg-blue-700 transition">Pilih</button>
                    </div>
                </div>
            </div>
    
            <!-- Kotak untuk Mengisi Kompresor ON -->
            <div class="mb-4 p-4 bg-gray-200 rounded shadow-lg">
                <p class="text-lg font-semibold text-gray-700 mb-2">Jumlah Kompresor ON</p>
    
                <div class="grid grid-cols-2 gap-4">
                    <!-- Input KL -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">KL</label>
                        <input type="text" name="kompressor_on_kl" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->kompressor_on_kl }}">
                    </div>
                    <!-- Input KH -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">KH</label>
                        <input type="text" name="kompressor_on_kh" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->kompressor_on_kh }}">
                    </div>
                </div>
    
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <!-- Input Mesin ON -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Mesin ON</label>
                        <input type="text" name="mesin_on" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->mesin_on }}">
                    </div>
                    <!-- Input Mesin OFF -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Mesin OFF</label>
                        <input type="text" name="mesin_off" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->mesin_off }}">
                    </div>
                </div>
            </div>
    
            <!-- Kotak Kelembapan Udara -->
            <div class="mb-4 p-4 bg-gray-200 rounded shadow-lg">
                <p class="text-lg font-semibold text-gray-700 mb-2">Kelembapan Udara</p>
    
                <div class="grid grid-cols-2 gap-4">
                    <!-- Input Temperatur Shift 1 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Temperatur Shift 1</label>
                        <input type="text" id="temp-shift-1" name="temperatur_shift1" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->temperatur_shift1 }}">
                    </div>
                    <!-- Input Temperatur Shift 2 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Temperatur Shift 2</label>
                        <input type="text" id="temp-shift-2" name="temperatur_shift2" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->temperatur_shift2 }}">
                    </div>
                    <!-- Input Humidity Shift 1 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Humidity Shift 1</label>
                        <input type="text" id="humidity-shift-1" name="humidity_shift1" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->humidity_shift1 }}">
                    </div>
                    <!-- Input Humidity Shift 2 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Humidity Shift 2</label>
                        <input type="text" id="humidity-shift-2" name="humidity_shift2" class="w-full border border-gray-400 p-2 rounded text-center" value="{{ $check->humidity_shift2 }}">
                    </div>
                </div>
            </div>
            
            <!-- Low Kompressor Table -->
            <div class="text-lg font-semibold mb-4 mt-4">
                Form Pengisian Low Kompressor
            </div>
    
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
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
                        
                            // Kolom KL untuk tabel
                            $klDbColumns = ['kl_10I', 'kl_10II', 'kl_5I', 'kl_5II', 'kl_6I', 'kl_6II', 'kl_7I', 'kl_7II', 'kl_8I', 'kl_8II', 'kl_9I', 'kl_9II'];
                            $klFormColumns = ['KL_10I', 'KL_10II', 'KL_5I', 'KL_5II', 'KL_6I', 'KL_6II', 'KL_7I', 'KL_7II', 'KL_8I', 'KL_8II', 'KL_9I', 'KL_9II'];
                        @endphp
    
                        @foreach ($lowResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                            @php 
                                $result = $resultGroup->first();
                                $index = array_search($result->checked_items, $checkedItems);
                            @endphp
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 p-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2 w-1">{{ $result->checked_items }}</td>
    
                                @foreach ($klDbColumns as $i => $klColumn)
                                    <td class="border border-gray-300 p-2 w-auto">
                                        @if (isset($customOptions[$index]))
                                            <select name="kl_{{ $klFormColumns[$i] }}[]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                @foreach ($customOptions[$index] as $option)
                                                    <option value="{{ $option }}" {{ $result->$klColumn == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" name="kl_{{ $klFormColumns[$i] }}[]" class="w-full border border-gray-300 p-1 rounded text-center" 
                                                   placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}" 
                                                   value="{{ $result->$klColumn }}">
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- High Kompressor Table -->
            <div class="text-lg font-semibold mb-4 mt-4">
                Form Pengisian High Kompressor
            </div>
    
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
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
                        
                            // Kolom KH untuk tabel
                            $khDbColumns = ['kh_7I', 'kh_7II', 'kh_8I', 'kh_8II', 'kh_9I', 'kh_9II', 'kh_10I', 'kh_10II', 'kh_11I', 'kh_11II'];
                            $khFormColumns = ['KH_7I', 'KH_7II', 'KH_8I', 'KH_8II', 'KH_9I', 'KH_9II', 'KH_10I', 'KH_10II', 'KH_11I', 'KH_11II'];
                        @endphp
                    
                        @foreach ($highResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                            @php 
                                $result = $resultGroup->first();
                                $index = array_search($result->checked_items, $checkedItems);
                            @endphp
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 p-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2 w-1/12">{{ $result->checked_items }}</td>
                    
                                @foreach ($khDbColumns as $i => $khColumn)
                                    <td class="border border-gray-300 p-2 w-auto">
                                        @if (isset($customOptions[$index]))
                                            <select name="kh_{{ $khFormColumns[$i] }}[]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                @foreach ($customOptions[$index] as $option)
                                                    <option value="{{ $option }}" {{ $result->$khColumn == $option ? 'selected' : '' }}>
                                                        {{ $option }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input type="text" name="kh_{{ $khFormColumns[$i] }}[]" class="w-full border border-gray-400 p-1 rounded text-center" 
                                                   placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}" 
                                                   value="{{ $result->$khColumn }}">
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                                </table>
                            </div>
                    
                            <div class="mt-4 flex justify-between">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Simpan Perubahan
                                </button>
                                <a href="{{ route('compressor.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                                    Kembali
                                </a>
                            </div>
                        </form>
                    </div>

    <!-- Script untuk mengisi hari berdasarkan tanggal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fungsi untuk mengatur status disable/enable field input berdasarkan shift yang sudah diisi
            function setupFieldsBasedOnShifts() {
                const shift1Value = document.getElementById('shift1').value;
                const shift2Value = document.getElementById('shift2').value;
                
                if (shift1Value && !shift2Value) {
                    // Shift 1 terisi, Shift 2 kosong
                    document.getElementById("temp-shift-1").disabled = false;
                    document.getElementById("temp-shift-1").classList.remove("bg-gray-300");
                    document.getElementById("humidity-shift-1").disabled = false;
                    document.getElementById("humidity-shift-1").classList.remove("bg-gray-300");
                    
                    document.getElementById("temp-shift-2").disabled = true;
                    document.getElementById("temp-shift-2").classList.add("bg-gray-300");
                    document.getElementById("humidity-shift-2").disabled = true;
                    document.getElementById("humidity-shift-2").classList.add("bg-gray-300");
                } else if (!shift1Value && shift2Value) {
                    // Shift 1 kosong, Shift 2 terisi
                    document.getElementById("temp-shift-1").disabled = true;
                    document.getElementById("temp-shift-1").classList.add("bg-gray-300");
                    document.getElementById("humidity-shift-1").disabled = true;
                    document.getElementById("humidity-shift-1").classList.add("bg-gray-300");
                    
                    document.getElementById("temp-shift-2").disabled = false;
                    document.getElementById("temp-shift-2").classList.remove("bg-gray-300");
                    document.getElementById("humidity-shift-2").disabled = false;
                    document.getElementById("humidity-shift-2").classList.remove("bg-gray-300");
                } else if (shift1Value && shift2Value) {
                    // Kedua shift terisi (khusus untuk kasus edit)
                    document.getElementById("temp-shift-1").disabled = false;
                    document.getElementById("temp-shift-1").classList.remove("bg-gray-300");
                    document.getElementById("humidity-shift-1").disabled = false;
                    document.getElementById("humidity-shift-1").classList.remove("bg-gray-300");
                    
                    document.getElementById("temp-shift-2").disabled = false;
                    document.getElementById("temp-shift-2").classList.remove("bg-gray-300");
                    document.getElementById("humidity-shift-2").disabled = false;
                    document.getElementById("humidity-shift-2").classList.remove("bg-gray-300");
                } else {
                    // Kedua shift kosong
                    document.getElementById("temp-shift-1").disabled = true;
                    document.getElementById("temp-shift-1").classList.add("bg-gray-300");
                    document.getElementById("humidity-shift-1").disabled = true;
                    document.getElementById("humidity-shift-1").classList.add("bg-gray-300");
                    
                    document.getElementById("temp-shift-2").disabled = true;
                    document.getElementById("temp-shift-2").classList.add("bg-gray-300");
                    document.getElementById("humidity-shift-2").disabled = true;
                    document.getElementById("humidity-shift-2").classList.add("bg-gray-300");
                }
            }

            // Inisialisasi status field saat halaman dimuat
            setupFieldsBasedOnShifts();

            // Event listener untuk tanggal
            document.getElementById("tanggal").addEventListener("change", function() {
                let tanggal = new Date(this.value);
                let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
                document.getElementById("hari").value = hari;
            });
        });

        // Fungsi untuk memilih shift
        function pilihShift(shift) {
            let username = "{{ Auth::user()->username }}"; // Ambil username yang sedang login

            if (shift === 1) {
                document.getElementById('shift1').value = username; // Isi Shift 1
                document.getElementById('shift2').value = ""; // Kosongkan Shift 2

                // Aktifkan input shift 1 & nonaktifkan shift 2
                document.getElementById("temp-shift-1").disabled = false;
                document.getElementById("temp-shift-1").classList.remove("bg-gray-300");

                document.getElementById("humidity-shift-1").disabled = false;
                document.getElementById("humidity-shift-1").classList.remove("bg-gray-300");

                document.getElementById("temp-shift-2").disabled = true;
                document.getElementById("temp-shift-2").classList.add("bg-gray-300");

                document.getElementById("humidity-shift-2").disabled = true;
                document.getElementById("humidity-shift-2").classList.add("bg-gray-300");
            } else {
                document.getElementById('shift2').value = username; // Isi Shift 2
                document.getElementById('shift1').value = ""; // Kosongkan Shift 1

                // Aktifkan input shift 2 & nonaktifkan shift 1
                document.getElementById("temp-shift-2").disabled = false;
                document.getElementById("temp-shift-2").classList.remove("bg-gray-300");

                document.getElementById("humidity-shift-2").disabled = false;
                document.getElementById("humidity-shift-2").classList.remove("bg-gray-300");

                document.getElementById("temp-shift-1").disabled = true;
                document.getElementById("temp-shift-1").classList.add("bg-gray-300");

                document.getElementById("humidity-shift-1").disabled = true;
                document.getElementById("humidity-shift-1").classList.add("bg-gray-300");
            }
        }
    </script>
</body>
</html>