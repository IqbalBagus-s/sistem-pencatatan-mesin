<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Hopper</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-sky-50 font-sans">
    <div class="container mx-auto mt-4 px-4">
        <h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Hopper</h2>

        <div class="bg-white rounded-lg shadow-md mb-5">
            <div class="p-4">
                <!-- Menampilkan Nama Checker -->
                <div class="bg-sky-50 p-4 rounded-md mb-5">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">
                        {{ $hopperRecord->created_by_1 ?? $hopperRecord->created_by_2 ?? $hopperRecord->created_by_3 ?? $hopperRecord->created_by_4 ?? 'Tidak Diketahui' }}
                    </span>
                </div>

                <!-- Detail Informasi -->
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">No Hopper:</label>
                        <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md flex items-center">
                            {{ $hopperRecord->nomer_hopper }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                        <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md flex items-center">
                            {{ \Carbon\Carbon::parse($hopperRecord->bulan)->translatedFormat('F Y') }}
                        </div>
                    </div>
                </div>

                <!-- Tabel Inspeksi -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse border border-gray-300 text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 p-1 w-10">No</th>
                                <th class="border border-gray-300 p-1 w-24">No Hopper</th>
                                <th class="border border-gray-300 p-1 w-20">Minggu 1</th>
                                <th class="border border-gray-300 p-1 w-20">Keterangan 1</th>
                                <th class="border border-gray-300 p-1 w-20">Minggu 2</th>
                                <th class="border border-gray-300 p-1 w-20">Keterangan 2</th>
                                <th class="border border-gray-300 p-1 w-20">Minggu 3</th>
                                <th class="border border-gray-300 p-1 w-20">Keterangan 3</th>
                                <th class="border border-gray-300 p-1 w-20">Minggu 4</th>
                                <th class="border border-gray-300 p-1 w-20">Keterangan 4</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $items = [
                                    1 => 'Filter',
                                    2 => 'Selang', 
                                    3 => 'Kontraktor',
                                    4 => 'Temperatur Kontrol',
                                    5 => 'MCB'
                                ];
                            @endphp
                            
                            @foreach($items as $i => $item)
                                <tr class="bg-white">
                                    <td class="border border-gray-300 p-1 text-center w-10">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 text-center w-24">{{ $item }}</td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'check_1'}[$i] ?? '' }}
                                    </td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'keterangan_1'}[$i] ?? '' }}
                                    </td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'check_2'}[$i] ?? '' }}
                                    </td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'keterangan_2'}[$i] ?? '' }}
                                    </td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'check_3'}[$i] ?? '' }}
                                    </td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'keterangan_3'}[$i] ?? '' }}
                                    </td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'check_4'}[$i] ?? '' }}
                                    </td>
                                    <td class="border border-gray-300 p-1 text-center w-20">
                                        {{ $hopperRecord->{'keterangan_4'}[$i] ?? '' }}
                                    </td>
                                </tr>
                                @if($i == 2)
                                    <tr>
                                        <td colspan="10" class="border border-gray-300 text-center p-2 h-12 bg-gray-100 font-medium">
                                            Panel Kelistrikan
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-start mt-6">
                    <a href="{{ route('hopper.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto w-full">
        <p class="mb-0 font-bold">2025 © PT Asia Pramulia</p>
    </footer>
</body>
</html>