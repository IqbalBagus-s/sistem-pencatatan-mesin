<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pencatatan Mesin Air Dryer</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-sky-50 font-sans">
    <div class="container mx-auto mt-4 px-4">
        <h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Air Dryer</h2>

        <div class="bg-white rounded-lg shadow-md mb-5">
            <div class="p-4">
                <!-- Menampilkan Nama Checker -->
                <div class="bg-sky-50 p-4 rounded-md mb-5">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
                </div>

                <!-- Form Input -->
                <form action="{{ route('air-dryer.store') }}" method="POST">
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

                    <!-- Tabel Inspeksi -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="border border-gray-300 bg-sky-50 p-2">No</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Nomor Mesin</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kompresor</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kabel</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur MCB</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Angin In</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Angin Out</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Evaporator</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2">Fan Evaporator</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-[140px]">Auto Drain</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 8; $i++)
                                    <tr>
                                        <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                        <td class="border border-gray-300 text-center p-2">
                                            <input type="text" name="nomor_mesin[{{ $i }}]" value="AD{{ $i }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" readonly>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_kompresor[{{ $i }}]" value="{{ old("temperatur_kompresor.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_kabel[{{ $i }}]" value="{{ old("temperatur_kabel.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_mcb[{{ $i }}]" value="{{ old("temperatur_mcb.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_angin_in[{{ $i }}]" value="{{ old("temperatur_angin_in.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_angin_out[{{ $i }}]" value="{{ old("temperatur_angin_out.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                                                <option value="Bersih">Bersih</option>
                                                <option value="Kotor">Kotor</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                                                <option value="Suara Halus">Suara Halus</option>
                                                <option value="Suara Kasar">Suara Kasar</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="auto_drain[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                                                <option value="Berfungsi">Berfungsi</option>
                                                <option value="Tidak Berfungsi">Tidak Berfungsi</option>
                                                <option value="OFF">OFF</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <!-- Detail Mesin & Form Input Keterangan -->
                    <div class="flex flex-col md:flex-row gap-4 mt-5">
                        <!-- Detail Mesin -->
                        <div class="bg-gray-100 p-4 rounded-md md:w-auto">
                            <h5 class="mb-3 font-medium">Detail Mesin:</h5>
                            <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                            <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                            <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                            <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
                        </div>

                        <!-- Form Input Keterangan -->
                        <div class="flex-1">
                            <label for="keterangan" class="block mb-2 font-medium">Keterangan:</label>
                            <textarea id="keterangan" name="keterangan" rows="5"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" 
                                placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="{{ route('air-dryer.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">Simpan</button>
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