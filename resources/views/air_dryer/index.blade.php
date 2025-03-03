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

        <a href="{{ route('air-dryer.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">Tambah Pencatatan</a>

        <form method="GET" action="{{ route('air-dryer.index') }}" class="mt-4">
            <label for="filter_bulan" class="block text-sm font-medium">Filter berdasarkan Bulan:</label>
            <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" class="border rounded px-4 py-2">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
        </form>
        
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
                        <a href="{{ route('air-dryer.edit', $check->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="mt-4">
            {{ $checks->links() }}
        </div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                Kembali ke Dashboard
            </a>
        </div>

    </div>
</body>
</html>
