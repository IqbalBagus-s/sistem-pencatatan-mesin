<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Mesin')</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    <style>
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
        }
    </style>
    @yield('additional-styles')
</head>
<body class="bg-sky-50 font-sans">
    <!-- Notification Popup -->
    <div id="error-notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 hidden">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md flex items-center max-w-md">
            <div class="mr-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-6 text-sm" id="error-message">There was a problem sending your mail. Please try again.</p>
            <button type="button" id="close-notification" class="ml-auto">
                <svg class="w-4 h-4 text-red-500 hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
    <div class="container mx-auto mt-4 px-4">
        <h2 class="mb-4 text-xl font-bold">@yield('page-title', 'Pencatatan Mesin')</h2>

        <div class="bg-white rounded-lg shadow-md mb-5">
            <div class="p-4">
                <!-- Menampilkan Nama Checker -->
                <div class="bg-sky-50 p-4 rounded-md mb-5">
                    <span class="text-gray-600 font-bold">Checker: </span>
                    <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
                </div>

                <!-- Form Input -->
                <form action="@yield('form-action')" method="POST" id="air-dryer-form">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block mb-2">Hari:</label>
                            <input type="text" id="hari" name="hari" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
                        </div>
                        <div>
                            <label class="block mb-2">Tanggal:</label>
                            <input type="date" id="tanggal" name="tanggal" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" required>
                        </div>
                    </div>

                    <!-- Tabel Inspeksi -->
                    <div class="overflow-x-auto">
                        @if(View::hasSection('air-dryer-table'))
                            <!-- Air Dryer Table -->
                            <div class="overflow-x-auto max-h-[500px]">
                                <table class="w-full border-collapse border border-gray-300">
                                    <thead class="sticky top-0 z-10 bg-sky-50">
                                        <tr>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">No</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Nomor Mesin</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Kompresor</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Kabel</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur MCB</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Angin In</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Angin Out</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Evaporator</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Fan Evaporator</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-[140px] sticky top-0">Auto Drain</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 1; $i <= 8; $i++)
                                            <tr>
                                                <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                                <td class="border border-gray-300 text-center p-2">
                                                    <input type="text" name="nomor_mesin[{{ $i }}]" value="AD{{ $i }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" readonly>
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_kompresor[{{ $i }}]" value="{{ old("temperatur_kompresor.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_kabel[{{ $i }}]" value="{{ old("temperatur_kabel.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_mcb[{{ $i }}]" value="{{ old("temperatur_mcb.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_angin_in[{{ $i }}]" value="{{ old("temperatur_angin_in.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_angin_out[{{ $i }}]" value="{{ old("temperatur_angin_out.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                                                        <option value="Bersih">Bersih</option>
                                                        <option value="Kotor">Kotor</option>
                                                        <option value="OFF">OFF</option>
                                                    </select>
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                                                        <option value="Suara Halus">Suara Halus</option>
                                                        <option value="Suara Kasar">Suara Kasar</option>
                                                        <option value="OFF">OFF</option>
                                                    </select>
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <select name="auto_drain[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                                                        <option value="Berfungsi">Berfungsi</option>
                                                        <option value="Tidak Berfungsi">Tidak Berfungsi</option>
                                                        <option value="OFF">OFF</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @elseif(View::hasSection('water-chiller-table'))
                            <!-- Water Chiller Table -->
                            <div class="overflow-x-auto max-h-[600px]">
                                <table class="w-full border-collapse border border-gray-300">
                                    <thead class="sticky-header">
                                        <tr>
                                            <th class="border border-gray-300 bg-sky-50 p-2 w-12">NO.</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 w-20">No Mesin</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Compressor</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kabel</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Mcb</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Air</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Pompa</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 w-24">Evaporator</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 w-28">Fan Evaporator</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 w-24">Freon</th>
                                            <th class="border border-gray-300 bg-sky-50 p-2 w-24">Air</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 1; $i <= 32; $i++)
                                            <tr>
                                                <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                                <td class="border border-gray-300 text-center p-2">
                                                    <input type="text" name="no_mesin[{{ $i }}]" 
                                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center" 
                                                        value="CH{{ $i }}" readonly>
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_1[{{ $i }}]" 
                                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                        placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_2[{{ $i }}]" 
                                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                        placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_3[{{ $i }}]" 
                                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                        placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_4[{{ $i }}]" 
                                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                        placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <input type="text" name="temperatur_5[{{ $i }}]" 
                                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                        placeholder="30°C - 60°C">
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                        <option value="Bersih">Bersih</option>
                                                        <option value="Kotor">Kotor</option>
                                                        <option value="OFF">OFF</option>
                                                    </select>
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                        <option value="Suara Halus">Suara Halus</option>
                                                        <option value="Suara Keras">Suara Keras</option>
                                                        <option value="OFF">OFF</option>
                                                    </select>
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <select name="freon[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                        <option value="Cukup">Cukup</option>
                                                        <option value="Tidak Cukup">Tidak Cukup</option>
                                                        <option value="OFF">OFF</option>
                                                    </select>
                                                </td>
                                                <td class="border border-gray-300 p-2">
                                                    <select name="air[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                        <option value="Cukup">Cukup</option>
                                                        <option value="Tidak Cukup">Tidak Cukup</option>
                                                        <option value="OFF">OFF</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                        @else
                            @yield('table-content')
                        @endif
                    </div>

                    <!-- Form Input Keterangan -->
                    <div class="@yield('keterangan-container-class', 'mt-5')">
                        @hasSection('detail-mesin')
                            <!-- Detail Mesin Section for Air Dryer -->
                            <div class="bg-gray-100 p-4 rounded-md md:w-auto">
                                <h5 class="mb-3 font-medium">Detail Mesin:</h5>
                                <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                                <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                                <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                                <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
                            </div>
                        @endif
                        
                        <div class="@yield('keterangan-class', 'flex-1')">
                            <label for="keterangan" class="block mb-2 font-medium">Keterangan:</label>
                            <textarea id="keterangan" name="keterangan" rows="@yield('keterangan-rows', '3')"
                                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" 
                                placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <a href="@yield('back-route')" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Kembali</a>
                        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.footer')

    @vite('resources/js/app.js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Existing code for date handling
            if (document.getElementById("tanggal")) {
                document.getElementById("tanggal").addEventListener("change", function() {
                    let tanggal = new Date(this.value);
                    let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
                    document.getElementById("hari").value = hari;
                });
            }
            
            // Error notification handling
            const errorNotification = document.getElementById('error-notification');
            const closeNotification = document.getElementById('close-notification');
            const errorMessage = document.getElementById('error-message');
            
            // Check if there's an error or warning message from the session
            const sessionError = "{{ session('error') }}";
            const sessionWarning = "{{ session('warning') }}";
            
            if (sessionError && sessionError.trim() !== '') {
                // Update the error message text
                errorMessage.textContent = sessionError;
                
                // Show the notification
                errorNotification.classList.remove('hidden');
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    errorNotification.classList.add('hidden');
                }, 5000);
            } else if (sessionWarning && sessionWarning.trim() !== '') {
                // Update the error message text
                errorMessage.textContent = sessionWarning;
                
                // Show the notification
                errorNotification.classList.remove('hidden');
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    errorNotification.classList.add('hidden');
                }, 5000);
            }
            
            // Close button functionality
            if (closeNotification) {
                closeNotification.addEventListener('click', function() {
                    errorNotification.classList.add('hidden');
                });
            }
        });
    </script>
    
    @yield('additional-scripts')
</body>
</html>