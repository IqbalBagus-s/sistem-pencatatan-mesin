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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    
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
        
        @yield('additional-styles');
    </style>
</head>
<body class="bg-blue-50 pt-5 font-poppins min-h-screen flex flex-col overscroll-none">

    <div class="container mx-auto px-4 pb-12">
    <!-- Notification popup with success, error, and warning variants -->
    <div id="notification-popup" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 hidden">
        <!-- Success notification (green) -->
        <div id="success-notification" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-md flex items-center max-w-md hidden">
            <div class="mr-2">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-6 text-sm" id="success-message">Data berhasil disimpan.</p>
            <button type="button" class="ml-auto close-notification">
                <svg class="w-4 h-4 text-green-500 hover:text-green-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        
        <!-- Error notification (red) -->
        <div id="error-notification" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md flex items-center max-w-md hidden">
            <div class="mr-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-6 text-sm" id="error-message">There was a problem sending your mail. Please try again.</p>
            <button type="button" class="ml-auto close-notification">
                <svg class="w-4 h-4 text-red-500 hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        
        <!-- Warning notification (yellow) -->
        <div id="warning-notification" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded shadow-md flex items-center max-w-md hidden">
            <div class="mr-2">
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-6 text-sm" id="warning-message">Peringatan: Silakan periksa data Anda.</p>
            <button type="button" class="ml-auto close-notification">
                <svg class="w-4 h-4 text-yellow-500 hover:text-yellow-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>

        <h2 class="text-2xl font-bold mb-6 text-gray-900">@yield('page-title', 'Pencatatan Mesin')</h2>

        <!-- Form Pencarian dan Tombol Tambah -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-5 mb-5">
            <form method="GET" action="@yield('form-action')" autocomplete="off">
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

    <!-- Include Footer Component -->
    @include('components.footer')

    @vite('resources/js/app.js')
    @yield('scripts')
    <script>
        // Function to show notification
        function showNotification(message, type = 'success') {
            const popup = document.getElementById('notification-popup');
            const successNotification = document.getElementById('success-notification');
            const errorNotification = document.getElementById('error-notification');
            const warningNotification = document.getElementById('warning-notification');
            
            // Hide all notifications first
            successNotification.classList.add('hidden');
            errorNotification.classList.add('hidden');
            warningNotification.classList.add('hidden');
            
            // Show the popup container
            popup.classList.remove('hidden');
            
            // Show the appropriate notification type
            if (type === 'success') {
                document.getElementById('success-message').textContent = message;
                successNotification.classList.remove('hidden');
            } else if (type === 'error') {
                document.getElementById('error-message').textContent = message;
                errorNotification.classList.remove('hidden');
            } else if (type === 'warning') {
                document.getElementById('warning-message').textContent = message;
                warningNotification.classList.remove('hidden');
            }
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                popup.classList.add('hidden');
            }, 5000);
        }

        // Close button handling
        document.addEventListener('DOMContentLoaded', () => {
            // Add click event to all close buttons
            const closeButtons = document.querySelectorAll('.close-notification');
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('notification-popup').classList.add('hidden');
                });
            });
            
            // Check for flash messages on page load
            const successMessage = "{{ session('success') }}";
            const errorMessage = "{{ session('error') }}";
            const warningMessage = "{{ session('warning') }}";
            
            if (successMessage) {
                showNotification(successMessage, 'success');
            }
            
            if (errorMessage) {
                showNotification(errorMessage, 'error');
            }
            
            if (warningMessage) {
                showNotification(warningMessage, 'warning');
            }
        });
    </script>
</body>
</html>