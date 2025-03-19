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
        <h2 class="mb-4 font-bold text-xl text-center">Approval Pencatatan Mesin Water Chiller</h2>
    
        <div class="mb-4 p-3 bg-gray-100 rounded-lg">
            <p class="text-lg font-semibold">Approver: 
                <span class="text-blue-600">
                    {{ $check->approved_by ? $check->approved_by : Auth::user()->username }}
                </span>
            </p>
        </div>
    
        <div class="mb-4 p-3 bg-gray-100 rounded-lg">
            <p class="text-lg font-semibold">Checker: 
                <span class="text-green-600">{{ $check->checked_by }}</span>
            </p>
        </div>
    
        <div class="mb-3">
            <label class="block font-medium mb-1">Tanggal:</label>
            <input type="date" value="{{ $check->tanggal }}" 
                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
        </div>
    
        <div class="mb-3">
            <label class="block font-medium mb-1">Hari:</label>
            <input type="text" value="{{ $check->hari }}" 
                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
        </div>
    
        <!-- Tabel hanya untuk tampilan -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 p-1 w-10">No</th>
                        <th class="border border-gray-300 p-1 w-24">Nomor Mesin</th>
                        <th class="border border-gray-300 p-1 w-20">Temperatur Kompresor</th>
                        <th class="border border-gray-300 p-1 w-20">Temperatur Kabel</th>
                        <th class="border border-gray-300 p-1 w-20">Temperatur MCB</th>
                        <th class="border border-gray-300 p-1 w-20">Temperatur Air</th>
                        <th class="border border-gray-300 p-1 w-20">Temperatur Pompa</th>
                        <th class="border border-gray-300 p-1 w-20">Evaporator</th>
                        <th class="border border-gray-300 p-1 w-20">Fan Evaporator</th>
                        <th class="border border-gray-300 p-1 w-20">Freon</th>
                        <th class="border border-gray-300 p-1 w-20">Air</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $index => $result)
                        <tr class="bg-white">
                            <td class="border border-gray-300 p-1 text-center w-10">{{ $index + 1 }}</td>
                            <td class="border border-gray-300 p-1 text-center w-24">{{ $result->no_mesin }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Compressor }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Kabel }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Mcb }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Air }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Pompa }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Evaporator }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Fan_Evaporator }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Freon }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Air }}</td>
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
    
        <form action="{{ route('water-chiller.approve', $check->id) }}" method="POST" class="mt-4">
            @csrf
            <div class="flex justify-between">
                <a href="{{ route('water-chiller.index') }}" 
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition duration-200">
                    Kembali
                </a>
                @if(!$check->approved_by)
                    <button type="submit" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition duration-200">
                        Setujui
                    </button>
                @else
                    <a href="{{ route('water-chiller.downloadPdf', $check->id) }}" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
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