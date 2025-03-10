<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pencatatan Mesin Air Dryer</title>
    
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
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
        
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background-color: #e6f2ff;
            padding-top: 20px;
            overflow-y: auto;
            overscroll-behavior-y: none;
        }
        .container {
            padding-bottom: 50px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        .page-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #1a1a1a;
        }
        .btn-add {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            font-weight: 500;
            height: 38px;
        }
        .btn-add:hover {
            background-color: #218838;
            color: white;
        }
        .btn-view {
            background-color: #1565c0;
            color: white;
            border: none;
        }
        .btn-view:hover {
            background-color: #0d47a1;
            color: white;
        }
        .eye-icon {
            transition: opacity 0.2s ease;
        }
        .eye-icon:hover {
            opacity: 0.7;
        }
        .edit-icon {
            color: #ffc107;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .edit-icon:hover {
            color: #e0a800;
        }
        .edit-icon-disabled {
            color: #ffc10780;
            opacity: 0.5;
            cursor: not-allowed;
        }
        .footer {
            background-color: #ffffff;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
            margin-top: auto;
            width: 100%;
        }
        .btn-back {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 20px;
        }
        .btn-back:hover {
            background-color: #5a6268;
            color: white;
        }
        .btn-search {
            background-color: #1565c0;
            color: white;
            border: none;
            height: 38px;
        }
        .btn-search:hover {
            background-color: #0d47a1;
            color: white;
        }
        .table-container {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .table-header {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }
        .status-pending {
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-block;
        }
        .filter-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .form-control:focus {
            border-color: #1565c0;
            box-shadow: 0 0 0 0.2rem rgba(21, 101, 192, 0.25);
        }
        .search-container {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }
        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }
        .table-hover tbody tr:hover {
            color: #212529;
            background-color: rgba(0, 0, 0, 0.075);
        }
        .text-center {
            text-align: center !important;
        }
        .pagination-container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 1rem;
            padding: 0 15px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            gap: 5px;
            width: 100%;
            margin: 0 auto;
        }
        .pagination .page-link {
            color: #1565c0;
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 0.5rem 0.75rem;
            text-decoration: none;
        }
        .pagination .page-link.active {
            background-color: #1565c0;
            color: white;
            border-color: #1565c0;
            font-weight: bold;
        }
        .pagination .page-link:hover:not(.active):not([style*="pointer-events: none"]) {
            background-color: #e9ecef;
        }
        .search-and-add {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .action-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        @media (min-width: 768px) {
            .col-md-2 {
                flex: 0 0 16.666667%;
                max-width: 16.666667%;
            }
            .col-md-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
        }
        @media (max-width: 768px) {
            body {
                padding-top: 20px;
            }
            .search-container {
                padding: 15px;
            }
            .search-and-add {
                flex-direction: column;
                align-items: flex-start;
            }
            .action-buttons {
                margin-top: 15px;
                width: 100%;
            }
            .btn-add, .btn-search {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="page-title">Pencatatan Mesin Air Dryer</h2>

        <!-- Form Pencarian dan Tombol Tambah -->
        <div class="card search-container">
            <form method="GET" action="{{ route('air-dryer.index') }}">
                <div class="search-and-add">
                    <div class="row g-3 align-items-end flex-grow-1">
                        @if(auth()->user() instanceof \App\Models\Approver)
                        <div class="col-md-4">
                            <label for="search" class="filter-label">Cari berdasarkan nama Checker:</label>
                            <input type="text" name="search" id="search" placeholder="Masukkan nama checker..." 
                                value="{{ request('search') }}" class="form-control">
                        </div>
                        @endif
                        <div class="col-md-4">
                            <label for="filter_bulan" class="filter-label">Filter berdasarkan Bulan:</label>
                            <input type="month" name="bulan" id="filter_bulan" value="{{ request('bulan') }}" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-search w-100">Cari</button>
                        </div>
                    </div>
                    
                    @if(auth()->user() instanceof \App\Models\Checker)
                        <div class="action-buttons">
                            <a href="{{ route('air-dryer.create') }}" class="btn btn-add">
                                Tambah Pencatatan
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
        
        <!-- Tabel Data -->
        <div class="table-container">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-header">
                    <tr class="text-center">
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Hari</th>
                        <th class="py-3">Checker</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($checks->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-4">Tidak ada data ditemukan.</td>
                        </tr>
                    @else
                        @foreach($checks as $check)
                            <tr class="text-center align-middle">
                                <td class="py-3">{{ $check->tanggal }}</td>
                                <td class="py-3">{{ $check->hari }}</td>
                                <td class="py-3">{{ $check->checked_by }}</td>
                                <td class="py-3">
                                    @if($check->approved_by)
                                        <span class="status-approved">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="status-pending">
                                            Belum Disetujui
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    {{-- Menu lihat --}}
                                    @if(auth()->user() instanceof \App\Models\Approver)
                                        <a href="{{ route('air-dryer.show', $check->id) }}" title="Lihat Detail">
                                            <i class="fas fa-eye" style="color: #1565c0;" title="Lihat Detail"></i>
                                        </a>
                                    {{-- Menu edit --}}
                                    @elseif(auth()->user() instanceof \App\Models\Checker)
                                        @if(!$check->approved_by)
                                            <a href="{{ route('air-dryer.edit', $check->id) }}" title="Edit">
                                                <i class="fas fa-pen edit-icon"></i>
                                            </a>
                                        @else
                                            <i class="fas fa-pen edit-icon-disabled" title="Tidak dapat diedit karena sudah disetujui"></i>
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
        <div class="pagination-container">
            <div class="pagination">
                <!-- Previous button -->
                @if (!$checks->onFirstPage())
                    <a href="{{ $checks->previousPageUrl() }}" class="page-link" rel="prev">&laquo; Previous</a>
                @endif
                
                <!-- Page numbers -->
                @foreach ($checks->getUrlRange(1, $checks->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page == $checks->currentPage() ? 'active' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach
                
                <!-- Next button -->
                @if ($checks->hasMorePages())
                    <a href="{{ $checks->nextPageUrl() }}" class="page-link" rel="next">Next &raquo;</a>
                @endif
            </div>
        </div>

        <!-- Tombol Kembali ke Dashboard -->
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-back">
                Kembali
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0 fw-bold">2025 © PT ASIA PRAMULIA</p>
    </footer>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>