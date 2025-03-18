<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Mesin Water Chiller</title>
    
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    
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
            width: 120px; /* Or any width you prefer */
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
    </style>
</head>
<body class="bg-blue-50 pt-5 font-poppins min-h-screen flex flex-col overscroll-none">

    <div class="container mx-auto px-4 pb-12">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Pencatatan Mesin Water Chiller</h2>

        <!-- Form Pencarian dan Tombol Tambah -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-5 mb-5">
            <form method="GET" action="{{ route('water-chiller.index') }}">
                <div class="flex flex-col md:flex-row md:justify-between md:items-end">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end flex-grow">
                        @if(auth()->user() instanceof \App\Models\Approver)
                        <div>
                            <label for="search" class="block font-medium text-gray-700 mb-2">Cari berdasarkan nama Checker:</label>
                            <input type="text" name="search" id="search" placeholder="Masukkan nama checker..." 
                                value="{{ request('search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        @endif
                        <div>
                            <label for="filter_bulan" class="block font-medium text-gray-700 mb-2">Filter berdasarkan Bulan:</label>
                            <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        <div>
                            <button type="submit" class="bg-primary hover:bg-primaryDark text-white py-2 px-4 rounded-md transition duration-200 search-button">Cari</button>
                        </div>
                    </div>
                    
                    @if(auth()->user() instanceof \App\Models\Checker)
                        <div class="mt-4 md:mt-0 flex">
                            <a href="{{ route('water-chiller.create') }}" class="bg-success hover:bg-successDark text-white py-2 px-4 rounded-md font-medium transition duration-200">
                                Tambah Pencatatan
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Tabel Data -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr class="text-center">
                        <th class="py-3 px-4 border-b border-gray-200 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 border-b border-gray-200 font-semibold">Hari</th>
                        <th class="py-3 px-4 border-b border-gray-200 font-semibold">Checker</th>
                        <th class="py-3 px-4 border-b border-gray-200 font-semibold">Status</th>
                        <th class="py-3 px-4 border-b border-gray-200 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($checks->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada data ditemukan.</td>
                        </tr>
                    @else
                        @foreach($checks as $check)
                            <tr class="text-center hover:bg-gray-50">
                                <td class="py-3 px-4 border-b border-gray-200">{{ $check->tanggal }}</td>
                                <td class="py-3 px-4 border-b border-gray-200">{{ $check->hari }}</td>
                                <td class="py-3 px-4 border-b border-gray-200">{{ $check->checked_by }}</td>
                                <td class="py-3 px-4 border-b border-gray-200">
                                    @if($check->approved_by)
                                        <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="bg-pending text-pendingText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                            Belum Disetujui
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 border-b border-gray-200">
                                    {{-- Menu lihat --}}
                                    @if(auth()->user() instanceof \App\Models\Approver)
                                        <a href="{{ route('water-chiller.show', $check->id) }}" title="Lihat Detail">
                                            <i class="fas fa-eye text-primary" title="Lihat Detail"></i>
                                        </a>
                                    {{-- Menu edit --}}
                                    @elseif(auth()->user() instanceof \App\Models\Checker)
                                        @if(!$check->approved_by)
                                            <a href="{{ route('water-chiller.edit', $check->id) }}" title="Edit">
                                                <i class="fas fa-pen text-amber-500 text-lg hover:text-amber-600 cursor-pointer"></i>
                                            </a>
                                        @else
                                            <i class="fas fa-pen text-amber-300 opacity-50 text-lg cursor-not-allowed" title="Tidak dapat diedit karena sudah disetujui"></i>
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
        <div class="flex justify-center mt-4">
            <div class="flex flex-wrap gap-1 justify-center">
                <!-- Previous button -->
                @if (!$checks->onFirstPage())
                    <a href="{{ $checks->previousPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-primary hover:bg-gray-100 transition duration-200">&laquo; Previous</a>
                @endif
                
                <!-- Page numbers -->
                @foreach ($checks->getUrlRange(1, $checks->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="px-3 py-2 border {{ $page == $checks->currentPage() ? 'bg-primary text-white border-primary font-bold' : 'bg-white text-primary border-gray-300 hover:bg-gray-100' }} rounded-md transition duration-200">
                        {{ $page }}
                    </a>
                @endforeach
                
                <!-- Next button -->
                @if ($checks->hasMorePages())
                    <a href="{{ $checks->nextPageUrl() }}" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-primary hover:bg-gray-100 transition duration-200">Next &raquo;</a>
                @endif
            </div>
        </div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-secondary hover:bg-gray-600 text-white rounded-md transition duration-200">
                Kembali
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white py-4 text-center shadow-md mt-auto">
        <p class="font-bold">2025 © PT Asia Pramulia</p>
    </footer>

    @vite('resources/js/app.js')
</body>
</html>