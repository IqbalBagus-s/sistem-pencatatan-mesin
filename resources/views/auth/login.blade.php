<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Login</h2>

        @if(session('error'))
            <div class="mb-4 text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 mt-2 border rounded-lg focus:ring focus:ring-indigo-200" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 mt-2 border rounded-lg focus:ring focus:ring-indigo-200" required>
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" class="w-full px-4 py-2 mt-2 border rounded-lg focus:ring focus:ring-indigo-200">
                    <option value="approver">Approver</option>
                    <option value="checker">Checker</option>
                </select>
            </div>

            <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-indigo-500 rounded-lg hover:bg-indigo-600 mt-4">
                Login
            </button>
        </form>
    </div>
</body>
</html>
