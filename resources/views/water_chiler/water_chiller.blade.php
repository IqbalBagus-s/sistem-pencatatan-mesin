<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Inspeksi Water Chiller</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Form Inspeksi Water Chiller</h2>

        <!-- Form untuk input hari & tanggal manual -->
        <form action="{{ route('water_chiller.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Hari:</label>
                <input type="text" name="hari" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan hari">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Tanggal:</label>
                <input type="date" name="tanggal" class="w-full p-2 border border-gray-300 rounded">
            </div>

            <!-- Tabel Inspeksi -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2">No</th>
                            <th class="border border-gray-300 p-2">Item yang Diperiksa</th>
                            <th class="border border-gray-300 p-2">Standar</th>
                            @for ($i = 1; $i <= 32; $i++)
                                <th class="border border-gray-300 p-2">CH{{ $i }}</th>
                            @endfor
                            <th class="border border-gray-300 p-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody id="table-body"></tbody>
                </table>
            </div>

            <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Submit
            </button>
        </form>
    </div>

    <script>
        // Data standar
        const standards = {
            "Temperatur Compressor": "30°C - 60°C",
            "Temperatur Kabel": "30°C - 45°C",
            "Temperatur MCB": "30°C - 50°C",
            "Temperatur Air": "Sesuai Setelan",
            "Temperatur Pompa": "40°C - 50°C",
            "Evaporator": "Bersih",
            "Fan Evaporator": "Suara Halus",
            "Freon": "Cukup",
            "Air Check": "Cukup"
        };

        // Membuat tabel dinamis dengan 32 CH
        const tableBody = document.getElementById("table-body");
        let rowIndex = 1;
        for (const [key, value] of Object.entries(standards)) {
            let row = `<tr class="bg-white">
                <td class="border border-gray-300 p-2 text-center">${rowIndex++}</td>
                <td class="border border-gray-300 p-2">${key}</td>
                <td class="border border-gray-300 p-2 text-center">${value}</td>`;

            for (let i = 1; i <= 32; i++) {
                if (["Evaporator", "Fan Evaporator", "Freon", "Air Check"].includes(key)) {
                    row += `<td class="border border-gray-300 p-2 text-center">
                                <input type="checkbox" name="${key.replace(/\s/g, "_")}_CH${i}" class="h-4 w-4">
                            </td>`;
                } else {
                    row += `<td class="border border-gray-300 p-2 text-center">
                                <input type="text" name="${key.replace(/\s/g, "_")}_CH${i}" class="w-16 p-1 border border-gray-300 rounded">
                            </td>`;
                }
            }

            row += `<td class="border border-gray-300 p-2">
                        <input type="text" name="Keterangan_CH${rowIndex - 1}" class="w-full p-1 border border-gray-300 rounded">
                    </td>
                </tr>`;

            tableBody.innerHTML += row;
        }
    </script>

</body>
</html>
