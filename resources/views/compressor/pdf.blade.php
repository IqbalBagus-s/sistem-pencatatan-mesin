<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Checklist</title>
    <style>
        body {
            font-family: sans-serif;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .table th {
            background-color: #f3f3f3;
        }
        .text-primary { color: blue; }
        .text-success { color: green; }
        .bg-light { background-color: #f8f9fa; }
        .rounded { border-radius: 5px; }
        .mb-4 { margin-bottom: 16px; }
        .p-3 { padding: 12px; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Checklist Mesin</h2>

    <div class="mb-4 p-3 bg-light rounded">
        <p><strong>Approver:</strong> <span class="text-primary">{{ $check->approved_by }}</span></p>
    </div>

    <div class="mb-4 p-3 bg-light rounded">
        <p><strong>Checker:</strong> <span class="text-success">{{ $check->checked_by }}</span></p>
    </div>

    <div class="mb-3">
        <p><strong>Tanggal:</strong> {{ $check->tanggal }}</p>
    </div>

    <div class="mb-3">
        <p><strong>Hari:</strong> {{ $check->hari }}</p>
    </div>

    <table class="table">
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
                    <td>{{ $result->Temperatur_Compressor }}</td>
                    <td>{{ $result->Temperatur_Kabel }}</td>
                    <td>{{ $result->Temperatur_Mcb }}</td>
                    <td>{{ $result->Temperatur_Air }}</td>
                    <td>{{ $result->Temperatur_Pompa }}</td>
                    <td>{{ $result->Evaporator }}</td>
                    <td>{{ $result->Fan_Evaporator }}</td>
                    <td>{{ $result->Freon }}</td>
                    <td>{{ $result->Air }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        <p><strong>Keterangan:</strong></p>
        <p>{{ $check->keterangan }}</p>
    </div>
</body>
</html>