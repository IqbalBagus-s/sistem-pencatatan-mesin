<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pencatatan Mesin Water Chiller</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <!-- Tabel Inspeksi -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2">No</th>
                            <th class="border border-gray-300 p-2" style="width: 200px; min-width: 200px;">ITEM YANG DIPERIKSA</th>
                            <th class="border border-gray-300 p-2" style="width: 130px; min-width: 130px;">STANDART</th>
                            @for ($i = 1; $i <= 32; $i++)
                                <th class="border border-gray-300 p-2 text-center" style="width: 80px; min-width: 80px;">CH{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @php
                            $checkedItems = [
                                ['Temperatur Compressor', '30 °C - 60 °C'],
                                ['Temperatur Kabel', '30 °C - 45 °C'],
                                ['Temperatur Mcb', '30 °C - 50 °C'],
                                ['Temperatur Air', 'Sesuai Setelan'],
                                ['Temperatur Pompa', '40 °C - 50 °C'],
                                ['Evaporator', 'Bersih'],
                                ['Fan Evaporator', 'Suara Halus'],
                                ['Freon', 'Cukup'],
                                ['Air', 'Cukup']
                            ];
                        @endphp
                    
                        @foreach ($checkedItems as $index => $item)
                            <tr class="bg-white">
                                <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2 text-center" style="width: 250px;">
                                    <input type="text" name="checked_items[{{ $index + 1 }}]" 
                                        class="w-full p-1 border border-gray-300 rounded bg-gray-200 text-center"
                                        value="{{ $item[0] }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-full p-1 border border-gray-300 rounded bg-gray-200 text-center"
                                        value="{{ $item[1] }}" readonly>
                                </td>
                                @for ($j = 1; $j <= 32; $j++)
                                    <td class="border border-gray-300 p-2 text-center">
                                        @if ($index >= 5) 
                                            <!-- Dropdown dengan pilihan ✔️, ❌, ➖ -->
                                            <select name="CH{{ $j }}[{{ $index + 1 }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                                <option value="✔️">✔️</option>
                                                <option value="❌">❌</option>
                                                <option value="➖">➖</option>
                                            </select>
                                        @else
                                            <!-- Input teks wajib diisi -->
                                            <input type="text" name="CH{{ $j }}[{{ $index + 1 }}]" 
                                                class="w-full p-1 border border-gray-300 rounded text-center" required/>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach                
                    </tbody>                    
                </table>
            </div>

            <!-- Form Input Keterangan -->
            <div class="mt-4">
                <label for="keterangan" class="block text-gray-700 font-semibold">Keterangan:</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="w-full p-2 border border-gray-300 rounded" 
                    placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
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