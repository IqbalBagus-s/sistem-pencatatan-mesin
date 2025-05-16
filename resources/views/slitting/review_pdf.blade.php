<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 20px;
        }
        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }
        .header-info {
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .info-value {
            border: 1px solid #000;
            padding: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .centered {
            text-align: center;
        }
        .note-section {
            border: 1px solid #000;
            padding: 15px;
            margin-bottom: 20px;
        }
        .note-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .note-item {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pencatatan Mesin Slitting</h2>
        
        <div class="header-info">
            <span class="info-label">Checker:</span>
            @php
                $checkers = [];
                for ($i = 1; $i <= 4; $i++) {
                    if (!empty($slittingCheck->{'checked_by_minggu'.$i})) {
                        $checkers[] = $slittingCheck->{'checked_by_minggu'.$i};
                    }
                }
                $checkersText = !empty($checkers) ? implode(', ', array_unique($checkers)) : 'Belum ada checker';
            @endphp
            <span>{{ $checkersText }}</span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">No Slitting:</span>
                <div class="info-value">Slitting {{ $slittingCheck->nomer_slitting }}</div>
            </div>
            
            <div class="info-item">
                <span class="info-label">Bulan:</span>
                <div class="info-value">{{ date('F Y', strtotime($bulan)) }}</div>
            </div>
        </div>
        
        @php
            $items = [
                1 => 'Conveyor',
                2 => 'Motor Conveyor',
                3 => 'Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Inverter',
                6 => 'Vibrator',
                7 => 'Motor Vibrator',
                8 => 'Motor Blower',
                9 => 'Selang angin',
                10 => 'Flow Control',
                11 => 'Sensor',
                12 => 'Limit Switch',
                13 => 'Pisau Cutting',
                14 => 'Motor Cutting',
                15 => 'Elemen ',
                16 => 'Regulator',
                17 => 'Air Filter',
            ];

            $options = [
                'V' => '✓',
                'X' => '✗',
                '-' => '—',
                'OFF' => 'OFF'
            ];
        @endphp
        
        <table>
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th colspan="1">Minggu</th>
                    @for ($i = 1; $i <= 4; $i++)
                        <th colspan="1">0{{ $i }}</th>
                        <th rowspan="2">Keterangan</th>
                    @endfor
                </tr>
                <tr>
                    <th>Item Terperiksa</th>
                    @for ($i = 1; $i <= 4; $i++)
                        <th>Check</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    <tr>
                        <td class="centered">{{ $i }}</td>
                        <td>{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                // Cari hasil untuk item ini berdasarkan checked_items
                                $resultObj = $results->where('checked_items', $i)->first();
                                
                                $resultField = 'minggu' . $j;
                                $keteranganField = 'keterangan_minggu' . $j;
                                
                                // Ambil nilai langsung dari objek hasil, tanpa kondisi pengecekan
                                $resultValue = $resultObj ? ($resultObj->$resultField ?? '-') : '-';
                                $keteranganValue = $resultObj ? ($resultObj->$keteranganField ?? '') : '';
                            @endphp
                        
                            <td class="centered">
                                {!! isset($options[$resultValue]) ? $options[$resultValue] : '—' !!}
                            </td>
                            
                            <td>
                                {{ $keteranganValue }}
                            </td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td class="centered">-</td>
                    <td>Dibuat Oleh</td>
                    
                    @for($j = 1; $j <= 4; $j++)
                        @php
                            $checkedBy = $slittingCheck->{'checked_by_minggu'.$j} ?? '';
                        @endphp
                        <td colspan="2" class="centered">
                            {{ $checkedBy ?: '-' }}
                        </td>
                    @endfor
                </tr>
                <tr>
                    <td class="centered">-</td>
                    <td>Penanggung Jawab</td>
                    
                    @for($j = 1; $j <= 4; $j++)
                        @php
                            $approvedBy = $slittingCheck->{'approved_by_minggu'.$j} ?? '';
                        @endphp
                        <td colspan="2" class="centered">
                            {{ $approvedBy ?: '-' }}
                        </td>
                    @endfor
                </tr>
            </tfoot>
        </table>
        
        <div class="note-section">
            <div class="note-title">Catatan Pemeriksaan</div>
            <div class="note-item">
                • Pengecekan mesin, dilakukan setiap minggu secara berkala.
            </div>
        </div>
    </div>
</body>
</html>