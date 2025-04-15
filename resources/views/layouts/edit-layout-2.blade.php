<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PT Asia Pramulia')</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="bg-sky-50 font-sans flex flex-col min-h-screen">
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
                        <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
                    </div>
                    @endif

                    <!-- Form Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    @include('components.footer')

    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @yield('scripts')
</body>
</html>