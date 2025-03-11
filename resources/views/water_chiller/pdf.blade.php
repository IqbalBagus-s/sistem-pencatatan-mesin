<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pencatatan Mesin Air Dryer</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0; /* Hapus margin dan padding bawaan */
        }
        .container {
            width: 100%;
            padding: 5px; /* Kurangi padding */
            margin: 0; /* Hilangkan margin */
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 5px; /* Kurangi margin agar lebih rapat */
        }
        .info p {
            margin: 2px 0;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
            margin: 0; /* Hapus margin yang tidak perlu */
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 3px;
            text-align: center;
        }
        .text-left {
            text-align: left;
        }
        @media print {
            @page {
                size: landscape;
                margin: 10px; /* Sesuaikan margin untuk cetak */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Laporan Pencatatan Mesin Air Dryer</div>

        <div class="info">
            <p><strong>Approver:</strong> {{ Auth::user()->username }}</p>
            <p><strong>Checker:</strong> {{ $check->checked_by }}</p>
            <p><strong>Tanggal:</strong> {{ $check->tanggal }}</p>
            <p><strong>Hari:</strong> {{ $check->hari }}</p>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="text-left">ITEM YANG DIPERIKSA</th>
                        <th>STANDART</th>
                        @for ($i = 1; $i <= 32; $i++)
                            <th>CH{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $index => $result)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-left">{{ $result->checked_items }}</td>
                            <td>{{ $result->standart }}</td>
                            @for ($j = 1; $j <= 32; $j++)
                                @php 
                                    $key = "CH{$j}"; 
                                    $value = $result->$key ?? '-';
                                @endphp
                                <td>{{ $value }}</td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="info">
            <p><strong>Keterangan:</strong> {{ $check->keterangan }}</p>
        </div>
    </div>
</body>
</html>
