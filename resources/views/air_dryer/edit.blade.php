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

    <div class="container mx-auto bg-white p-4 rounded-lg shadow-md">
        <h2 class="font-semibold text-gray-800 mb-4 text-xl">Edit Pencatatan Mesin Air Dryer</h2>

        <form action="{{ route('air-dryer.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="block mb-1">Tanggal:</label>
                <input type="date" name="tanggal" value="{{ $check->tanggal }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
            </div>
            
            <div class="mb-3">
                <label class="block mb-1">Hari:</label>
                <input type="text" name="hari" value="{{ $check->hari }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border border-gray-300 p-2 text-center w-12">No</th>
                            <th class="border border-gray-300 p-2 text-center w-32">Nomor Mesin</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Kompresor</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Kabel</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur MCB</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Angin In</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Angin Out</th>
                            <th class="border border-gray-300 p-2 text-center w-28">Evaporator</th>
                            <th class="border border-gray-300 p-2 text-center w-36">Fan Evaporator</th>
                            <th class="border border-gray-300 p-2 text-center min-w-[140px]">Auto Drain</th>
                            <th class="border border-gray-300 p-2 text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                        <tr>
                            <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-2 text-center">
                                <input type="text" name="nomor_mesin[{{ $result->id }}]" value="{{ $result->nomor_mesin }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded-md" readonly>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_kompresor[{{ $result->id }}]" value="{{ $result->temperatur_kompresor }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md" required>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_kabel[{{ $result->id }}]" value="{{ $result->temperatur_kabel }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md" required>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_mcb[{{ $result->id }}]" value="{{ $result->temperatur_mcb }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md" required>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_angin_in[{{ $result->id }}]" value="{{ $result->temperatur_angin_in }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md" required>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_angin_out[{{ $result->id }}]" value="{{ $result->temperatur_angin_out }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md" required>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <select name="evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md">
                                    <option value="Bersih" {{ $result->evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                    <option value="Kotor" {{ $result->evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                    <option value="OFF" {{ $result->evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <select name="fan_evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md">
                                    <option value="Suara Halus" {{ $result->fan_evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                    <option value="Suara Kasar" {{ $result->fan_evaporator == 'Suara Kasar' ? 'selected' : '' }}>Suara Kasar</option>
                                    <option value="OFF" {{ $result->fan_evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td class="border border-gray-300 p-2 min-w-[140px]">
                                <select name="auto_drain[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md">
                                    <option value="Berfungsi" {{ $result->auto_drain == 'Berfungsi' ? 'selected' : '' }}>Berfungsi</option>
                                    <option value="Tidak Berfungsi" {{ $result->auto_drain == 'Tidak Berfungsi' ? 'selected' : '' }}>Tidak Berfungsi</option>
                                    <option value="OFF" {{ $result->auto_drain == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="keterangan[{{ $result->id }}]" value="{{ $result->keterangan }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Detail Mesin -->
            <div class="mt-4 p-3 bg-gray-100 rounded-md w-1/2">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Detail Mesin:</h3>
                <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
            </div>

            <div class="mt-4 flex justify-between">
                <a href="{{ route('air-dryer.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-400">
                    Kembali
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-4 w-full">
        <p class="font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
</body>
</html>