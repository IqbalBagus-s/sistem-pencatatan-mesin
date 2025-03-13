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

            <!-- Tabel Inspeksi -->
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 p-2">NO.</th>
                            <th class="border border-gray-300 p-2">No Mesin</th>
                            <th class="border border-gray-300 p-2">Temperatur Compressor</th>
                            <th class="border border-gray-300 p-2">Temperatur Kabel</th>
                            <th class="border border-gray-300 p-2">Temperatur Mcb</th>
                            <th class="border border-gray-300 p-2">Temperatur Air</th>
                            <th class="border border-gray-300 p-2">Temperatur Pompa</th>
                            <th class="border border-gray-300 p-2">Evaporator</th>
                            <th class="border border-gray-300 p-2">Fan Evaporator</th>
                            <th class="border border-gray-300 p-2">Freon</th>
                            <th class="border border-gray-300 p-2">Air</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        @for ($i = 1; $i <= 32; $i++)
                            <tr class="bg-white">
                                <td class="border border-gray-300 p-2 text-center">{{ $i }}</td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="no_mesin[{{ $i }}]" 
                                        class="w-full p-1 border border-gray-300 rounded bg-gray-200 text-center" 
                                        value="CH{{ $i }}" readonly>
                                </td>
                                @for ($j = 1; $j <= 5; $j++)
                                    <td class="border border-gray-300 p-2 text-center">
                                        <input type="text" name="temperatur_{{ $j }}[{{ $i }}]" 
                                            class="w-full p-1 border border-gray-300 rounded text-center" required>
                                    </td>
                                @endfor
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="evaporator[{{ $i }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                        <option value="Bersih">Bersih</option>
                                        <option value="Kotor">Kotor</option>
                                        <option value="OFF">OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="fan_evaporator[{{ $i }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                        <option value="Suara Halus">Suara Halus</option>
                                        <option value="Suara Keras">Suara Keras</option>
                                        <option value="OFF">OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="freon[{{ $i }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                        <option value="Cukup">Cukup</option>
                                        <option value="Tidak Cukup">Tidak Cukup</option>
                                        <option value="OFF">OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="air[{{ $i }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                        <option value="Cukup">Cukup</option>
                                        <option value="Tidak Cukup">Tidak Cukup</option>
                                        <option value="OFF">OFF</option>
                                    </select>
                                </td>
                            </tr>
                        @endfor
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