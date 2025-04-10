<!-- resources/views/layouts/app.blade.php -->
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
<body class="bg-sky-50 font-sans">
    <div class="container mx-auto mt-4 px-4">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto w-full">
        <p class="mb-0 font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @yield('scripts')
</body>
</html>