<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Compressor</title>
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Detail Pencatatan Compressor</h2>
    
        <!-- Tampilan Detail -->
        <div>
            {{-- Informasi Tanggal dan Hari --}}
            <div class="mb-4 grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 font-medium">Hari:</label>
                    <div class="w-full p-2 border border-gray-300 rounded bg-gray-100">{{ $check->hari }}</div>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium">Tanggal:</label>
                    <div class="w-full p-2 border border-gray-300 rounded bg-gray-100">{{ date('d-m-Y', strtotime($check->tanggal)) }}</div>
                </div>
            </div>
    
            <!-- Informasi Shift -->
            <div class="mb-4 p-4 bg-gray-200 rounded">
                <p class="text-lg font-semibold text-gray-700">Informasi Shift Checker</p>
                
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <!-- Shift 1 -->
                    <div class="p-4 bg-white shadow rounded border border-gray-300">
                        <label class="block text-gray-700 font-semibold">Shift 1</label>
                        <div class="mt-2 w-full p-2 border rounded bg-gray-100">{{ $check->checked_by_shift1 ?: 'Belum diisi' }}</div>
                    </div>

                    <!-- Shift 2 -->
                    <div class="p-4 bg-white shadow rounded border border-gray-300">
                        <label class="block text-gray-700 font-semibold">Shift 2</label>
                        <div class="mt-2 w-full p-2 border rounded bg-gray-100">{{ $check->checked_by_shift2 ?: 'Belum diisi' }}</div>
                    </div>
                </div>
            </div>
    
            <!-- Informasi Kompresor ON -->
            <div class="mb-4 p-4 bg-gray-200 rounded shadow-lg">
                <p class="text-lg font-semibold text-gray-700 mb-2">Jumlah Kompresor ON</p>
    
                <div class="grid grid-cols-2 gap-4">
                    <!-- KL -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">KL</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->kompressor_on_kl }}</div>
                    </div>
                    <!-- KH -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">KH</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->kompressor_on_kh }}</div>
                    </div>
                </div>
    
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <!-- Mesin ON -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Mesin ON</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->mesin_on }}</div>
                    </div>
                    <!-- Mesin OFF -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Mesin OFF</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->mesin_off }}</div>
                    </div>
                </div>
            </div>
    
            <!-- Informasi Kelembapan Udara -->
            <div class="mb-4 p-4 bg-gray-200 rounded shadow-lg">
                <p class="text-lg font-semibold text-gray-700 mb-2">Kelembapan Udara</p>
    
                <div class="grid grid-cols-2 gap-4">
                    <!-- Temperatur Shift 1 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Temperatur Shift 1</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->temperatur_shift1 }}</div>
                    </div>
                    <!-- Temperatur Shift 2 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Temperatur Shift 2</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->temperatur_shift2 }}</div>
                    </div>
                    <!-- Humidity Shift 1 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Humidity Shift 1</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->humidity_shift1 }}</div>
                    </div>
                    <!-- Humidity Shift 2 -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Humidity Shift 2</label>
                        <div class="w-full border border-gray-400 p-2 rounded text-center bg-gray-100">{{ $check->humidity_shift2 }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Low Kompressor Table -->
            <div class="text-lg font-semibold mb-4 mt-4">
                Data Low Kompressor
            </div>

            <div class="overflow-x-auto max-h-[500px]">
                <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
                    <thead class="bg-gray-200 text-center sticky top-0 z-10">
                        <tr>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">No.</th>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">Checked Items</th>
                            <th class="border border-gray-300 p-2 sticky top-0" colspan="12">Hasil Pemeriksaan</th>
                        </tr>
                        <tr class="sticky top-[41px] z-10">
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KL 10</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KL 5</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KL 6</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KL 7</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KL 8</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KL 9</th>
                        </tr>
                        <tr class="sticky top-[82px] z-10">
                            @for ($i = 0; $i < 6; $i++)
                                <th class="border border-gray-300 p-2 bg-gray-200">I</th>
                                <th class="border border-gray-300 p-2 bg-gray-200">II</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody class="text-sm text-center">
                        @php
                            $checkedItems = [
                                "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                                "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
                            ];
                        
                            // Kolom KL untuk tabel
                            $klDbColumns = ['kl_10I', 'kl_10II', 'kl_5I', 'kl_5II', 'kl_6I', 'kl_6II', 'kl_7I', 'kl_7II', 'kl_8I', 'kl_8II', 'kl_9I', 'kl_9II'];
                        @endphp
                    
                        @foreach ($lowResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                            @php 
                                $result = $resultGroup->first();
                            @endphp
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 p-2">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 p-2 w-1/8 text-left">{{ $result->checked_items }}</td>
                    
                                @foreach ($klDbColumns as $klColumn)
                                    <td class="border border-gray-300 p-2 w-auto">
                                        {{ $result->$klColumn }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>                    
                </table>
            </div>

            <!-- High Kompressor Table -->
            <div class="text-lg font-semibold mb-4 mt-8">
                Data High Kompressor
            </div>

            <div class="overflow-x-auto max-h-[500px]">
                <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
                    <thead class="bg-gray-200 text-center sticky top-0 z-10">
                        <tr>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">No.</th>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">Checked Items</th>
                            <th class="border border-gray-300 p-2 sticky top-0" colspan="10">Hasil Pemeriksaan</th>
                        </tr>
                        <tr class="sticky top-[41px] z-10">
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KH 7</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KH 8</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KH 9</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KH 10</th>
                            <th class="border border-gray-300 p-2 bg-gray-200" colspan="2">KH 11</th>
                        </tr>
                        <tr class="sticky top-[82px] z-10">
                            @for ($i = 0; $i < 5; $i++)
                                <th class="border border-gray-300 p-2 bg-gray-200">I</th>
                                <th class="border border-gray-300 p-2 bg-gray-200">II</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="text-sm text-center">
                        @php
                            $checkedItems = [
                                "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                                "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
                            ];
                        
                            // Kolom KH untuk tabel
                            $khDbColumns = ['kh_7I', 'kh_7II', 'kh_8I', 'kh_8II', 'kh_9I', 'kh_9II', 'kh_10I', 'kh_10II', 'kh_11I', 'kh_11II'];
                        @endphp
                    
                        @foreach ($highResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                            @php 
                                $result = $resultGroup->first();
                            @endphp
                            <tr class="hover:bg-gray-100">
                                <td class="border border-gray-300 p-2">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 p-2 w-1/8 text-left">{{ $result->checked_items }}</td>
                    
                                @foreach ($khDbColumns as $khColumn)
                                    <td class="border border-gray-300 p-2 w-auto">
                                        {{ $result->$khColumn }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>                    
                </table>
            </div>
            <!-- Menampilkan Pilihan Shift untuk Approver -->
            <div class="mb-4 p-4 bg-gray-200 rounded">
                <p class="text-lg font-semibold text-gray-700">Pilih Shift Approver</p>
                
                <div class="grid grid-cols-2 gap-4 mt-2">
                    <!-- Shift 1 -->
                    <div class="p-4 bg-white shadow rounded border border-gray-300">
                        <label class="block text-gray-700 font-semibold">Shift 1</label>
                        <input type="text" id="approved_by_shift1" name="approved_by_shift1" class="mt-2 w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" 
                            value="{{ $check->approved_by_shift1 }}" readonly>
                        <button type="button" id="btn-shift-1" class="mt-2 w-full bg-blue-500 text-white py-1 px-3 rounded disabled:opacity-50" 
                                onclick="pilihShift(1)" {{ $check->approved_by_shift1 ? 'disabled' : '' }}>Pilih</button>
                    </div>

                    <!-- Shift 2 -->
                    <div class="p-4 bg-white shadow rounded border border-gray-300">
                        <label class="block text-gray-700 font-semibold">Shift 2</label>
                        <input type="text" id="approved_by_shift2" name="approved_by_shift2" class="mt-2 w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" 
                            value="{{ $check->approved_by_shift2 }}" readonly>
                        <button type="button" id="btn-shift-2" class="mt-2 w-full bg-green-500 text-white py-1 px-3 rounded disabled:opacity-50" 
                                onclick="pilihShift(2)" {{ $check->approved_by_shift2 ? 'disabled' : '' }}>Pilih</button>
                    </div>
                </div>

                <!-- Tombol Simpan Persetujuan -->
                <form id="approvalForm" method="POST" action="{{ route('compressor.approve', $check->id) }}">
                    @csrf
                    <input type="hidden" id="shift1" name="shift1" value="{{ $check->approved_by_shift1 }}">
                    <input type="hidden" id="shift2" name="shift2" value="{{ $check->approved_by_shift2 }}">

                    <div class="mt-4 flex justify-between">
                        <!-- Tombol Kembali -->
                        <a href="{{ route('compressor.index') }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">
                            Kembali
                        </a>

                        @if($check->approved_by_shift1 && $check->approved_by_shift2)
                            <!-- Jika kedua shift sudah disetujui, tampilkan tombol Download PDF -->
                            <a href="{{ route('compressor.downloadPdf', $check->id) }}" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                                Download PDF
                            </a>
                            <button type="button" class="bg-gray-400 text-white py-2 px-4 rounded cursor-not-allowed" disabled>
                                Telah Disetujui
                            </button>
                        @else
                            <!-- Jika salah satu shift belum disetujui, tampilkan tombol Simpan Persetujuan -->
                            <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded hover:bg-blue-800">
                                Simpan Persetujuan
                            </button>
                        @endif
                    </div>
                </form>                
            </div>
                
        </div>
    </div>
    
    <script>
        // Fungsi untuk mengisi shift dengan nama user yang sedang login
        function pilihShift(shift) {
            let user = "{{ Auth::user()->username }}"; // Gantilah dengan data user yang sesuai
            if (shift === 1) {
                document.getElementById("approved_by_shift1").value = user;
                document.getElementById("shift1").value = user;
                document.getElementById("btn-shift-1").disabled = true;
            } else if (shift === 2) {
                document.getElementById("approved_by_shift2").value = user;
                document.getElementById("shift2").value = user;
                document.getElementById("btn-shift-2").disabled = true;
            }
        }
    </script>
</body>
</html>