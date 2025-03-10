<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin Water Chiller</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        Approval Pencatatan Mesin Water Chiller
    </div>

    <div class="info">
        <strong>Approver:</strong> {{ Auth::user()->username }} <br>
        <strong>Checker:</strong> {{ $check->checked_by }} <br>
        <strong>Tanggal:</strong> {{ $check->tanggal }} <br>
        <strong>Hari:</strong> {{ $check->hari }} <br>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ITEM YANG DIPERIKSA</th>
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
                    <td>{{ $result->checked_items }}</td>
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

    <div class="info">
        <strong>Keterangan:</strong> {{ $check->keterangan }}
    </div>
</body>
</html>
