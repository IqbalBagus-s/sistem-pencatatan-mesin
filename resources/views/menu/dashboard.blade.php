<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pencatatan Mesin</title>
    
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
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
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }
        
        /* Main container styling */
        #app-container {
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Full viewport height */
            padding-top: 65px; /* Header height + some padding */
            padding-bottom: 0; /* No bottom padding - footer will add space */
        }
        
        /* Content area that can expand */
        #content-area {
            flex: 1 0 auto; /* Grow but don't shrink */
            width: 100%;
            padding-bottom: 2rem; /* Add some space before footer */
        }
        
        /* Footer fixes */
        #footer {
            flex-shrink: 0; /* Don't let footer shrink */
            width: 100%;
            position: relative; /* Ensure it sits above any content */
            z-index: 10;
        }
        
        .btn-check-machine {
            background-color: #1565c0;
            transition: background-color 0.2s ease;
            display: inline-block;
            width: 100%;
            text-align: center;
        }
        
        .btn-check-machine:hover {
            background-color: #0d47a1;
        }

        /* Notification popup styles - dengan lebar yang ditambah */
        #notification-popup {
            position: fixed;
            top: 90px; /* Positioned just below the header and near the "Hello" text */
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: none;
            min-width: 250px; /* Lebar minimum */
        }
        
        #login-notification {
            background-color: #e6f3ff; /* Light blue background */
            border: 1px solid #1565c0; /* Blue border */
            color: #1565c0; /* Blue text */
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: auto; /* Auto width based on content */
            white-space: nowrap; /* Hindari text wrap */
            text-align: center;
            display: flex;
            align-items: center;
        }
        
        #login-message {
            margin-right: 20px;
            font-size: 0.9rem;
            white-space: nowrap; /* Pastikan teks tidak terpotong */
        }
        
        .close-notification {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
        }
        
        /* Logout button styling */
        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: 1px solid #ef4444;
            border-radius: 6px;
            background-color: transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }
        
        .logout-btn:hover {
            background-color: #ef4444;
        }
        
        .logout-btn:hover svg {
            fill: white;
            stroke: white;
        }
        
        .logout-btn svg {
            fill: none;
            stroke: #ef4444;
            stroke-width: 2;
            transition: all 0.2s ease;
        }
        
        /* Responsive header */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 0.75rem 1rem;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        #currentDateTime {
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Machine card styling */
        .machine-card {
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease;
        }
        
        .machine-card:hover {
            transform: translateY(-3px);
        }
        
        .card-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* Card grid fixes for small devices */
        .machine-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 12px;
            width: 100%;
        }
        
        /* Enhanced responsive grid */
        @media (min-width: 640px) {
            .machine-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
        }
        
        @media (min-width: 768px) {
            .machine-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (min-width: 1024px) {
            .machine-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (min-width: 1280px) {
            .machine-grid {
                grid-template-columns: repeat(5, 1fr);
                gap: 20px;
            }
        }
        
        /* Small device optimizations */
        @media (max-width: 640px) {
            .header-container {
                padding: 0.5rem;
            }
            
            #currentDateTime {
                font-size: 0.8rem;
                max-width: 120px;
            }
            
            .page-title {
                font-size: 1.25rem;
                margin-top: 1rem;
            }
            
            .subtitle {
                font-size: 0.875rem;
            }
            
            .section-title {
                font-size: 1rem;
                margin-top: 1.25rem;
            }
            
            .logo-img {
                height: 2rem;
            }
            
            /* Ensure footer has enough space on small screens */
            #content-area {
                padding-bottom: 1rem;
            }
            
            #app-container {
                padding-top: 55px; /* Smaller header on mobile */
            }
            
            /* Fixed height for machine cards on small screens */
            .machine-card {
                min-height: 90px;
            }
            
            .btn-check-machine {
                padding-top: 0.35rem;
                padding-bottom: 0.35rem;
                font-size: 0.875rem;
            }
            
            /* Improved footer on mobile */
            #footer {
                padding: 0.5rem 0;
            }
            
            #footer p {
                font-size: 0.75rem;
            }
            
            /* Responsive notification on mobile */
            #notification-popup {
                top: 70px;
                max-width: 90%; /* Batasi lebar maksimum pada mobile */
            }
            
            #login-notification {
                padding: 10px 15px;
            }
            
            #login-message {
                font-size: 0.8rem;
                margin-right: 10px;
            }
        }
        
        /* Tablet optimizations */
        @media (min-width: 641px) and (max-width: 1023px) {
            .machine-card {
                min-height: 100px;
            }
            
            #footer {
                padding: 0.75rem 0;
            }
            
            #footer p {
                font-size: 0.875rem;
            }
            
            .section-title {
                margin-top: 1.5rem;
                margin-bottom: 1rem;
            }
        }
        
        /* Extra small device fixes */
        @media (max-width: 359px) {
            .header-container {
                padding: 0.4rem;
            }
            
            .logo-img {
                height: 1.8rem;
            }
            
            #currentDateTime {
                font-size: 0.7rem;
                max-width: 80px;
            }
            
            .logout-btn {
                width: 35px;
                height: 35px;
            }
            
            .logout-btn svg {
                width: 18px;
                height: 18px;
            }
            
            .machine-grid {
                gap: 10px;
            }
            
            .card-content {
                padding: 0.5rem !important;
            }
            
            .card-content h5 {
                font-size: 0.75rem;
                margin-bottom: 0.5rem;
            }
            
            .btn-check-machine {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            #app-container {
                padding-top: 45px;
            }
            
            /* Responsive notification on extra small screens */
            #notification-popup {
                top: 60px;
            }
            
            #login-message {
                font-size: 0.7rem;
                margin-right: 8px;
            }
            
            .close-notification svg {
                width: 14px;
                height: 14px;
            }
        }
        
        /* Sticky footer for larger screens */
        @media (min-height: 900px) {
            #content-area {
                min-height: calc(100vh - 130px); /* Viewport height - (header + footer) */
            }
        }
    </style>
