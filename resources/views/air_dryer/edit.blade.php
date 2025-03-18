<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pencatatan Mesin Air Dryer</title>
 
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 p-3">

    <div class="container mx-auto mt-4 px-4">
        <h2 class="mb-4 text-xl font-bold">Edit Pencatatan Mesin Air Dryer</h2>
    
        <div class="bg-white rounded-lg shadow-md mb-5">
            <div class="p-4">
                <!-- Menampilkan Nama Checker -->
                <div class="bg-sky-50 p-4 rounded-md mb-5">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
                </div>
    
                <!-- Form Input -->
                <form action="{{ route('air-dryer.update', $check->id) }}" method="POST">
                    @csrf
                    @method('PUT')
    
                    <!-- Input Tanggal dan Hari -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Tanggal:</label>
                            <input type="date" name="tanggal" value="{{ $check->tanggal }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                        </div>
                        <div>
                            <label class="block mb-2">Hari:</label>
                            <input type="text" name="hari" value="{{ $check->hari }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
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
                                @foreach($results as $index => $result)
                                <tr>
                                    <td class="border border-gray-300 text-center p-2">{{ $index + 1 }}</td>
                                    <td class="border border-gray-300 text-center p-2">
                                        <input type="text" name="nomor_mesin[{{ $result->id }}]" value="{{ $result->nomor_mesin }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" readonly>
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_kompresor[{{ $result->id }}]" value="{{ $result->temperatur_kompresor }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_kabel[{{ $result->id }}]" value="{{ $result->temperatur_kabel }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_mcb[{{ $result->id }}]" value="{{ $result->temperatur_mcb }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_angin_in[{{ $result->id }}]" value="{{ $result->temperatur_angin_in }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_angin_out[{{ $result->id }}]" value="{{ $result->temperatur_angin_out }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <select name="evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                            <option value="Bersih" {{ $result->evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                            <option value="Kotor" {{ $result->evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                            <option value="OFF" {{ $result->evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <select name="fan_evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                            <option value="Suara Halus" {{ $result->fan_evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                            <option value="Suara Kasar" {{ $result->fan_evaporator == 'Suara Kasar' ? 'selected' : '' }}>Suara Kasar</option>
                                            <option value="OFF" {{ $result->fan_evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <select name="auto_drain[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                            <option value="Berfungsi" {{ $result->auto_drain == 'Berfungsi' ? 'selected' : '' }}>Berfungsi</option>
                                            <option value="Tidak Berfungsi" {{ $result->auto_drain == 'Tidak Berfungsi' ? 'selected' : '' }}>Tidak Berfungsi</option>
                                            <option value="OFF" {{ $result->auto_drain == 'OFF' ? 'selected' : '' }}>OFF</option>
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
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
                                placeholder="Tambahkan keterangan jika diperlukan...">{{ $check->keterangan }}</textarea>
                        </div>
                    </div>
    
                    <!-- Tombol Kembali dan Simpan -->
                    <div class="flex justify-between mt-6">
                        <a href="{{ route('air-dryer.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-4 w-full">
        <p class="font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
</body>
</html>