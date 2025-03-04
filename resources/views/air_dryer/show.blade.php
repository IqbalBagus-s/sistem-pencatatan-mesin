<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin Air Dryer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Approval Pencatatan Mesin Air Dryer</h2>

        <form action="{{ route('air-dryer.approve', $check->id) }}" method="POST">
            @csrf

            <!-- Tampilkan nama approver yang sedang login -->
            <div class="mb-4 p-4 bg-gray-200 rounded">
                <p class="text-lg font-semibold text-gray-700">Approver: 
                    <span class="text-blue-600">{{ Auth::user()->username }}</span></p>
            </div>

            

            <div class="mb-4">
                <label class="block text-gray-700">Tanggal:</label>
                <input type="date" value="{{ $check->tanggal }}" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700">Hari:</label>
                <input type="text" value="{{ $check->hari }}" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-2">No</th>
                            <th class="border p-2">Nomor Mesin</th>
                            <th class="border p-2">Temperatur Kompresor</th>
                            <th class="border p-2">Temperatur Kabel</th>
                            <th class="border p-2">Temperatur MCB</th>
                            <th class="border p-2">Temperatur Angin In</th>
                            <th class="border p-2">Temperatur Angin Out</th>
                            <th class="border p-2">Evaporator</th>
                            <th class="border p-2">Fan Evaporator</th>
                            <th class="border p-2">Auto Drain</th>
                            <th class="border p-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                        <tr class="bg-white">
                            <td class="border p-2 text-center">{{ $index + 1 }}</td>
                            <td class="border p-2 text-center">{{ $result->nomor_mesin }}</td>
                            <td class="border p-2 text-center">{{ $result->temperatur_kompresor }}</td>
                            <td class="border p-2 text-center">{{ $result->temperatur_kabel }}</td>
                            <td class="border p-2 text-center">{{ $result->temperatur_mcb }}</td>
                            <td class="border p-2 text-center">{{ $result->temperatur_angin_in }}</td>
                            <td class="border p-2 text-center">{{ $result->temperatur_angin_out }}</td>
                            <td class="border p-2 text-center">{{ $result->evaporator }}</td>
                            <td class="border p-2 text-center">{{ $result->fan_evaporator }}</td>
                            <td class="border p-2 text-center">{{ $result->auto_drain }}</td>
                            <td class="border p-2 text-center">{{ $result->keterangan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-4 bg-gray-100 rounded w-1/2">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">Detail Mesin:</h3>
                <p class="text-gray-700">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                <p class="text-gray-700">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                <p class="text-gray-700">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                <p class="text-gray-700">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
            </div>

            

            <div class="mt-4 flex justify-between">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Setujui
                </button>
                <a href="{{ route('air-dryer.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Kembali
                </a>
            </div>
        </form>
    </div>

</body>
</html>
