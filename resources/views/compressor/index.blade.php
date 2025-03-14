<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Mesin Kompresor</title>
    
    <!-- Tailwind CSS -->
    @vite('resources/css/app.css')

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

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #e6f2ff;
            padding-top: 80px;
        }
    </style>
</head>
<body class="overflow-y-auto">

    <!-- Header -->
    <header class="fixed top-0 left-0 w-full bg-white shadow-md flex justify-between items-center px-6 py-3 z-50">
        <img src="{{ asset('images/logo.png') }}" alt="ASPRA Logo" class="h-10">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                Logout
            </button>
        </form>
    </header>

    <div class="container mx-auto mt-0 p-3">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Pencatatan Mesin Kompresor</h2>

        <!-- Form Pencarian dan Tombol Tambah -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('compressor.index') }}">
                <div class="flex flex-wrap gap-4">
                    @if(auth()->user() instanceof \App\Models\Approver)
                        <div class="w-full md:w-1/3">
                            <label for="search" class="block font-medium text-gray-700">Cari Nama Checker:</label>
                            <input type="text" name="search" id="search" 
                                placeholder="Masukkan nama checker..." value="{{ request('search') }}" 
                                class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    @endif
                    <div class="w-full md:w-1/3">
                        <label for="filter_bulan" class="block font-medium text-gray-700">Filter Bulan:</label>
                        <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" 
                            class="w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="w-full md:w-1/6 flex items-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md w-full hover:bg-blue-700">
                            Cari
                        </button>
                    </div>
                </div>

                @if(auth()->user() instanceof \App\Models\Checker)
                    <div class="mt-4">
                        <a href="{{ route('compressor.create') }}" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                            Tambah Pencatatan
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-center">
                        <th class="p-3 border">Tanggal</th>
                        <th class="p-3 border">Hari</th>
                        <th class="p-3 border">Checker</th>
                        <th class="p-3 border">Status</th>
                        <th class="p-3 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($checks->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada data ditemukan.</td>
                        </tr>
                    @else
                        @foreach($checks as $check)
                            <tr class="text-center border">
                                <td class="p-3 border">{{ $check->tanggal }}</td>
                                <td class="p-3 border">{{ $check->hari }}</td>
                                <td class="p-3 border text-center">
                                    @if($check->checked_by_shift1)
                                        <span class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm">
                                            {{ $check->checked_by_shift1 }}
                                        </span>
                                    @endif
                                    @if($check->checked_by_shift2)
                                        <span class="bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-sm ml-1">
                                            {{ $check->checked_by_shift2 }}
                                        </span>
                                    @endif
                                    @if(!$check->checked_by_shift1 && !$check->checked_by_shift2)
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                
                                <td class="p-3 border">
                                    @if($check->approved_by)
                                        <span class="bg-green-200 text-green-700 px-3 py-1 rounded-full text-sm">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="bg-red-200 text-red-700 px-3 py-1 rounded-full text-sm">
                                            Belum Disetujui
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 border flex justify-center items-center gap-3">
                                    @if(auth()->user() instanceof \App\Models\Approver)
                                        <a href="{{ route('compressor.show', $check->id) }}" class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @elseif(auth()->user() instanceof \App\Models\Checker)
                                        @if(!$check->approved_by)
                                            <a href="{{ route('compressor.edit', $check->id) }}" class="text-yellow-500 hover:text-yellow-700">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        @else
                                            <i class="fas fa-pen text-yellow-300 cursor-not-allowed"></i>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            <div class="flex space-x-2">
                @if (!$checks->onFirstPage())
                    <a href="{{ $checks->previousPageUrl() }}" class="px-3 py-2 bg-gray-300 rounded hover:bg-gray-400">&laquo; Prev</a>
                @endif

                @foreach ($checks->getUrlRange(1, $checks->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="px-3 py-2 rounded {{ $page == $checks->currentPage() ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if ($checks->hasMorePages())
                    <a href="{{ $checks->nextPageUrl() }}" class="px-3 py-2 bg-gray-300 rounded hover:bg-gray-400">Next &raquo;</a>
                @endif
            </div>
        </div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Kembali
            </a>
        </div>
    </div>
    
</body>
</html>
