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

        /* Notification badge styling */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            border: 2px solid white;
            z-index: 10;
        }
        
        .card-header {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .notification-icon {
            position: relative;
            display: inline-block;
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

        /* Responsive notification badge */
        @media (max-width: 640px) {
            .notification-badge {
                width: 16px;
                height: 16px;
                font-size: 0.625rem;
                top: -3px;
                right: -3px;
            }
        }
        
        @media (max-width: 359px) {
            .notification-badge {
                width: 14px;
                height: 14px;
                font-size: 0.5rem;
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
                        <button type="submit" class="logout-btn" title="Logout" onclick="this.disabled=true; this.form.submit();">
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
                <h1 class="font-bold text-xl sm:text-2xl page-title mt-3 sm:mt-4">Halo, {{ $user->username ?? $user->name }} 👋</h1>
                <p class="text-gray-500 text-sm sm:text-base subtitle">Anda login sebagai {{ $currentGuard === 'approver' ? 'Approver' : 'Checker' }}</p>
        
                <h2 class="mt-5 sm:mt-6 text-center font-bold text-lg sm:text-xl section-title">
                    {{ $currentGuard === 'approver' ? 'Daftar Form Pengajuan Pencatatan Mesin' : 'Daftar Form Pencatatan Mesin' }}
                </h2>
        

                <div class="machine-grid mt-4 sm:mt-6">
                    @php
                        $machines = [
                            ['name' => 'Air Dryer', 'route' => 'air-dryer.index', 'key' => 'air_dryer'],
                            ['name' => 'Auto Loader', 'route' => 'autoloader.index', 'key' => 'auto_loader'],
                            ['name' => 'Caplining', 'route' => 'caplining.index', 'key' => 'caplining'],
                            ['name' => 'Compressor', 'route' => 'compressor.index', 'key' => 'compressor'],
                            ['name' => 'Crane Matras', 'route' => 'crane-matras.index', 'key' => 'crane_matras'],
                            ['name' => 'Dehum Bahan', 'route' => 'dehum-bahan.index', 'key' => 'dehum_bahan'],
                            ['name' => 'Dehum Matras', 'route' => 'dehum-matras.index', 'key' => 'dehum_matras'],
                            ['name' => 'Gilingan', 'route' => 'giling.index', 'key' => 'gilingan'],
                            ['name' => 'Hopper', 'route' => 'hopper.index', 'key' => 'hopper'],
                            ['name' => 'Slitting', 'route' => 'slitting.index', 'key' => 'slitting'],
                            ['name' => 'Vacuum Cleaner', 'route' => 'vacuum-cleaner.index', 'key' => 'vacuum_cleaner'],
                            ['name' => 'Water Chiller', 'route' => 'water-chiller.index', 'key' => 'water_chiller']
                        ];
                    @endphp
                    
                    @foreach ($machines as $index => $machine)
                        <div>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden machine-card">
                                <div class="p-2 sm:p-3 md:p-4 card-content">
                                    <div class="card-header">
                                        <h5 class="font-semibold text-center text-xs sm:text-sm md:text-base">
                                            {{ $machine['name'] }}
                                        </h5>
                                        <!-- Ikon notifikasi dengan badge - hanya tampil untuk approver -->
                                        @if($currentGuard === 'approver')
                                            <div class="notification-icon ml-2">
                                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                                </svg>
                                                <!-- Badge notifikasi berdasarkan data real -->
                                                @if(isset($notificationCounts[$machine['key']]) && $notificationCounts[$machine['key']] > 0)
                                                    <span class="notification-badge">{{ $notificationCounts[$machine['key']] }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
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
                @if($currentGuard === 'approver')
                    <!-- Recent Activities -->
                    <div class="mt-8 mb-6">
                        <h3 class="mt-5 sm:mt-6 mb-5 sm:mb-6 text-center font-bold text-lg sm:text-xl section-title">Aktivitas Terbaru</h3>
                        <div class="bg-white rounded-lg shadow overflow-hidden">
                            <div class="p-4">
                                @if(isset($recentActivities) && count($recentActivities) > 0)
                                    <ul class="divide-y divide-gray-200">
                                        @foreach($recentActivities as $activity)
                                            <li class="py-3">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0">
                                                        <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="sm:text-sm md:text-sm text-gray-900">{{ $activity->description }}</p>
                                                        <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-gray-500 text-center py-4">Tidak ada aktivitas terbaru</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
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

        // Fungsi untuk menampilkan notifikasi
        function showNotification() {
            const notification = document.getElementById('notification-popup');
            if (notification) {
                notification.style.display = 'block';
                
                // Auto-hide after 5 seconds
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 5000);
            }
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

            // **Cek session flash untuk notifikasi login**
            @if(session('login_success'))
                showNotification();
            @endif
            
            // Add click event to close button
            const closeButtons = document.querySelectorAll('.close-notification');
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('notification-popup').style.display = 'none';
                });
            });

            // Add event listener to the logout form
            document.getElementById('logout-form').addEventListener('submit', function(e) {
                // Disable button to prevent multiple clicks
                const button = this.querySelector('button');
                button.disabled = true;
            });
        });
    </script>
</body>
</html>