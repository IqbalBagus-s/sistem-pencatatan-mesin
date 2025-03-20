<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Water Chiller</title>
    <style>
        @page { 
            size: A4 landscape;
            margin: 8px; /* Margin lebih kecil untuk maksimalkan ruang */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px; /* Ukuran font sedikit lebih besar */
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px; /* Kurangi margin bawah tabel */
        }

        th, td {
            border: 1px solid black;
            padding: 4px 3px; /* Padding sedikit dikurangi */
            text-align: center;
            font-size: 10px; /* Font tabel sedikit lebih besar */
        }

        th {
            background-color: #f2f2f2;
        }

        .table-container {
            page-break-inside: auto;
        }

        tr { 
            page-break-inside: avoid; 
            page-break-after: auto;
        }

        .title {
            text-align: center;
            font-size: 16px; /* Ukuran judul lebih besar */
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 8px;
        }
        
        .logo {
            height: 70px; /* Logo sedikit lebih kecil */
            max-width: 200px;
        }
        
        .header {
            margin-bottom: 12px; /* Header margin lebih kecil */
        }
        
        .report-info {
            margin-bottom: 10px;
            display: flex;
            flex-wrap: wrap;
        }
        
        .report-info p {
            margin: 0 20px 5px 0; /* Susun informasi secara horizontal dengan jarak */
            padding: 0;
        }
        
        .keterangan {
            margin-top: 8px;
        }
        
        /* Memastikan penggunaan ruang optimal */
        .keterangan p {
            margin: 3px 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container"> 
            <img src="{{ public_path('images/logo.png') }}" alt="Logo ASPRA" class="logo">
        </div>
        <div class="title">Laporan Pemeriksaan Water Chiller</div>
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
                    <th>Temperatur Air</th>
                    <th>Temperatur Pompa</th>
                    <th>Evaporator</th>
                    <th>Fan Evaporator</th>
                    <th>Freon</th>
                    <th>Air</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $index => $result)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $result->no_mesin }}</td>
                    <td>{{ $result->Temperatur_Compressor ?? '-' }}</td>
                    <td>{{ $result->Temperatur_Kabel ?? '-' }}</td>
                    <td>{{ $result->Temperatur_Mcb ?? '-' }}</td>
                    <td>{{ $result->Temperatur_Air ?? '-' }}</td>
                    <td>{{ $result->Temperatur_Pompa ?? '-' }}</td>
                    <td>{{ $result->Evaporator ?? '-' }}</td>
                    <td>{{ $result->Fan_Evaporator ?? '-' }}</td>
                    <td>{{ $result->Freon ?? '-' }}</td>
                    <td>{{ $result->Air ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="keterangan">
        <p><strong>Keterangan:</strong> {{ $check->keterangan }}</p>
    </div>
</body>
</html>