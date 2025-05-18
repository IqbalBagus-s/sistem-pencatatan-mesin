<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Water Chiller</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        h2 {
            font-size: 14px;
            text-align: center;
            margin-bottom: 15px;
        }
        hr {
            border: 0.5px solid #000;
            margin: 10px 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            padding: 2px;
            vertical-align: top;
            border: none;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 2px;
            border: 1px solid #000;
            width: 50%;
        }
        .label {
            font-weight: bold;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .main-table th {
            background-color: #f2f2f2;
        }
        .note-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .status-normal {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pencatatan Mesin Water Chiller</h2>

        <table class="header-table">
            <tr>
                <!-- Hari di kiri, Tanggal di kanan -->
                <td width="50%"><span class="label">Hari:</span> {{ $waterChiller->hari }}</td>
                <td width="50%" style="text-align: right;"><span class="label">Tanggal:</span> {{ \Carbon\Carbon::parse($waterChiller->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <table class="main-table">
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
                @foreach($details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>CH{{ $index + 1 }}</td>
                    <td>{{ $detail->Temperatur_Compressor }}</td>
                    <td>{{ $detail->Temperatur_Kabel }}</td>
                    <td>{{ $detail->Temperatur_Mcb }}</td>
                    <td>{{ $detail->Temperatur_Air }}</td>
                    <td>{{ $detail->Temperatur_Pompa }}</td>
                    <td class="status-normal">{{ $detail->Evaporator }}</td>
                    <td class="status-normal">{{ $detail->Fan_Evaporator }}</td>
                    <td class="status-normal">{{ $detail->Freon }}</td>
                    <td class="status-normal">{{ $detail->Air }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Horizontal Note Boxes -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px; font-size: 12px; border-collapse: collapse;">
            <tr>
                <!-- Kolom 1: Informasi Standar (bagian kiri) -->
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 33%;">
                <div style="font-weight: bold; margin-bottom: 5px;">Informasi Standar Pemeriksaan</div>
                • Temperatur Kompresor: 30°C – 60°C<br>
                • Temperatur Kabel: 30°C – 60°C<br>
                • Temperatur MCB: 30°C – 60°C<br>
                • Temperatur Air: 30°C – 60°C<br>
                </td>

                <!-- Kolom 2: Informasi Standar (bagian tengah) -->
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 33%;">
                <div style="font-weight: bold; margin-bottom: 5px;">Informasi Standar Pemeriksaan</div>
                • Temperatur Pompa: 30°C – 60°C<br>
                • Evaporator: V / X / – / OFF<br>
                • Fan Evaporator: V / X / – / OFF<br>
                • Freon: V / X / – / OFF<br>
                • Air: V / X / – / OFF<br>
                </td>

                <!-- Kolom 3: Keterangan Status -->
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 33%;">
                <div style="font-weight: bold; margin-bottom: 5px;">Keterangan Status</div>
                • V : Baik/Normal<br>
                • X : Tidak Baik/Abnormal<br>
                • – : Tidak Diisi<br>
                • OFF : Mesin Mati<br>
                </td>
            </tr>
            </table>

        <!-- Catatan Tambahan -->
        <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
            <div class="note-title">Catatan Tambahan</div>
            {{ $waterChiller->keterangan ?: 'Tidak ada catatan' }}
        </div>

        <!-- Tanda Tangan -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Diperiksa oleh:</div>
                    <div style="font-weight: bold;">{{ $waterChiller->checked_by }}</div>
                    <div>Checker</div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Disetujui oleh:</div>
                    <div style="font-weight: bold;">{{ $waterChiller->approved_by ?: '.........................' }}</div>
                    <div>Penanggung Jawab</div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="text-align: left;"><strong>Nomor Form:</strong> {{ $form->nomor_form }}</td>
                <td style="text-align: right;"><strong>Tanggal Efektif:</strong> {{ $formattedTanggalEfektif }}</td>
            </tr>
        </table>
    </div>
</body>
</html>