<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pencatatan Mesin Water Chiller</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Pencatatan Mesin Water Chiller</h2>

        <!-- Menampilkan Nama Checker -->
        <div class="mb-4 p-4 bg-gray-200 rounded">
            <p class="text-lg font-semibold text-gray-700">Checker: <span class="text-blue-600">{{ Auth::user()->username }}</span></p>
        </div>

        <!-- Form Edit -->
        <form action="{{ route('water-chiller.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Hari:</label>
                    <input type="text" name="hari" value="{{ $check->hari }}" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                </div>
                <div>
                    <label class="block text-gray-700">Tanggal:</label>
                    <input type="date" name="tanggal" value="{{ $check->tanggal }}" class="w-full p-2 border border-gray-300 rounded" readonly>
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
                    <tbody>
                        @foreach($results as $index => $result)
                            <tr class="bg-white">
                                <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="no_mesin[{{ $result->id }}]" 
                                        class="w-full p-1 border border-gray-300 rounded bg-gray-200 text-center" 
                                        value="{{ $result->no_mesin }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="temperatur_1[{{ $result->id }}]" 
                                        class="w-full p-1 border border-gray-300 rounded text-center" 
                                        value="{{ $result->Temperatur_Compressor }}" required>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="temperatur_2[{{ $result->id }}]" 
                                        class="w-full p-1 border border-gray-300 rounded text-center" 
                                        value="{{ $result->Temperatur_Kabel }}" required>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="temperatur_3[{{ $result->id }}]" 
                                        class="w-full p-1 border border-gray-300 rounded text-center" 
                                        value="{{ $result->Temperatur_Mcb }}" required>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="temperatur_4[{{ $result->id }}]" 
                                        class="w-full p-1 border border-gray-300 rounded text-center" 
                                        value="{{ $result->Temperatur_Air }}" required>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="temperatur_5[{{ $result->id }}]" 
                                        class="w-full p-1 border border-gray-300 rounded text-center" 
                                        value="{{ $result->Temperatur_Pompa }}" required>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="evaporator[{{ $result->id }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                        <option value="Bersih" {{ $result->Evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                        <option value="Kotor" {{ $result->Evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                        <option value="OFF" {{ $result->Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="fan_evaporator[{{ $result->id }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                        <option value="Suara Halus" {{ $result->Fan_Evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                        <option value="Suara Keras" {{ $result->Fan_Evaporator == 'Suara Keras' ? 'selected' : '' }}>Suara Keras</option>
                                        <option value="OFF" {{ $result->Fan_Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="freon[{{ $result->id }}]" class="w-full p-1 border border-gray-300 rounded text-center">
                                        <option value="Cukup" {{ $result->Freon == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="Tidak Cukup" {{ $result->Freon == 'Tidak Cukup' ? 'selected' : '' }}>Tidak Cukup</option>
                                        <option value="OFF" {{ $result->Freon == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select name="air[{{ $result->id }}]" class="w-full p-1 border border-gray-300 rounded text-center">
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

            <!-- Form Input Keterangan -->
            <div class="mt-4">
                <label for="keterangan" class="block text-gray-700 font-semibold">Keterangan:</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    class="w-full p-2 border border-gray-300 rounded" 
                    placeholder="Tambahkan keterangan jika diperlukan...">{{ $check->keterangan }}</textarea>
            </div>

            <div class="mt-4 flex flex-col sm:flex-row justify-between gap-2">
                <a href="{{ route('water-chiller.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded text-center">
                    Kembali
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</body>
</html>