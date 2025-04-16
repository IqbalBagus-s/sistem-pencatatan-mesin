<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin @yield('machine-type')</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    @vite('resources/css/app.css')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-blue-50 pt-5 font-poppins min-h-screen flex flex-col overscroll-none">

    <div class="container mx-auto bg-white p-4 rounded-lg shadow-md">
        <h2 class="mb-4 font-bold text-xl @yield('title-left')">Approval Pencatatan Mesin @yield('machine-type')</h2>
        
        <!-- Tampilkan nama approver yang sedang login -->
        <div class="mb-4 p-3 bg-gray-100 rounded-lg">
            <p class="text-lg font-semibold">Approver: 
                <span class="text-blue-600">
                    {{ $check->approved_by ? $check->approved_by : Auth::user()->username }}
                </span>
            </p>
        </div>

        <!-- Tampilkan nama checker yang mengisi data -->
        <div class="mb-4 p-3 bg-gray-100 rounded-lg">
            <p class="text-lg font-semibold">Checker: 
                <span class="text-green-600">{{ $check->checked_by }}</span>
            </p>
        </div>

        @hasSection('month-time')
            @yield('month-time')
        @else
        <div class="mb-3">
            <label class="block font-medium mb-1">Tanggal:</label>
            <input type="date" value="{{ $check->tanggal }}" 
            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
        </div>
        
        <div class="mb-3">
            <label class="block font-medium mb-1">Hari:</label>
            <input type="text" value="{{ $check->hari }}" 
            class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" readonly>
        </div>
        @endif
        
        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            @yield('table-content')
        </div>
        
        @yield('machine-detail')

        <div class="mt-4 @yield('keterangan-container')">
            <label class="block @yield('keterangan-label') font-medium mb-1">Keterangan:</label>
            <div class="w-full px-3 py-2 min-h-[100px] bg-gray-100 border border-gray-300 rounded-md">
                @if($check->keterangan)
                    {{ $check->keterangan }}
                @else
                    <span class="text-gray-400 italic">Tidak ada keterangan....</span>
                @endif
            </div>
        </div>

        {{-- Menu setujui --}}
        @yield('approval-menu')

        <!-- Bagian tombol yang diperbaiki - Form hanya di bagian ini -->
        <div class="flex justify-between items-center mt-4">
            <div>
                <a href="@yield('back-route')" 
                    class="inline-block px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md transition duration-200">
                    Kembali
                </a>
            </div>
            
            <div>
                @if(!($check->approved_by1 && $check->approved_by2) && !$check->approved_by)
                <form action="@yield('approval-route')" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="approved_by1" x-model="approver1">
                    <input type="hidden" name="approved_by2" x-model="approver2">
                    <button type="submit" 
                        class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md transition duration-200">
                        Setujui
                    </button>
                </form>
                @else
                <a href="@yield('pdf-route')" 
                    class="inline-block mr-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200">
                    Download PDF
                </a>
                <button type="button" 
                    class="inline-block px-4 py-2 bg-gray-600 opacity-75 text-white rounded-md cursor-not-allowed" 
                    disabled>
                    Telah Disetujui
                </button>
                @endif
            </div>
        </div>
    </div>

</body>
</html>