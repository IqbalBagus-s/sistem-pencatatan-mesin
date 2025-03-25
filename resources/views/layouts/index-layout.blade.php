<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pencatatan Mesin')</title>
    
    @vite('resources/css/app.css')
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <!-- Tambahkan di <head> -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    
    <style>
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Regular.ttf') }}") format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Medium.ttf') }}") format('truetype');
            font-weight: 500;
            font-style: normal;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-SemiBold.ttf') }}") format('truetype');
            font-weight: 600;
            font-style: normal;
        }
        @font-face {
            font-family: 'Poppins';
            src: url("{{ asset('fonts/Poppins-Bold.ttf') }}") format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        /* Menambahkan custom colors untuk status dan pagination */
        .bg-approved {
            background-color: #d4edda;
        }
        .text-approvedText {
            color: #155724;
        }
        .bg-pending {
            background-color: #f8d7da;
        }
        .text-pendingText {
            color: #721c24;
        }
        .bg-primary {
            background-color: #1565c0;
        }
        .bg-primaryDark {
            background-color: #0d47a1;
        }
        .text-primary {
            color: #1565c0;
        }
        .border-primary {
            border-color: #1565c0;
        }
        .bg-success {
            background-color: #28a745;
        }
        .bg-successDark {
            background-color: #218838;
        }
        .bg-secondary {
            background-color: #6c757d;
        }
        .search-button {
            width: 120px;
        }
        .focus\:ring-primary:focus {
            --tw-ring-color: #1565c0;
        }
        .focus\:border-primary:focus {
            border-color: #1565c0;
        }
        .hover\:bg-primaryDark:hover {
            background-color: #0d47a1;
        }
        .hover\:bg-successDark:hover {
            background-color: #218838;
        }
        .hover\:bg-gray-600:hover {
            background-color: #4b5563;
        }
        
        @yield('additional-styles'){}
    </style>
</head>
<body class="bg-blue-50 pt-5 font-poppins min-h-screen flex flex-col overscroll-none">

    <div class="container mx-auto px-4 pb-12">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">@yield('page-title', 'Pencatatan Mesin')</h2>

        <!-- Form Pencarian dan Tombol Tambah -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-5 mb-5">
            <form method="GET" action="@yield('form-action')">
                <div class="flex flex-col md:flex-row md:justify-between md:items-end">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end flex-grow">
                        @yield('custom-filters')
                        
                        <div>
                            <button type="submit" class="bg-primary hover:bg-primaryDark text-white py-2 px-4 rounded-md transition duration-200 search-button">Cari</button>
                        </div>
                    </div>
                    
                    @if(auth()->user() instanceof \App\Models\Checker)
                        <div class="mt-4 md:mt-0 flex">
                            <a href="@yield('create-route')" class="bg-success hover:bg-successDark text-white py-2 px-4 rounded-md font-medium transition duration-200">
                                @yield('create-button-text', 'Tambah Pencatatan')
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Tabel Data -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            @yield('table-content')
        </div>
        
        <!-- Pagination -->
        @yield('pagination')

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-4">
            <a href="@yield('back-route', route('dashboard'))" class="px-4 py-2 bg-secondary hover:bg-gray-600 text-white rounded-md transition duration-200">
                Kembali
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto">
        <p class="font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
    @yield('scripts')

    <script>
    // Function to show notification
    function showNotification(message, type = 'success') {
        const popup = document.getElementById('notification-popup');
        const messageEl = document.getElementById('notification-message');
        
        // Reset classes
        popup.classList.remove('notification-success', 'notification-warning');
        
        // Add appropriate class based on type
        popup.classList.add(`notification-${type}`);
        
        messageEl.textContent = message;
        popup.style.display = 'block';
        
        setTimeout(() => {
            popup.style.display = 'none';
        }, 3000);
    }

    // Check for flash messages on page load
    document.addEventListener('DOMContentLoaded', () => {
        const successMessage = "{{ session('success') }}";
        
        if (successMessage) {
            showNotification(successMessage);
        }
    });
</script>
</body>
</html>