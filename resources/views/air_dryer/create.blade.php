<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pencatatan Mesin Air Dryer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Form Pencatatan Mesin Air Dryer</h2>

        <!-- Menampilkan Nama Checker -->
        <div class="mb-4 p-4 bg-gray-200 rounded">
            <p class="text-lg font-semibold text-gray-700">Checker: <span class="text-blue-600">{{ Auth::user()->username }}</span></p>
        </div>

        <!-- Form Input -->
        <form action="{{ route('air-dryer.store') }}" method="POST">
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
                            <th class="border border-gray-300 p-2">Nomor Mesin</th>
                            <th class="border border-gray-300 p-2">Temperatur Kompresor</th>
                            <th class="border border-gray-300 p-2">Temperatur Kabel</th>
                            <th class="border border-gray-300 p-2">Temperatur MCB</th>
                            <th class="border border-gray-300 p-2">Temperatur Angin In</th>
                            <th class="border border-gray-300 p-2">Temperatur Angin Out</th>
                            <th class="border border-gray-300 p-2">Evaporator</th>
                            <th class="border border-gray-300 p-2">Fan Evaporator</th>
                            <th class="border border-gray-300 p-2">Auto Drain</th>
                            <th class="border border-gray-300 p-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>

            <!-- Detail Mesin -->
            <div class="mt-4 p-4 bg-gray-100 rounded w-1/2">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Detail Mesin:</h3>
                <p class="text-gray-700">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                <p class="text-gray-700">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                <p class="text-gray-700">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                <p class="text-gray-700">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
            </div>

            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Simpan
            </button>
        </form>
    </div>

    <script>
        // Data mesin air dryer
        const jumlahMesin = 8; // Sesuaikan jumlah mesin
        const tableBody = document.getElementById("table-body");

        for (let i = 1; i <= jumlahMesin; i++) {
            let nomorMesin = `AD${i}`;

            let row = `<tr class="bg-white">
                <td class="border border-gray-300 p-2 text-center">${i}</td>
                <td class="border border-gray-300 p-2 text-center">
                    <input type="text" name="nomor_mesin[${i}]" value="${nomorMesin}" class="w-20 p-1 border border-gray-300 rounded bg-gray-100" readonly>
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_kompresor[${i}]" 
                        class="w-full p-1 border border-gray-300 rounded"
                        placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_kabel[${i}]" 
                        class="w-full p-1 border border-gray-300 rounded"
                        placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_mcb[${i}]" 
                        class="w-full p-1 border border-gray-300 rounded"
                        placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_angin_in[${i}]" 
                        class="w-full p-1 border border-gray-300 rounded"
                        placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_angin_out[${i}]" 
                        class="w-full p-1 border border-gray-300 rounded"
                        placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <select name="evaporator[${i}]" class="w-full p-1 border border-gray-300 rounded">
                        <option value="Bersih">Bersih</option>
                        <option value="Kotor">Kotor</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td class="border border-gray-300 p-2 w-40">
                    <select name="fan_evaporator[${i}]" class="w-full p-1 border border-gray-300 rounded">
                        <option value="Suara Halus">Suara Halus</option>
                        <option value="Suara Kasar">Suara Kasar</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td class="border border-gray-300 p-2 w-40">
                    <select name="auto_drain[${i}]" class="w-full p-1 border border-gray-300 rounded">
                        <option value="Berfungsi">Berfungsi</option>
                        <option value="Tidak Berfungsi">Tidak Berfungsi</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="keterangan[${i}]" class="w-full p-1 border border-gray-300 rounded">
                </td>
            </tr>`;

            tableBody.innerHTML += row;
        }

        // Fungsi untuk mengubah tanggal menjadi hari otomatis
        document.getElementById("tanggal").addEventListener("change", function() {
            let tanggal = new Date(this.value);
            let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
            document.getElementById("hari").value = hari;
        });
    </script>

</body>
</html>
