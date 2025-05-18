<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemeriksaan Mesin Giling</title>
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
        <h2>Detail Pemeriksaan Mesin Giling</h2>

        <table class="header-table">
            <tr>
                <!-- Minggu di kiri, Bulan di kanan -->
                <td width="50%"><span class="label">Minggu ke:</span> {{ $gilingCheck->minggu }}</td>
                <td width="50%" style="text-align: right;"><span class="label">Bulan:</span> {{ \Carbon\Carbon::parse($gilingCheck->bulan)->translatedFormat('F Y') }}</td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Checked Items</th>
                    <th colspan="10">HASIL PEMERIKSAAN GILINGAN</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= 10; $i++)
                        <th>G{{ $i }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @php
                    $items = [
                        1 => 'Cek Motor Mesin Giling',
                        2 => 'Cek Vanbelt',
                        3 => 'Cek Dustcollector',
                        4 => 'Cek Safety Switch',
                        5 => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
                    ];
                @endphp
                
                @foreach($items as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for ($g = 1; $g <= 10; $g++)
                            <td class="status-normal">
                                @php
                                    $result = $details->where('checked_items', $item)->first();
                                    $value = $result ? $result->{"g$g"} : '-';
                                @endphp
                                {{ $value }}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Horizontal Note Boxes -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px;">
            <tr>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Standar Kriteria Pemeriksaan</div>
                    • Motor Mesin Giling: Suara halus, tidak panas berlebih<br>
                    • Vanbelt: Tidak pecah/retak, kekencangan sesuai standar<br>
                    • Dustcollector: Berfungsi normal, tidak tersumbat<br>
                    • Safety Switch: Berfungsi dengan baik saat diuji<br>
                    • Ketajaman Pisau: Tajam dan tidak tumpul, tidak ada kerusakan (Pemeriksaan pada minggu keempat setiap bulan)
                </td>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
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
            {{ $gilingCheck->keterangan ?: 'Tidak ada catatan' }}
        </div>

        <!-- Tanda Tangan -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Diperiksa oleh:</div>
                    <div style="font-weight: bold;">{{ $gilingCheck->checked_by }}</div>
                    <div>Pelaksana Utility</div>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Disetujui oleh:</div>
                    <div style="font-weight: bold;">{{ $gilingCheck->approved_by1 ?: '.........................' }}</div>
                    <div>Pelaksana Utility</div>
                    <div>{{ $gilingCheck->approval_date1 ? \Carbon\Carbon::parse($gilingCheck->approval_date1)->format('d/m/Y') : '' }}</div>
                </td>
                <td style="width: 33%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Disetujui oleh:</div>
                    <div style="font-weight: bold;">{{ $gilingCheck->approved_by2 ?: '.........................' }}</div>
                    <div>Koordinator Staff Utility</div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 10px; margin-top: 20px;">
            <tr>
                <td style="text-align: left;"><strong>Nomor Form:</strong> {{ $form->nomor_form }}</td>
                <td style="text-align: right;"><strong>Tanggal Efektif:</strong> {{ $formattedTanggalEfektif }}</td>
            </tr>
        </table>
    </div>
</body>
</html>