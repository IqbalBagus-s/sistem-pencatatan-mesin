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
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2" rowspan="3">NO.</th>
                            <th class="border border-gray-300 p-2" rowspan="3">Checked Items</th>
                            <th class="border border-gray-300 p-2" colspan="12">Hasil Pemeriksaan</th>
                        </tr>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2" colspan="2">KL 10</th>
                            <th class="border border-gray-300 p-2" colspan="2">KL 5</th>
                            <th class="border border-gray-300 p-2" colspan="2">KL 6</th>
                            <th class="border border-gray-300 p-2" colspan="2">KL 7</th>
                            <th class="border border-gray-300 p-2" colspan="2">KL 8</th>
                            <th class="border border-gray-300 p-2" colspan="2">KL 9</th>
                        </tr>
                        <tr class="bg-gray-200">
                            @for ($i = 0; $i < 6; $i++)
                                <th class="border border-gray-300 p-2">I</th>
                                <th class="border border-gray-300 p-2">II</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @php
                            $checkedItems = [
                                "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                                "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
                            ];
                            $selectIndexes = [5, 6, 7, 8, 9, 12, 16]; // Indeks yang menggunakan pilihan Ya/Tidak
            
                            // Sesuai dengan header, hanya ada 12 kolom KL
                            $klColumns = ['KL 10I', 'KL 10II', 'KL 5I', 'KL 5II', 'KL 6I', 'KL 6II', 'KL 7I', 'KL 7II', 'KL 8I', 'KL 8II', 'KL 9I', 'KL 9II'];
                        @endphp
                        
                        @foreach ($checkedItems as $index => $item)
                            <tr>
                                <td class="border border-gray-300 p-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2">{{ $item }}</td>
            
                                @foreach ($klColumns as $kl)
                                    <td class="border border-gray-300 p-2">
                                        @if (in_array($index, $selectIndexes))
                                            <select name="kl_{{ $kl }}[{{ $index }}]" class="w-full border-gray-300 p-1">
                                                <option value="">Pilih</option>
                                                <option value="Ya">Ya</option>
                                                <option value="Tidak">Tidak</option>
                                            </select>
                                        @else
                                            <input type="text" name="kl_{{ $kl }}[{{ $index }}]" class="w-full border-gray-300 p-1">
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div>
                    Form pengisian High Kompressor
                </div>
 
                <!-- Tabel High Kompressor -->           
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 p-2">NO.</th>
                            <th class="border border-gray-300 p-2">Checked Items</th>
                            <th class="border border-gray-300 p-2">Standart KL</th>

                            <!-- Kompresor High -->
                            <th class="border border-gray-300 p-2">KH 7I</th>
                            <th class="border border-gray-300 p-2">KH 7II</th>
                            <th class="border border-gray-300 p-2">KH 8I</th>
                            <th class="border border-gray-300 p-2">KH 8II</th>
                            <th class="border border-gray-300 p-2">KH 9I</th>
                            <th class="border border-gray-300 p-2">KH 9II</th>
                            <th class="border border-gray-300 p-2">KH 10I</th>
                            <th class="border border-gray-300 p-2">KH 10II</th>
                            <th class="border border-gray-300 p-2">KH 11I</th>
                            <th class="border border-gray-300 p-2">KH 11II</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">

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