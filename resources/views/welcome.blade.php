<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pencatatan Mesin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-lg p-8 bg-white rounded-lg shadow-md text-center">
        <h1 class="text-3xl font-bold text-gray-700">Sistem Pencatatan Mesin</h1>
        <p class="mt-4 text-gray-600">Silakan login untuk mengakses sistem.</p>

        <a href="{{ route('login') }}" class="mt-6 inline-block w-full px-4 py-2 font-semibold text-white bg-indigo-500 rounded-lg hover:bg-indigo-600">
            Login
        </a>
    </div>
</body>
</html>
