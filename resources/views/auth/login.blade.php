<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
</head>
<body class="flex items-center justify-center min-h-screen bg-green-50">
    <div class="w-full max-w-md">
        <div class="p-6 bg-white rounded-lg shadow-md">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-12 mb-2 mx-auto">
            </div>
            <h2 class="text-center text-gray-600 text-2xl mb-6">Login</h2>

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <button type="button" id="togglePassword" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-eye-slash" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <option value="" disabled selected>Pilih posisi</option>
                        <option value="approver">Approver</option>
                        <option value="checker">Checker</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">Sign in</button>
            </form>

            <p class="text-center text-gray-500 text-sm mt-6">2025 © PT Asia Pramulia</p>
        </div>
    </div>

    <!-- Error Modal -->
    @if(session('error'))
    <div id="errorModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl max-w-md w-full">
            <div class="bg-red-600 px-4 py-3 flex justify-between items-center">
                <h5 class="text-white font-medium">Login Error</h5>
                <button type="button" class="text-white hover:text-gray-200" onclick="closeModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div class="px-4 py-3">
                {{ session('error') }}
            </div>
            <div class="px-4 py-3 bg-gray-50 text-right">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md text-sm transition duration-200" onclick="closeModal()">Close</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Logout notification -->
    <div id="logoutNotification" style="display: none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background-color: #2563eb; color: white; padding: 12px 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); z-index: 1000; font-weight: 500;">
        Anda telah logout
    </div>

    @vite('resources/js/app.js')
    <script>
        function closeModal() {
            document.getElementById('errorModal').style.display = 'none';
        }

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
        });

        // Check for logout parameter in URL
        document.addEventListener('DOMContentLoaded', function() {
            // Check if user just logged out
            if (localStorage.getItem('just_logged_out') === 'true') {
                // Show notification
                const notification = document.getElementById('logoutNotification');
                notification.style.display = 'block';
                
                // Remove the flag from localStorage
                localStorage.removeItem('just_logged_out');
                
                // Hide notification after 3 seconds
                setTimeout(function() {
                    notification.style.opacity = '1';
                    notification.style.transition = 'opacity 0.5s ease';
                    
                    setTimeout(function() {
                        notification.style.opacity = '0';
                        setTimeout(function() {
                            notification.style.display = 'none';
                        }, 500);
                    }, 3000);
                }, 100);
            }
        });

        // Add login success handling
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            // Set a flag in localStorage before form submission
            localStorage.setItem('just_logged_in', 'true');
        });
    </script>
</body>
</html>