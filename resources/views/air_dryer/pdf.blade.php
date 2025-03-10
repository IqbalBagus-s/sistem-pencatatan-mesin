<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Air Dryer</title>
    <style>
        @page { 
            size: A4 landscape; /* Ukuran A4 Landscape */
            margin: 10px; /* Margin kecil agar lebih banyak konten yang muat */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px; /* Ukuran font kecil agar muat di halaman */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
            font-size: 9px; /* Perkecil teks dalam tabel */
        }

        th {
            background-color: #f2f2f2;
        }

        .table-container {
            page-break-inside: auto; /* Hindari tabel pecah dalam satu halaman */
        }

        tr { 
            page-break-inside: avoid; 
            page-break-after: auto;
        }

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .logo {
            height: 60px; /* Sesuaikan ukuran logo sesuai kebutuhan */
            max-width: 200px;
        }
        
        .header {
            margin-bottom: 20px;
        }
        
        .report-info {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo ASPRA" class="logo">
        </div>
        <div class="title">Laporan Pemeriksaan Air Dryer</div>
    </div>

    <div class="report-info">
        <p><strong>Tanggal:</strong> {{ $check->tanggal }}</p>
        <p><strong>Hari:</strong> {{ $check->hari }}</p>
        <p><strong>Checker:</strong> {{ $check->checked_by }}</p>
        <p><strong>Approved By:</strong> {{ $check->approved_by ?? 'Belum Disetujui' }}</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Mesin</th>
                    <th>Temperatur Kompresor</th>
                    <th>Temperatur Kabel</th>
                    <th>Temperatur MCB</th>
                    <th>Temperatur Angin In</th>
                    <th>Temperatur Angin Out</th>
                    <th>Evaporator</th>
                    <th>Fan Evaporator</th>
                    <th>Auto Drain</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($results as $index => $result)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $result->nomor_mesin }}</td>
                    <td>{{ $result->temperatur_kompresor ?? '-' }}</td>
                    <td>{{ $result->temperatur_kabel ?? '-' }}</td>
                    <td>{{ $result->temperatur_mcb ?? '-' }}</td>
                    <td>{{ $result->temperatur_angin_in ?? '-' }}</td>
                    <td>{{ $result->temperatur_angin_out ?? '-' }}</td>
                    <td>{{ $result->evaporator ?? '-' }}</td>
                    <td>{{ $result->fan_evaporator ?? '-' }}</td>
                    <td>{{ $result->auto_drain ?? '-' }}</td>
                    <td>{{ $result->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>