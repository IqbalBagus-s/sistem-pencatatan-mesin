<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Air Dryer</title>
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
        <h2>Detail Pencatatan Mesin Air Dryer</h2>

        <table class="header-table">
    <tr>
        <!-- Checker di kiri, Approver di kanan -->
        <td width="50%"><span class="label">Checker:</span> {{ $airDryer->checked_by }}</td>
        <td width="50%" style="text-align: right;"><span class="label">Approver:</span> {{ $airDryer->approved_by ?: 'Belum disetujui' }}</td>
    </tr>
</table>

<hr>

<table class="header-table">
    <tr>
        <!-- Hari di kiri, Tanggal di kanan -->
        <td width="50%"><span class="label">Hari:</span> {{ $airDryer->hari }}</td>
        <td width="50%" style="text-align: right;"><span class="label">Tanggal:</span> {{ \Carbon\Carbon::parse($airDryer->tanggal)->translatedFormat('d F Y') }}</td>
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
                    <th>Temperatur Angin In</th>
                    <th>Temperatur Angin Out</th>
                    <th>Evaporator</th>
                    <th>Fan Evaporator</th>
                    <th>Auto Drain</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->nomor_mesin }}</td>
                    <td>{{ $detail->temperatur_kompresor }}</td>
                    <td>{{ $detail->temperatur_kabel }}</td>
                    <td>{{ $detail->temperatur_mcb }}</td>
                    <td>{{ $detail->temperatur_angin_in }}</td>
                    <td>{{ $detail->temperatur_angin_out }}</td>
                    <td class="status-normal">{{ $detail->evaporator }}</td>
                    <td class="status-normal">{{ $detail->fan_evaporator }}</td>
                    <td class="status-normal">{{ $detail->auto_drain }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Horizontal Note Boxes -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px;">
            <tr>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px;">
                    <div class="note-title">Informasi Standar Pemeriksaan</div>
                    • Temperatur Kompresor: 30°C - 60°C<br>
                    • Temperatur Kabel: 30°C - 60°C<br>
                    • Temperatur MCB: 30°C - 60°C<br>
                    • Temperatur Angin In: 30°C - 60°C<br>
                    • Temperatur Angin Out: 30°C - 60°C<br>
                    • Evaporator: Bersih/Kotor (V/X)<br>
                    • Fan Evaporator: Suara Halus/Kasar (V/X)<br>
                    • Auto Drain: Berfungsi/Tidak Berfungsi (V/X)
                </td>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px;">
                    <div class="note-title">Detail Mesin</div>
                    • AD 1 : HIGH PRESS 1<br>
                    • AD 2 : HIGH PRESS 2<br>
                    • AD 3 : LOW PRESS 1<br>
                    • AD 4 : LOW PRESS 2<br>
                    • AD 5 : SUPPLY INJECT<br>
                    • AD 6 : LOW PRESS 3<br>
                    • AD 7 : LOW PRESS 4<br>
                    • AD 8 : LOW PRESS 5
                </td>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px;">
                    <div class="note-title">Keterangan Status</div>
                    • V : Baik/Normal<br>
                    • X : Tidak Baik/Abnormal<br>
                    • - : Tidak Diisi<br>
                    • OFF : Mesin Mati
                </td>
            </tr>
        </table>

        <!-- Catatan Tambahan -->
        <div style="border: 1px solid #000; padding: 5px; margin-bottom: 10px;">
            <div class="note-title">Catatan Tambahan</div>
            {{ $airDryer->keterangan ?: 'Tidak ada catatan' }}
        </div>

        <table style="width: 100%; margin-bottom: 10px;">
            <tr>
                <td style="text-align: left;"><strong>Nomor Form:</strong> {{ $form->nomor_form }}</td>
                <td style="text-align: right;"><strong>Tanggal Efektif:</strong> {{ $formattedTanggalEfektif }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
