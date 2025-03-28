<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Mesin')</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-sky-50 font-sans">
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
                <form action="@yield('form-action')" method="POST">
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
                        @yield('table-content')
                    </div>

                    <!-- Form Input Keterangan -->
                    <div class="@yield('keterangan-container-class', 'mt-5')">
                        @yield('detail-mesin')
                        
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

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto w-full">
        <p class="mb-0 font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
    <script>
        document.getElementById("tanggal").addEventListener("change", function() {
            let tanggal = new Date(this.value);
            let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
            document.getElementById("hari").value = hari;
        });
    </script>
    
    @yield('additional-scripts')
</body>
</html>