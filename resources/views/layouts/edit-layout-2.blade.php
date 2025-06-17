<!-- resources/views/layouts/edit-layout-2.blade -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PT Asia Pramulia')</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    @yield('styles')
</head>
<body class="bg-sky-50 font-sans flex flex-col min-h-screen">
    <!-- Notifikasi Error - Sama seperti di create-layout-2 -->
    <div id="error-notification" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 {{ session('error') || $errors->any() ? '' : 'hidden' }}">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md flex items-center max-w-md">
            <div class="mr-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-6 text-sm" id="error-message">
                @if(session('error'))
                    {{ session('error') }}
                @elseif($errors->any())
                    {{ $errors->first() }}
                @endif
            </p>
            <button type="button" id="close-notification" class="ml-auto">
                <svg class="w-4 h-4 text-red-500 hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="container mx-auto mt-4 px-4">
            <h2 class="mb-4 text-xl font-bold">@yield('page-title', 'Form Pencatatan')</h2>

            <div class="bg-white rounded-lg shadow-md mb-5">
                <div class="p-4">
                    @hasSection('show-checker')
                    <!-- Menampilkan Nama Checker -->
                    <div class="bg-sky-50 p-4 rounded-md mb-5">
                        <span class="text-gray-600 font-bold">Checker: </span>
                        <span class="font-bold text-blue-700">{{ $user->username ?? $user->name }}</span>
                    </div>
                    @endif

                    <!-- Form Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @yield('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tanggal handler
            if (document.getElementById("tanggal")) {
                document.getElementById("tanggal").addEventListener("change", function() {
                    let tanggal = new Date(this.value);
                    let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
                    document.getElementById("hari").value = hari;
                });
            }
            
            // Error notification handler
            const errorNotification = document.getElementById('error-notification');
            const closeNotification = document.getElementById('close-notification');
            
            // Auto-hide setelah 5 detik jika notifikasi sedang ditampilkan
            if (errorNotification && !errorNotification.classList.contains('hidden')) {
                setTimeout(function() {
                    errorNotification.classList.add('hidden');
                }, 5000);
            }
            
            // Fungsi tombol tutup
            if (closeNotification) {
                closeNotification.addEventListener('click', function() {
                    errorNotification.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>