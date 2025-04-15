<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
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
                    <div>
                        <label class="block text-gray-700">Hari:</label>
                        <input type="text" name="hari" value="@yield('hari-value')" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-700">Tanggal:</label>
                        <input type="date" name="tanggal" value="@yield('tanggal-value')" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                    </div>
                </div>
            @endif

            <!-- Tabel Inspeksi -->
            <div class="overflow-x-auto">
                @yield('table-content')
            </div>

            <!-- Form Input Keterangan -->
            <div class="mt-5">
                <label for="keterangan" class="block mb-2 font-medium">Keterangan:</label>
                <textarea id="keterangan" name="keterangan" rows="4"
                class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" 
                placeholder="Tambahkan keterangan jika diperlukan...">{{ $check->keterangan ?? '' }}</textarea>
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

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-6 w-full">
        <p class="font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
</body>
</html>