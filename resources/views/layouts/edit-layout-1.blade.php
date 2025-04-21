<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @yield('additional-scripts')
</head>
<body class="bg-gray-100 p-6">

    <div class="max-w-7xl mx-auto bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">@yield('page-title')</h2>

        <!-- Menampilkan Nama Checker -->
        <div class="mb-4 p-4 bg-sky-50 rounded">
            <p class="text-lg"><span class="text-gray-600 font-bold">Checker: </span><span class="font-bold text-blue-700">{{ Auth::user()->username }}</span></p>
        </div>

        <!-- Form Edit -->
        <form action="@yield('form-action')" method="POST">
            @csrf
            @method('PUT')

            <!-- Check if specific date-time fields are provided, otherwise use default -->
            @hasSection('date-time-fields')
                @yield('date-time-fields')
            @else
                <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Check if it's Mesin Giling form -->
                    @if(Route::currentRouteName() === 'giling.edit')
                        <div>
                            <label for="minggu" class="block text-gray-700">Pilih Minggu ke- :</label>
                            <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                                {{ $check->minggu }}
                            </div>
                            <input type="text" id="minggu" name="minggu" value="{{ $check->minggu }}" readonly class="hidden">
                        </div>
                        <div>
                            <label for="bulan" class="block text-gray-700">Bulan:</label>
                            <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                                @php
                                    $date = new DateTime($check->bulan);
                                    $formattedDate = $date->format('F Y');
                                @endphp
                                {{ $formattedDate }}
                            </div>
                            <input type="text" id="bulan" name="bulan" value="{{ $check->bulan }}" readonly class="hidden">
                        </div>
                    @else
                        <div>
                            <label class="block text-gray-700">Hari:</label>
                            <input type="text" name="hari" value="@yield('hari-value')" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                        </div>
                        <div>
                            <label class="block text-gray-700">Tanggal:</label>
                            <input type="date" name="tanggal" value="@yield('tanggal-value')" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Tabel Inspeksi -->
            <div class="overflow-x-auto">
                <!-- Air Dryer Table -->
                @if(View::hasSection('title') && View::yieldContent('title') == 'Edit Pencatatan Mesin Air Dryer')
                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2">No</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Nomor Mesin</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kompresor</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kabel</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur MCB</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Angin In</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Angin Out</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Evaporator</th>
                                <th class="border border-gray-300 bg-sky-50 p-2">Fan Evaporator</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-[140px]">Auto Drain</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $index => $result)
                            <tr>
                                <td class="border border-gray-300 text-center p-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 text-center p-2">
                                    <input type="text" name="nomor_mesin[{{ $result->id }}]" value="{{ $result->nomor_mesin }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" readonly>
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_kompresor[{{ $result->id }}]" value="{{ $result->temperatur_kompresor }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_kabel[{{ $result->id }}]" value="{{ $result->temperatur_kabel }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_mcb[{{ $result->id }}]" value="{{ $result->temperatur_mcb }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_angin_in[{{ $result->id }}]" value="{{ $result->temperatur_angin_in }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_angin_out[{{ $result->id }}]" value="{{ $result->temperatur_angin_out }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <select name="evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                        <option value="Bersih" {{ $result->evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                        <option value="Kotor" {{ $result->evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                        <option value="OFF" {{ $result->evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <select name="fan_evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                        <option value="Suara Halus" {{ $result->fan_evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                        <option value="Suara Kasar" {{ $result->fan_evaporator == 'Suara Kasar' ? 'selected' : '' }}>Suara Kasar</option>
                                        <option value="OFF" {{ $result->fan_evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <select name="auto_drain[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                        <option value="Berfungsi" {{ $result->auto_drain == 'Berfungsi' ? 'selected' : '' }}>Berfungsi</option>
                                        <option value="Tidak Berfungsi" {{ $result->auto_drain == 'Tidak Berfungsi' ? 'selected' : '' }}>Tidak Berfungsi</option>
                                        <option value="OFF" {{ $result->auto_drain == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Detail Mesin for Air Dryer -->
                    <div class="bg-gray-100 p-4 rounded-md mt-4">
                        <h5 class="mb-3 font-medium">Detail Mesin:</h5>
                        <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                        <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                        <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                        <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
                    </div>
                
                <!-- Water Chiller Table -->
                @elseif(View::hasSection('title') && View::yieldContent('title') == 'Edit Pencatatan Mesin Water Chiller')
                    <div class="overflow-x-auto max-h-[500px]">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead class="sticky top-0 z-10 bg-sky-50">
                                <tr>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-12 sticky top-0">NO.</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-20 sticky top-0">No Mesin</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Compressor</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Kabel</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Mcb</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Air</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Pompa</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24 sticky top-0">Evaporator</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-28 sticky top-0">Fan Evaporator</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24 sticky top-0">Freon</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24 sticky top-0">Air</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($results as $index => $result)
                                    <tr class="bg-white">
                                        <td class="border border-gray-300 text-center p-2">{{ $index + 1 }}</td>
                                        <td class="border border-gray-300 text-center p-2">
                                            <input type="text" name="no_mesin[{{ $result->id }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center" 
                                                value="{{ $result->no_mesin }}" readonly>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_1[{{ $result->id }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" value="{{ $result->Temperatur_Compressor }}">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_2[{{ $result->id }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" value="{{ $result->Temperatur_Kabel }}">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_3[{{ $result->id }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" value="{{ $result->Temperatur_Mcb }}">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_4[{{ $result->id }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" value="{{ $result->Temperatur_Air }}">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <input type="text" name="temperatur_5[{{ $result->id }}]" 
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="30°C - 60°C" value="{{ $result->Temperatur_Pompa }}">
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                <option value="Bersih" {{ $result->Evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                                <option value="Kotor" {{ $result->Evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                                <option value="OFF" {{ $result->Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="fan_evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                <option value="Suara Halus" {{ $result->Fan_Evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                                <option value="Suara Keras" {{ $result->Fan_Evaporator == 'Suara Keras' ? 'selected' : '' }}>Suara Keras</option>
                                                <option value="OFF" {{ $result->Fan_Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="freon[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                <option value="Cukup" {{ $result->Freon == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                                <option value="Tidak Cukup" {{ $result->Freon == 'Tidak Cukup' ? 'selected' : '' }}>Tidak Cukup</option>
                                                <option value="OFF" {{ $result->Freon == 'OFF' ? 'selected' : '' }}>OFF</option>
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-2">
                                            <select name="air[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
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

                <!-- Mesin Giling Table -->
                @elseif(View::hasSection('title') && View::yieldContent('title') == 'Edit Pemeriksaan Mesin Giling')
                    <div class="overflow-x-auto max-h-[600px]">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead class="sticky-header">
                                <tr>
                                    <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-5 align-middle">No.</th>
                                    <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-40 align-middle">Checked Items</th>
                                    <th colspan="10" class="border border-gray-300 bg-sky-50 p-2 text-center">HASIL PEMERIKSAAN GILINGAN</th>
                                </tr>
                                <tr>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <th class="border border-gray-300 bg-sky-50 p-2 text-center w-24">G{{ $i }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $items = [
                                        1 => 'Cek Motor Mesin Giling',
                                        2 => 'Cek Vanbelt',
                                        3 => 'Cek Dustcollector',
                                        4 => 'Cek Safety Switch',
                                        5 => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
                                    ];
                                    
                                    // Standard options for all items
                                    $options = ['Baik', 'Jelek', 'OFF', '-'];
                                    
                                    // Map from checked items to snake_case field names
                                    $fieldMappings = [
                                        'Cek Motor Mesin Giling' => 'cek_motor_mesin_giling',
                                        'Cek Vanbelt' => 'cek_vanbelt',
                                        'Cek Dustcollector' => 'cek_dustcollector',
                                        'Cek Safety Switch' => 'cek_safety_switch',
                                        'Cek Ketajaman Pisau Putar dan Pisau Duduk' => 'cek_ketajaman_pisau_putar_dan_pisau_duduk'
                                    ];
                                @endphp
                                
                                @foreach($items as $i => $item)
                                    @php
                                        $fieldName = $fieldMappings[$item];
                                        $result = isset($results[$item]) ? $results[$item] : null;
                                    @endphp
                                    <tr>
                                        <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                        <td class="border border-gray-300 p-2">{{ $item }}</td>
                                        
                                        @for ($g = 1; $g <= 10; $g++)
                                            <td class="border border-gray-300 p-2">
                                                <select name="{{ $fieldName }}[G{{ $g }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                    @foreach($options as $option)
                                                        <option value="{{ $option }}" {{ $result && $result->{"g$g"} == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Detail Mesin for Giling -->
                    <div class="bg-sky-50 p-4 rounded-md md:w-auto mt-4">
                        <h5 class="mb-3 font-medium">Catatan Pemeriksaan:</h5>
                        <p class="mb-1">- Pengecekan Ketajaman Pisau Putar dan Pisau Duduk DIlakukan Pada minggu ke-4 di setiap bulannya</p>
                    </div>
                @else
                    <!-- Default Table Content -->
                    @yield('table-content')
                @endif
            </div>

            <!-- Form Input Keterangan -->
            <div class="mt-5">
                <label for="keterangan" class="block mb-2 font-medium">Keterangan:</label>
                <textarea id="keterangan" name="keterangan" rows="4"
                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" 
                placeholder="Tambahkan keterangan jika diperlukan...">@yield('keterangan-value')</textarea>
            </div>

            <!-- Tombol Kembali dan Simpan -->
            <div class="mt-6 flex flex-col sm:flex-row justify-between gap-2">
                <a href="@yield('back-route')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-center">
                    Kembali
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    @vite('resources/js/app.js')
</body>
</html>