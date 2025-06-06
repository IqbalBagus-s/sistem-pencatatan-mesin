<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Crane Matras</title>
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
        .checker-row {
            background-color: #f2f2f2;
        }
        .approver-row {
            background-color: #e6f7ff;
        }
        .item-cell {
            text-align: left;
            padding-left: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pencatatan Mesin Crane Matras</h2>

        <table class="header-table">
            <tr>
                <td width="50%"><span class="label">No Crane Matras:</span> Crane Matras nomor {{ $checkerData['nomer_crane_matras'] }}</td>
                <td width="50%" style="text-align: right;"><span class="label">Bulan:</span> {{ \Carbon\Carbon::parse($checkerData['bulan'])->translatedFormat('F Y') }}</td>
            </tr>
        </table>

        <!-- Tabel Utama Inspeksi -->
        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 35%;">Item Terperiksa</th>
                    <th style="width: 15%;">Check</th>
                    <th style="width: 42%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Items yang perlu di-check (sesuai dengan halaman show)
                    $items = [
                        1 => 'INVERTER',
                        2 => 'KONTAKTOR',
                        3 => 'THERMAL OVERLOAD',
                        4 => 'PUSH BOTTOM',
                        5 => 'MOTOR',
                        6 => 'BREAKER',
                        7 => 'TRAFO',
                        8 => 'CONECTOR BUSBAR',
                        9 => 'REL BUSBAR',
                        10 => 'GREASE',
                        11 => 'RODA',
                        12 => 'RANTAI',
                    ];

                    // Format hasil pemeriksaan untuk kemudahan akses
                    $resultsByItem = [];
                    foreach($formattedResults as $result) {
                        $resultsByItem[$result['item']] = $result;
                    }
                @endphp
                
                @foreach($items as $i => $item)
                    @php
                        $result = $resultsByItem[$item] ?? null;
                        $checkValue = $result ? $result['check'] : '-';
                        $keterangan = $result ? $result['keterangan'] : '';
                    @endphp
                    <tr>
                        <td>{{ $i }}</td>
                        <td class="item-cell">{{ $item }}</td>
                        <td class="status-normal">{{ $checkValue }}</td>
                        <td class="item-cell">{{ $keterangan }}</td>
                    </tr>
                @endforeach
                
                <!-- Dibuat Oleh (Checker) -->
                <tr class="checker-row">
                    <td>-</td>
                    <td class="item-cell" style="font-weight: bold;">Dibuat Oleh</td>
                    <td colspan="2" style="text-align: center;">
                        {{ $checkerData['checker_name'] }}
                        @if($checkerData['tanggal'])
                            <br>
                            <span style="font-size: 9px;">{{ $checkerData['tanggal'] }}</span>
                        @endif
                    </td>
                </tr>
                
                <!-- Penanggung Jawab (Approver) -->
                <tr class="approver-row">
                    <td>-</td>
                    <td class="item-cell" style="font-weight: bold;">Penanggung Jawab</td>
                    <td colspan="2" style="text-align: center;">
                        {{ $checkerData['approver_name'] ?: '........................' }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Horizontal Note Boxes -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px; font-size: 11px;">
            <tr>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Standar Kriteria Pemeriksaan</div>
                    • INVERTER: Berfungsi normal<br>
                    • KONTAKTOR: Koneksi baik<br>
                    • THERMAL OVERLOAD: Tidak trip<br>
                    • PUSH BOTTOM: Responsif<br>
                    • MOTOR: Beroperasi normal<br>
                    • BREAKER: Tidak trip<br>
                    • TRAFO: Tegangan stabil<br>
                    • CONECTOR BUSBAR: Koneksi kuat<br>
                    • REL BUSBAR: Tidak longgar<br>
                    • GREASE: Cukup dan bersih<br>
                    • RODA: Tidak aus/retak<br>
                    • RANTAI: Tidak kendor/aus
                </td>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Keterangan Status</div>
                    • ✓ : Baik/Normal<br>
                    • ✗ : Tidak Baik/Abnormal<br>
                    • - : Tidak Diisi<br>
                    • OFF : Mesin Mati<br><br>
                    
                    <div class="note-title">Petunjuk Keselamatan:</div>
                    • Pastikan area kerja aman<br>
                    • Jangan melebihi beban maksimum<br>
                    • Lakukan pemeriksaan visual<br>
                    • Hentikan jika ada ketidaknormalan<br>
                    • Laporkan kerusakan ke supervisor
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Dibuat oleh:</div>
                    <div style="font-weight: bold;">
                        {{ $checkerData['checker_name'] }}
                    </div>
                    <div>Checker</div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Disetujui oleh:</div>
                    <div style="font-weight: bold;">
                        {{ $checkerData['approver_name'] ?: '........................' }}
                    </div>
                    <div>Penanggung Jawab</div>
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