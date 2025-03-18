<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin Water Chiller</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-blue-50 pt-5 font-poppins min-h-screen flex flex-col overscroll-none">

    <div class="container mx-auto bg-white p-4 rounded-lg shadow-md">
        <h2 class="mb-4 font-bold text-xl">Approval Pencatatan Mesin Water Chiller</h2>

        <form action="{{ route('water-chiller.approve', $check->id) }}" method="POST">
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
                            <th class="border border-gray-300 p-2">Temperatur Air</th>
                            <th class="border border-gray-300 p-2">Temperatur Pompa</th>
                            <th class="border border-gray-300 p-2">Evaporator</th>
                            <th class="border border-gray-300 p-2">Fan Evaporator</th>
                            <th class="border border-gray-300 p-2">Freon</th>
                            <th class="border border-gray-300 p-2">Air</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                            <tr class="bg-white">
                                <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-full p-1 border border-gray-300 rounded bg-gray-200 text-center" 
                                        value="{{ $result->no_mesin }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Compressor }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Kabel }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Mcb }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Air }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Pompa }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Bersih" {{ $result->Evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                        <option value="Kotor" {{ $result->Evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                        <option value="OFF" {{ $result->Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Suara Halus" {{ $result->Fan_Evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                        <option value="Suara Keras" {{ $result->Fan_Evaporator == 'Suara Keras' ? 'selected' : '' }}>Suara Keras</option>
                                        <option value="OFF" {{ $result->Fan_Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Cukup" {{ $result->Freon == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="Tidak Cukup" {{ $result->Freon == 'Tidak Cukup' ? 'selected' : '' }}>Tidak Cukup</option>
                                        <option value="OFF" {{ $result->Freon == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-full p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Cukup" {{ $result->Air == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="Tidak Cukup" {{ $result->Air == 'Tidak Cukup' ? 'selected' : '' }}>Tidak Cukup</option>
                                        <option value="OFF" {{ $result->Air == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                <label for="keterangan" class="block text-gray-700 font-semibold">Keterangan:</label>
                <textarea id="keterangan" name="keterangan" rows="3" 
                    class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                    placeholder="Tambahkan keterangan jika diperlukan..." readonly>{{ $check->keterangan }}</textarea>
            </div>

            <div class="mt-4 flex justify-between">
                <a href="{{ route('water-chiller.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition duration-200">
                    Kembali
                </a>
                @if(!$check->approved_by)
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition duration-200">
                        Setujui
                    </button>
                @else
                    <a href="{{ route('water-chiller.downloadPdf', $check->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
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