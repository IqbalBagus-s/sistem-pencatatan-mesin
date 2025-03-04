<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Pencatatan Mesin Air Dryer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Pencatatan Mesin Air Dryer</h2>

        <form action="{{ route('air-dryer.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700">Tanggal:</label>
                <input type="date" name="tanggal" value="{{ $check->tanggal }}" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700">Hari:</label>
                <input type="text" name="hari" value="{{ $check->hari }}" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
            </div>
            

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
                    <tbody>
                        @foreach($results as $index => $result)
                        <tr class="bg-white">
                            <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-2 text-center">
                                <input type="text" name="nomor_mesin[{{ $result->id }}]" value="{{ $result->nomor_mesin }}" class="w-20 p-1 border border-gray-300 rounded bg-gray-100" readonly>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_kompresor[{{ $result->id }}]" value="{{ $result->temperatur_kompresor }}" class="w-full p-1 border border-gray-300 rounded">
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_kabel[{{ $result->id }}]" value="{{ $result->temperatur_kabel }}" class="w-full p-1 border border-gray-300 rounded">
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_mcb[{{ $result->id }}]" value="{{ $result->temperatur_mcb }}" class="w-full p-1 border border-gray-300 rounded">
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_angin_in[{{ $result->id }}]" value="{{ $result->temperatur_angin_in }}" class="w-full p-1 border border-gray-300 rounded">
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="temperatur_angin_out[{{ $result->id }}]" value="{{ $result->temperatur_angin_out }}" class="w-full p-1 border border-gray-300 rounded">
                            </td>
                            <td class="border border-gray-300 p-2">
                                <select name="evaporator[{{ $result->id }}]" class="w-full p-1 border border-gray-300 rounded">
                                    <option value="Bersih" {{ $result->evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                    <option value="Kotor" {{ $result->evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                    <option value="OFF" {{ $result->evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td class="border border-gray-300 p-2 w-40">
                                <select name="fan_evaporator[{{ $result->id }}]" class="w-full p-1 border border-gray-300 rounded">
                                    <option value="Suara Halus" {{ $result->fan_evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                    <option value="Suara Kasar" {{ $result->fan_evaporator == 'Suara Kasar' ? 'selected' : '' }}>Suara Kasar</option>
                                    <option value="OFF" {{ $result->fan_evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td class="border border-gray-300 p-2 w-40">
                                <select name="auto_drain[{{ $result->id }}]" class="w-full p-1 border border-gray-300 rounded">
                                    <option value="Berfungsi" {{ $result->auto_drain == 'Berfungsi' ? 'selected' : '' }}>Berfungsi</option>
                                    <option value="Tidak Berfungsi" {{ $result->auto_drain == 'Tidak Berfungsi' ? 'selected' : '' }}>Tidak Berfungsi</option>
                                    <option value="OFF" {{ $result->auto_drain == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td class="border border-gray-300 p-2">
                                <input type="text" name="keterangan[{{ $result->id }}]" value="{{ $result->keterangan }}" class="w-full p-1 border border-gray-300 rounded">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
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

            <div class="mt-4 flex justify-between">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan
                </button>
                <a href="{{ route('air-dryer.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Kembali
                </a>
            </div>
        </form>
    </div>

</body>
</html>