</head>
<body class="bg-blue-50">
    <div id="app-container"> <!-- Main flex container -->
        <!-- Header Fixed -->
        <header class="fixed top-0 left-0 w-full bg-white shadow-md z-50 touch-pan-y">
            <div class="header-container">
                <img src="{{ asset('images/logo.png') }}" alt="ASPRA Logo" class="h-10 logo-img">
                <div class="header-right">
                    <div class="font-medium text-blue-700" id="currentDateTime">
                        <!-- Tanggal dan waktu akan ditampilkan di sini -->
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-btn" title="Logout">
                            <!-- SVG door icon -->
                            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 8l-1.41 1.41L17.17 11H9v2h8.17l-1.58 1.58L17 16l4-4-4-4z"></path>
                                <path d="M5 5h7V3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h7v-2H5V5z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Notification popup - diperlebar agar tidak terpotong -->
        <div id="notification-popup">
            <!-- Login notification (blue) dengan lebar yang cukup -->
            <div id="login-notification">
                <p id="login-message">Anda berhasil login</p>
                <button type="button" class="close-notification">
                    <svg class="w-4 h-4 text-blue-500 hover:text-blue-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div id="content-area">
            <div class="container mx-auto px-3 sm:px-4">
                <h1 class="font-bold text-xl sm:text-2xl page-title mt-3 sm:mt-4">Halo, {{ auth()->user()->username }} 👋</h1>
                <p class="text-gray-500 text-sm sm:text-base subtitle">Anda login sebagai {{ auth()->user() instanceof \App\Models\Approver ? 'Approver' : 'Checker' }}</p>
        
                <h2 class="mt-5 sm:mt-6 text-center font-bold text-lg sm:text-xl section-title">
                    {{ auth()->user() instanceof \App\Models\Approver ? 'Daftar Form Pengajuan Pencatatan Mesin' : 'Daftar Form Pencatatan Mesin' }}
                </h2>
        
                <div class="machine-grid mt-4 sm:mt-6">
                    @php
                        $machines = [
                            ['name' => 'Air Dryer', 'route' => 'air-dryer.index'],
                            ['name' => 'Water Chiller', 'route' => 'water-chiller.index'],
                            ['name' => 'Compressor', 'route' => 'compressor.index'],
                            ['name' => 'Hopper', 'route' => 'hopper.index'],
                            ['name' => 'Dehum Bahan', 'route' => 'dehum-bahan.index'],
                            ['name' => 'Dehum Matras', 'route' => 'dehum-matras.index'],
                            ['name' => 'Auto Loader', 'route' => 'autoloader.index'],
                            ['name' => 'Gilingan', 'route' => 'giling.index'],
                            ['name' => 'Caplining', 'route' => 'caplining.index'],
                            ['name' => 'Vacuum Cleaner', 'route' => 'vacuum-cleaner.index'],
                            ['name' => 'Mesin Sleeting', 'route' => 'slitting.index'],
                            ['name' => 'Crane', 'route' => null]
                        ];
                    @endphp
                    
                    @foreach ($machines as $machine)
                        <div>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden machine-card">
                                <div class="p-2 sm:p-3 md:p-4 card-content">
                                    <h5 class="font-semibold mb-2 sm:mb-3 md:mb-4 text-center text-xs sm:text-sm md:text-base">
                                        {{ $machine['name'] }}
                                    </h5>
                                    @if ($machine['route'])
                                        <a href="{{ route($machine['route']) }}" class="btn-check-machine text-white py-1.5 sm:py-2 px-2 sm:px-3 md:px-4 rounded mt-auto text-xs sm:text-sm md:text-base">Cek Disini</a>
                                    @else
                                        <button class="btn-check-machine text-white py-1.5 sm:py-2 px-2 sm:px-3 md:px-4 rounded mt-auto text-xs sm:text-sm md:text-base">Cek Disini</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Include Footer Component -->
        @include('components.footer')
    </div>

    @vite('resources/js/app.js')
    
    <!-- Script untuk menampilkan tanggal dan waktu terkini dan mengelola notifikasi -->
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
        
        // Responsive adjustments
        function handleResize() {
            const width = window.innerWidth;
            const dateTimeElement = document.getElementById('currentDateTime');
            
            // On very small screens, only show time, not date
            if (width < 360) {
                const now = new Date();
                const time = now.getHours().toString().padStart(2, '0') + ':' + 
                             now.getMinutes().toString().padStart(2, '0');
                dateTimeElement.innerHTML = time;
            } else {
                // Normal display will be handled by updateDateTime
                updateDateTime();
            }
        }
        
        // Menyesuaikan tinggi konten berdasarkan ukuran layar
        function adjustContentHeight() {
            const header = document.querySelector('header');
            const footer = document.getElementById('footer');
            const appContainer = document.getElementById('app-container');
            
            const headerHeight = header.offsetHeight;
            const footerHeight = footer.offsetHeight;
            const windowHeight = window.innerHeight;
            
            // Pastikan content area memiliki ruang yang cukup
            appContainer.style.paddingTop = headerHeight + 'px';
            
            // Atur tinggi minimum content-area untuk menghindari footer melayang
            const contentArea = document.getElementById('content-area');
            contentArea.style.minHeight = (windowHeight - headerHeight - footerHeight) + 'px';
        }
        
        // Mulai saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            updateDateTime();
            handleResize();
            adjustContentHeight();
            
            // Listen for window resize events
            window.addEventListener('resize', function() {
                handleResize();
                adjustContentHeight();
            });

            // Check if user just logged in
            if (localStorage.getItem('just_logged_in') === 'true') {
                // Show login success notification
                const notification = document.getElementById('notification-popup');
                notification.style.display = 'block';
                
                // Remove the flag from localStorage
                localStorage.removeItem('just_logged_in');
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000);
            }
            
            // Add click event to close button
            const closeButtons = document.querySelectorAll('.close-notification');
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('notification-popup').style.display = 'none';
                });
            });

            // Add event listener to the logout form
            document.getElementById('logout-form').addEventListener('submit', function(e) {
                // Store logout status in localStorage before form submission
                localStorage.setItem('just_logged_out', 'true');
            });
        });
    </script>
</body>
</html>