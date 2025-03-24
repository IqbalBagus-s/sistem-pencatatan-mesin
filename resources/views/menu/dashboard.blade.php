<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pencatatan Mesin</title>
    
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Font loading fixes */
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Regular.ttf') }}") format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Medium.ttf') }}") format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-SemiBold.ttf') }}") format('truetype');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Bold.ttf') }}") format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        
        /* Ensure font is applied to all elements */
        * {
            font-family: 'Poppins', sans-serif !important;
        }
        
        html, body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }
        
        .btn-check-machine {
            background-color: #1565c0;
        }
        .btn-check-machine:hover {
            background-color: #0d47a1;
        }
    </style>
</head>
<body class="bg-blue-50 pt-20 overflow-y-auto overscroll-y-none flex flex-col min-h-screen">
    <!-- Header Fixed -->
    <header class="fixed top-0 left-0 w-full bg-white shadow-md z-50 flex justify-between items-center p-3 touch-pan-y">
        <img src="{{ asset('images/logo.png') }}" alt="ASPRA Logo" class="h-10">
        <div class="flex items-center">
            <div class="font-medium text-blue-700 mr-3" id="currentDateTime">
                <!-- Tanggal dan waktu akan ditampilkan di sini -->
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 border border-red-500 text-red-500 rounded hover:bg-red-500 hover:text-white transition duration-200">Logout</button>
            </form>
        </div>
    </header>

    <div class="flex-1">
        <div class="container mx-auto px-4 mb-8">
            <h1 class="font-bold text-2xl">Hello, {{ auth()->user()->username }} 👋</h1>
            <p class="text-gray-500">You're login as a {{ auth()->user() instanceof \App\Models\Approver ? 'Approver' : 'Checker' }}</p>
    
            <h2 class="mt-6 text-center font-bold text-xl">
                {{ auth()->user() instanceof \App\Models\Approver ? 'Machines Form Approval List' : 'Machines Form Checking List' }}
            </h2>
    
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
                @php
                    $machines = [
                        ['name' => 'Air Dryer', 'route' => 'air-dryer.index'],
                        ['name' => 'Water Chiller', 'route' => 'water-chiller.index'],
                        ['name' => 'Compressor', 'route' => 'compressor.index'],
                        ['name' => 'Hopper', 'route' => 'hopper.index'],
                        ['name' => 'Dehum Bahan', 'route' => 'compressor.index'],
                        'Mesin C', 'Mesin D',
                        'Mesin E', 'Mesin F', 'Mesin G', 'Mesin H',
                        'Mesin I', 'Mesin J', 'Mesin K', 'Mesin L',
                        'Mesin M', 'Mesin N', 'Mesin O', 'Mesin P'
                    ];
                @endphp
                
                @foreach ($machines as $machine)
                    <div class="mb-4">
                        <div class="bg-white rounded-lg shadow-md text-center overflow-hidden">
                            <div class="p-4 flex flex-col h-full">
                                <h5 class="font-semibold mb-4">
                                    {{ is_array($machine) ? $machine['name'] : $machine }}
                                </h5>
                                @if (is_array($machine))
                                    <a href="{{ route($machine['route']) }}" class="btn-check-machine text-white py-2 px-4 rounded mt-auto hover:bg-blue-800 transition duration-200">Check Here</a>
                                @else
                                    <button class="btn-check-machine text-white py-2 px-4 rounded mt-auto hover:bg-blue-800 transition duration-200">Check Here</button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto">
        <p class="font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
    
    <!-- Script untuk menampilkan tanggal dan waktu terkini -->
    <script>
        function updateDateTime() {
            const now = new Date();
            
            // Format tanggal: DD/MM/YYYY
            const date = now.getDate().toString().padStart(2, '0') + '/' + 
                         (now.getMonth() + 1).toString().padStart(2, '0') + '/' + 
                         now.getFullYear();
            
            // Format waktu: HH:MM:SS
            const time = now.getHours().toString().padStart(2, '0') + ':' + 
                         now.getMinutes().toString().padStart(2, '0') + ':' + 
                         now.getSeconds().toString().padStart(2, '0');
            
            // Update elemen HTML
            document.getElementById('currentDateTime').innerHTML = date + ' ' + time;
            
            // Update setiap detik
            setTimeout(updateDateTime, 1000);
        }
        
        // Mulai saat halaman dimuat
        document.addEventListener('DOMContentLoaded', updateDateTime);

        // Add event listener to the logout form
        document.getElementById('logout-form').addEventListener('submit', function(e) {
            // Store logout status in localStorage before form submission
            localStorage.setItem('just_logged_out', 'true');
        });
    </script>
</body>
</html>