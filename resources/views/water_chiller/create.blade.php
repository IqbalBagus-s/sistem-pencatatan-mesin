<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pencatatan Mesin Water Chiller</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-sky-50 font-sans">
    <div class="container mx-auto mt-4 px-4">
        <h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Water Chiller</h2>

        <div class="bg-white rounded-lg shadow-md mb-5">
            <div class="p-4">
                <!-- Menampilkan Nama Checker -->
                <div class="bg-sky-50 p-4 rounded-md mb-5">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
                </div>

                <!-- Form Input -->
                <form action="{{ route('water-chiller.store') }}" method="POST">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Hari:</label>
                            <input type="text" id="hari" name="hari" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" readonly>
                        </div>
                        <div>
                            <label class="block mb-2">Tanggal:</label>
                            <input type="date" id="tanggal" name="tanggal" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                        </div>
                    </div>

                    <!-- Tabel Inspeksi -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-12">NO.</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-20">No Mesin</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Compressor</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kabel</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Mcb</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Air</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Pompa</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24">Evaporator</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-28">Fan Evaporator</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24">Freon</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24">Air</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 32; $i++)
                                    <tr>
                                        <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                        <td class="border border-gray-300 text-center p-2">
                                            <input type="text" name="no_mesin[{{ $i }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center" 
                                                value="CH{{ $i }}" readonly>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_1[{{ $i }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" required>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_2[{{ $i }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" required>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_3[{{ $i }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" required>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_4[{{ $i }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" required>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_5[{{ $i }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" required>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                <option value="Bersih">Bersih</option>
                                                <option value="Kotor">Kotor</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                <option value="Suara Halus">Suara Halus</option>
                                                <option value="Suara Keras">Suara Keras</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="freon[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                <option value="Cukup">Cukup</option>
                                                <option value="Tidak Cukup">Tidak Cukup</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="air[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
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
                    <div class="mt-5">
                        <label for="keterangan" class="block mb-2 font-medium">Keterangan:</label>
                        <textarea id="keterangan" name="keterangan" rows="3"
                            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" 
                            placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('water-chiller.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Kembali
                        </a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto w-full">
        <p class="mb-0 font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
    <script>
        document.getElementById("tanggal").addEventListener("change", function() {
            let tanggal = new Date(this.value);
            let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
            document.getElementById("hari").value = hari;
        });
    </script>
</body>
</html>