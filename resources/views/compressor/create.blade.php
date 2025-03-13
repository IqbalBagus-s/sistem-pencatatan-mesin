<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pencatatan Mesin Water Chiller</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Form Pencatatan Mesin Water Chiller</h2>

        <!-- Menampilkan Nama Checker -->
        <div class="mb-4 p-4 bg-gray-200 rounded">
            <p class="text-lg font-semibold text-gray-700">Checker: <span class="text-blue-600">{{ Auth::user()->username }}</span></p>
        </div>

        <!-- Form Input -->
        <form action="{{ route('water-chiller.store') }}" method="POST">
            @csrf
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Hari:</label>
                    <input type="text" id="hari" name="hari" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-gray-700">Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" class="w-full p-2 border border-gray-300 rounded" required>
                </div>
            </div>

            <div class="overflow-x-auto">
                <!-- Tabel Low Kompressor -->
                <div class="text-lg font-semibold mb-4">
                    Form Pengisian Low Kompressor
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
                        <thead class="bg-gray-200 text-center">
                            <tr>
                                <th class="border border-gray-300 p-2" rowspan="3">NO.</th>
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
                            @endphp
                
                            @foreach ($checkedItems as $index => $item)
                                <tr class="hover:bg-gray-100">
                                    <td class="border border-gray-300 p-2">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 p-2 w-1">{{ $item }}</td>
                
                                    @foreach ($klColumns as $kl)
                                        <td class="border border-gray-300 p-2 w-auto">
                                            @if (isset($customOptions[$index]))
                                                <select name="kl_{{ $kl }}[{{ $index }}]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                    @foreach ($customOptions[$index] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" name="kl_{{ $kl }}[{{ $index }}]" class="w-full border border-gray-300 p-1 rounded text-center" placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}">
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>                

                <!-- Tabel High Kompressor -->
                <div class="text-lg font-semibold mb-4 mt-4">
                    Form Pengisian High Kompressor
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
                        <thead class="bg-gray-200 text-center">
                            <tr>
                                <th class="border border-gray-300 p-2" rowspan="3">NO.</th>
                                <th class="border border-gray-300 p-2" rowspan="3">Checked Items</th>
                                <th class="border border-gray-300 p-2" colspan="12">Hasil Pemeriksaan</th>
                            </tr>
                            <tr>
                                <th class="border border-gray-300 p-2" colspan="2">KL 7</th>
                                <th class="border border-gray-300 p-2" colspan="2">KL 8</th>
                                <th class="border border-gray-300 p-2" colspan="2">KL 9</th>
                                <th class="border border-gray-300 p-2" colspan="2">KL 10</th>
                                <th class="border border-gray-300 p-2" colspan="2">KL 11</th>
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
                        
                                // Sesuai dengan header, hanya ada 12 kolom KL
                                $klColumns = ['KL 7I', 'KL 7II', 'KL 8I', 'KL 8II', 'KL 9I', 'KL 9II', 'KL 10I', 'KL 10II', 'KL 11I', 'KL 11II'];
                            @endphp
                
                            @foreach ($checkedItems as $index => $item)
                                <tr class="hover:bg-gray-100">
                                    <td class="border border-gray-300 p-2">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 p-2 w-1/12">{{ $item }}</td>
                
                                    @foreach ($klColumns as $kl)
                                        <td class="border border-gray-300 p-2 w-auto">
                                            @if (isset($customOptions[$index]))
                                                <select name="kl_{{ $kl }}[{{ $index }}]" class="w-full border border-gray-300 p-1 rounded appearance-none text-center">
                                                    @foreach ($customOptions[$index] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="text" name="kl_{{ $kl }}[{{ $index }}]" class="w-full border border-gray-300 p-1 rounded text-center" placeholder="{{ $placeholders[$index] ?? 'Masukkan nilai' }}">
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
                    Simpan
                </button>
                <a href="{{ route('water-chiller.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Kembali
                </a>
            </div>
        </form>
    </div>

    <!-- Script untuk mengisi hari berdasarkan tanggal -->
    <script>
        document.getElementById("tanggal").addEventListener("change", function() {
            let tanggal = new Date(this.value);
            let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
            document.getElementById("hari").value = hari;
        });
    </script>

</body>
</html>