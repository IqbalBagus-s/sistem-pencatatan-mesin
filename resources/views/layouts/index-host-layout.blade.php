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
        /* Perbaikan untuk button styling */
        .action-button {
            min-width: 140px;
            padding: 0.5rem 1rem;
            text-align: center;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: background-color 0.2s;
            display: inline-block;
            white-space: nowrap;
        }
        
        /* Spesifik untuk tombol cari */
        .search-button {
            background-color: #1565c0;
            color: white;
            min-width: 140px;
            max-width: 140px;
        }
        
        .search-button:hover {
            background-color: #0d47a1;
        }
        
        /* Spesifik untuk tombol tambah */
        .add-button {
            background-color: #28a745;
            color: white;
            min-width: 180px;
            max-width: 180px;
        }
        
        .add-button:hover {
            background-color: #218838;
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
        
        /* Responsive table styles */
        @media (max-width: 768px) {
            .responsive-table {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Optional: Make table rows appear as cards on mobile */
            .card-table-mobile tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.5rem;
                padding: 1rem;
            }
            
            .card-table-mobile tbody td {
                display: flex;
                justify-content: space-between;
                text-align: right;
                padding: 0.5rem 0;
                border-bottom: 1px solid #e2e8f0;
            }
            
            .card-table-mobile tbody td:last-child {
                border-bottom: none;
            }
            
            .card-table-mobile tbody td:before {
                content: attr(data-label);
                font-weight: 600;
                text-align: left;
            }
            
            /* Memperbaiki tombol pada layar kecil untuk tetap konsisten */
            .button-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-end;
                gap: 0.5rem;
            }
            
            .button-container .action-button {
                flex: 0 0 auto;
                width: auto;
            }
        }

        /* Improved notification positioning for mobile */
        #notification-popup {
            width: 90%;
            max-width: 450px;
        }
        
        @yield('additional-styles');
    </style>
</head>
<body class="bg-blue-50 pt-5 font-poppins min-h-screen flex flex-col overscroll-none">

    <div class="container mx-auto px-3 sm:px-4 pb-12">
    <!-- Notification popup with success, error, and warning variants -->
    <div id="notification-popup" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50 hidden">
        <!-- Success notification (green) -->
        <div id="success-notification" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-md flex items-center w-full">
            <div class="mr-2 flex-shrink-0">
                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-2 text-sm flex-grow" id="success-message">Data berhasil disimpan.</p>
            <button type="button" class="flex-shrink-0 close-notification">
                <svg class="w-4 h-4 text-green-500 hover:text-green-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        
        <!-- Error notification (red) -->
        <div id="error-notification" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md items-center w-full hidden">
            <div class="mr-2 flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-2 text-sm flex-grow" id="error-message">There was a problem sending your mail. Please try again.</p>
            <button type="button" class="flex-shrink-0 close-notification">
                <svg class="w-4 h-4 text-red-500 hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        
        <!-- Warning notification (yellow) -->
        <div id="warning-notification" class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded shadow-md flex items-center w-full">
            <div class="mr-2 flex-shrink-0">
                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <p class="mr-2 text-sm flex-grow" id="warning-message">Peringatan: Silakan periksa data Anda.</p>
            <button type="button" class="flex-shrink-0 close-notification">
                <svg class="w-4 h-4 text-yellow-500 hover:text-yellow-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>

        <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-gray-900">@yield('page-title', 'Pencatatan Mesin')</h2>

        <!-- Form Pencarian dan Tombol Tambah -->
        <div class="bg-white rounded-lg shadow-md p-3 sm:p-4 md:p-5 mb-4 sm:mb-5">
            <form method="GET" action="@yield('form-action')" autocomplete="off">
                <div class="flex flex-col space-y-4">
                    <!-- Filter section -->
                    <div>
                        @yield('custom-filters') 
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Tabel Data - with responsive wrapper -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="responsive-table">
                @yield('table-content')
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            @yield('pagination')
        </div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-4">
            <a href="@yield('back-route', route('dashboard'))" class="px-4 py-2 bg-secondary hover:bg-gray-600 text-white rounded-md transition duration-200 inline-block">
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
            
            // Initialize Select2 with responsive settings
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2({
                    width: '100%',
                    dropdownAutoWidth: true
                });
            }
        });
        
        // Add responsive handling for tables
        document.addEventListener('DOMContentLoaded', function() {
            // Check for tables that need to be transformed on mobile
            const tables = document.querySelectorAll('.card-table-mobile');
            
            if (tables.length > 0) {
                tables.forEach(table => {
                    const headers = table.querySelectorAll('thead th');
                    const headerTexts = Array.from(headers).map(header => header.textContent.trim());
                    
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        cells.forEach((cell, index) => {
                            if (index < headerTexts.length) {
                                cell.setAttribute('data-label', headerTexts[index]);
                            }
                        });
                    });
                });
            }
        });
    </script>
</body>
</html>