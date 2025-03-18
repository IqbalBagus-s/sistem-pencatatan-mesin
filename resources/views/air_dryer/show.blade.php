<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin Air Dryer</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-blue-50 pt-5 font-poppins min-h-screen flex flex-col overscroll-none">

    <div class="container mx-auto bg-white p-4 rounded-lg shadow-md">
        <h2 class="mb-4 font-bold text-xl">Approval Pencatatan Mesin Air Dryer</h2>

        <form action="{{ route('air-dryer.approve', $check->id) }}" method="POST">
            @csrf

            <!-- Tampilkan nama approver yang sedang login -->
            <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                @if(!$check->approved_by)
                <p class="text-lg font-semibold">Approver: 
                    <span class="text-blue-600">{{ Auth::user()->username }}</span></p>
                @else
                <p class="text-lg font-semibold">Approver: 
                    <span class="text-blue-600">{{ $check->approved_by }}</span></p>
                @endif
            </div>

            <!-- Tampilkan nama checker yang mengisi data -->
            <div class="mb-4 p-3 bg-gray-100 rounded-lg">
                <p class="text-lg font-semibold">Checker: 
                    <span class="text-green-600">{{ $check->checked_by }}</span></p>
            </div>

            {{-- tanggal form di isi --}}
            <div class="mb-3">
                <label class="block font-medium mb-1">Tanggal:</label>
                <input type="date" value="{{ $check->tanggal }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
            </div>

            <div class="mb-3">
                <label class="block font-medium mb-1">Hari:</label>
                <input type="text" value="{{ $check->hari }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
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
                        <tr>
                            <td class="text-center border border-gray-300 p-2">{{ $index + 1 }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->nomor_mesin }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_kompresor }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_kabel }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_mcb }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_angin_in }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_angin_out }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->evaporator }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->fan_evaporator }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->auto_drain }}</td>
                            <td class="text-center border border-gray-300 p-2">{{ $result->keterangan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-3 bg-gray-100 rounded-lg md:w-1/2">
                <h3 class="text-lg font-semibold mb-2">Detail Mesin:</h3>
                <p>AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                <p>AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                <p>AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                <p>AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
            </div>

            <div class="mt-4 flex justify-between">
                @if(!$check->approved_by)
                <a href="{{ route('air-dryer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition duration-200">
                    Kembali
                </a>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition duration-200">
                    Setujui
                </button>
                @else
                <a href="{{ route('air-dryer.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition duration-200">
                    Kembali
                </a>
                <a href="{{ route('air-dryer.downloadPdf', $check->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                    Download PDF
                </a>
                <button type="submit" class="px-4 py-2 bg-gray-600 opacity-75 text-white rounded-md cursor-not-allowed" disabled>
                    Telah Disetujui
                </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto w-full">
        <p class="font-bold">2025 © PT Asia Pramulia</p>
    </footer>
</body>
</html>