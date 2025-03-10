<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pencatatan Mesin Water Chiller</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <div class="mb-4 grid grid-cols-2 gap-4">
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
                            <th class="border border-gray-300 p-2">No</th>
                            <th class="border border-gray-300 p-2" style="width: 200px; min-width: 200px;">ITEM YANG DIPERIKSA</th>
                            <th class="border border-gray-300 p-2" style="width: 130px; min-width: 130px;">STANDART</th>
                            @for ($i = 1; $i <= 32; $i++)
                                <th class="border border-gray-300 p-2 text-center" style="width: 80px; min-width: 100px;">CH{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                            <tr class="bg-white">
                                <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" name="checked_items[{{ $result->id }}]" 
                                        class="w-full p-1 border border-gray-300 rounded bg-gray-200 text-center"
                                        value="{{ $result->checked_items }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" 
                                        class="w-full p-1 border border-gray-300 rounded bg-gray-200 text-center"
                                        value="{{ $result->standart }}" readonly>
                                </td>
                                @for ($j = 1; $j <= 32; $j++)
                                @php 
                                    $key = "CH{$j}"; 
                                    $value = $result->$key ?? '-';
                                    $isChecked = ($value == 'Bersih' || $value == 'Cukup') ? 'checked' : '';
                                    $checkboxValue = in_array($result->checked_items, ['Evaporator', 'Fan Evaporator']) ? 'Bersih' : 'Cukup';
                                    $hiddenValue = in_array($result->checked_items, ['Evaporator', 'Fan Evaporator']) ? 'Kotor' : 'Kurang';
                                @endphp
                                <td class="border border-gray-300 p-2 text-center">
                                    @if (in_array($result->checked_items, ['Evaporator', 'Fan Evaporator', 'Freon', 'Air']))
                                        <!-- Hidden input untuk menyimpan nilai default (Kotor / Kurang) -->
                                        <input type="hidden" name="{{ $key }}[{{ $result->id }}]" value="{{ $hiddenValue }}">
                                        <!-- Checkbox untuk mengubah nilai -->
                                        <input type="checkbox" name="{{ $key }}[{{ $result->id }}]" value="{{ $checkboxValue }}" class="form-check-input"
                                            {{ $isChecked }}>
                                    @elseif ($index >= 5)
                                        <!-- Baris 6-9 berupa checkbox (tidak wajib diisi) -->
                                        <input type="checkbox" name="{{ $key }}[{{ $result->id }}]" value="✔" class="w-5 h-5 border border-gray-300 rounded"
                                            {{ $value == '✔' ? 'checked' : '' }}>
                                    @else
                                        <!-- Input teks wajib diisi -->
                                        <input type="text" name="{{ $key }}[{{ $result->id }}]" 
                                            class="w-full p-1 border border-gray-300 rounded text-center" value="{{ $value }}" required>
                                    @endif
                                </td>
                                @endfor
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

            <div class="mt-4 flex justify-between">
                <a href="{{ route('water-chiller.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                    Kembali
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const standardValues = [
                "30 °C - 60 °C",
                "30 °C - 45 °C",
                "30 °C - 50 °C",
                "Sesuai Setelan",
                "40 °C - 50 °C",
                "Bersih",
                "Suara Halus",
                "Cukup",
                "Cukup"
            ];
    
            const standardInputs = document.querySelectorAll('td:nth-child(3) input');
            
            standardInputs.forEach((input, index) => {
                if (standardValues[index]) {
                    input.value = standardValues[index];
                }
            });
        });
    </script>
    

</body>
</html>
