<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media (max-width: 640px) {
            .login-container {
                width: 90%;
                margin: 0 auto;
                padding: 1rem;
            }
        }
        
        @media (min-width: 641px) and (max-width: 1024px) {
            .login-container {
                width: 70%;
                max-width: 500px;
            }
        }
        
        @media (min-width: 1025px) {
            .login-container {
                width: 100%;
                max-width: 450px;
            }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-green-50 px-4 py-6">
    <!-- Notification popup -->
    <div id="notification-popup" class="fixed top-5 left-1/2 transform -translate-x-1/2 z-50" style="display: none;">
        <!-- Logout notification (red) -->
        <div id="logout-notification" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-md flex items-center max-w-md">
            <p class="mr-6 text-sm" id="notification-message">Anda telah logout</p>
            <button type="button" class="ml-auto close-notification">
                <svg class="w-4 h-4 text-red-500 hover:text-red-700" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="login-container w-full">
        <div class="p-4 md:p-6 bg-white rounded-lg shadow-md">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-10 sm:h-12 mb-2 mx-auto">
            </div>
            <h2 class="text-center text-gray-600 text-xl sm:text-2xl mb-4 md:mb-6">Login</h2>

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-5 relative">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                    <div class="relative">
                        <select id="role" name="role" class="w-full px-3 py-2 appearance-none border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <option value="" disabled selected>Pilih posisi</option>
                            <option value="approver">Approver</option>
                            <option value="checker">Checker</option>
                        </select>
                        <div class="pointer-events-none absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">Sign in</button>
            </form>

            <p class="text-center text-gray-500 text-xs sm:text-sm mt-5">2025 © PT Asia Pramulia</p>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script>
        // Toggle password visibility
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle icon
                if (type === 'password') {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                } else {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                }
            });
            
            // Display error notification if there's an error in session
            @if(session('error'))
                showNotification("{{ session('error') }}");
            @endif
            
            // Check if user just logged out
            if (localStorage.getItem('just_logged_out') === 'true') {
                // Show notification
                showNotification("Anda telah logout");
                
                // Remove the flag from localStorage
                localStorage.removeItem('just_logged_out');
            }
        });

        // Function to show notification
        function showNotification(message) {
            const notification = document.getElementById('notification-popup');
            const notificationMessage = document.getElementById('notification-message');
            
            // Update message text
            notificationMessage.textContent = message;
            
            // Show notification
            notification.style.display = 'block';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.style.display = 'none';
            }, 5000);
        }
        
        // Add click event to close button
        document.addEventListener('DOMContentLoaded', function() {
            const closeButtons = document.querySelectorAll('.close-notification');
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('notification-popup').style.display = 'none';
                });
            });
        });

        // Add login success handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Set a flag in localStorage before form submission
            localStorage.setItem('just_logged_in', 'true');
        });
    </script>
</body>
</html>