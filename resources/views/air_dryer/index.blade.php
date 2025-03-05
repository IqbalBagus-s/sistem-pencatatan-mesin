<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Mesin Air Dryer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Pencatatan Mesin Air Dryer</h2>

        <!-- Tampilkan tombol "Tambah Pencatatan" hanya jika user adalah Checker -->
        @if(auth()->user() instanceof \App\Models\Checker)
            <a href="{{ route('air-dryer.create') }}" class="bg-green-500 text-white px-4 py-2 rounded mb-10">
                Tambah Pencatatan
            </a>
        @endif

        
        {{-- filtering table --}}
        <form method="GET" action="{{ route('air-dryer.index') }}">
            @if(auth()->user() instanceof \App\Models\Approver)
            <input type="text" name="search" placeholder="Cari nama checker..." 
            value="{{ request('search') }}"
            class="border rounded px-4 py-2 w-64">
            @endif
            <label for="filter_bulan" class="block text-sm font-medium mt-5">Filter berdasarkan Bulan:</label>
            <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" class="border rounded px-4 py-2">
            <button type="submit">Cari</button>
        </form>
        
        {{-- tabel form --}}
        <table class="w-full mt-4 border bg-white shadow-md rounded-lg">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">Tanggal</th>
                    <th class="border px-4 py-2">Hari</th>
                    <th class="border px-4 py-2">Checker</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if($checks->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center border px-4 py-2">Tidak ada data ditemukan.</td>
                    </tr>
                @else
                    @foreach($checks as $check)
                        <tr class="text-center">
                            <td class="border px-4 py-2">{{ $check->tanggal }}</td>
                            <td class="border px-4 py-2">{{ $check->hari }}</td>
                            <td class="border px-4 py-2">{{ $check->checked_by }}</td>
                            <td class="border px-4 py-2">
                                @if($check->approved_by)
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-800 bg-green-200 rounded-full">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-800 bg-red-200 rounded-full">
                                        Belum Disetujui
                                    </span>
                                @endif
                            </td>
                            <td class="border px-4 py-2">
                                {{-- menu lihat --}}
                                @if(auth()->user() instanceof \App\Models\Approver)
                                    <a href="{{ route('air-dryer.show', $check->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Lihat</a>
                                {{-- menu edit --}}
                                @elseif(auth()->user() instanceof \App\Models\Checker)
                                    @if(!$check->approved_by)
                                        <a href="{{ route('air-dryer.edit', $check->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded">Edit</a>
                                    @else
                                        <button class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed" disabled>Edit</button>
                                    @endif
                                @endif
                            </td>                            
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        
        
        <div class="mt-4">
            {{ $checks->links() }}
        </div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                Kembali
            </a>
        </div>

    </div>
</body>
</html>
