<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pencatatan Mesin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-3xl p-8 bg-white rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-700 text-center">Dashboard Pencatatan Mesin</h1>
        <p class="mt-4 text-gray-600 text-center">Selamat datang, {{ auth()->user()->username }}!</p>

        @if(session('success'))
            <div class="mt-4 text-green-600 text-center">{{ session('success') }}</div>
        @endif

        <div class="mt-6">
            @if(auth()->user() instanceof \App\Models\Approver)
                <p class="text-center text-lg font-bold">Anda login sebagai <span class="text-indigo-500">Approver</span></p>
                <a href="#" class="block w-full px-4 py-2 text-center font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 mt-4">
                    Lihat Data Approval
                </a>
            @elseif(auth()->user() instanceof \App\Models\Checker)
                <p class="text-center text-lg font-bold">Anda login sebagai <span class="text-green-500">Checker</span></p>
                <a href="#" class="block w-full px-4 py-2 text-center font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 mt-4">
                    Cek Pencatatan Mesin
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full px-4 py-2 font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600">
                    Logout
                </button>
            </form>

            @if(auth()->user() instanceof \App\Models\Checker)
            <p class="text-center text-lg font-bold">Anda login sebagai <span class="text-green-500">Checker</span></p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <a href="{{ route('air-dryer.index') }}" class="p-6 text-center bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600">
                    Pencatatan Mesin Air Dryer
                </a>
            </div>
            @endif

        </div>
    </div>
</body>
</html>
