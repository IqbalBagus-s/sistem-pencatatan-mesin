<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Slitting</title>
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
        .section-header {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pencatatan Mesin Slitting</h2>

        <table class="header-table">
            <tr>
                <td width="50%"><span class="label">No Slitting:</span> {{ $slittingCheck->nomer_slitting }}</td>
                <td width="50%" style="text-align: right;"><span class="label">Bulan:</span> {{ \Carbon\Carbon::parse($slittingCheck->bulan)->translatedFormat('F Y') }}</td>
            </tr>
        </table>

        <!-- Tabel Utama Inspeksi -->
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Item Terperiksa</th>
                    <th colspan="2">Minggu 01</th>
                    <th colspan="2">Minggu 02</th>
                    <th colspan="2">Minggu 03</th>
                    <th colspan="2">Minggu 04</th>
                </tr>
                <tr>
                    <th>Check</th>
                    <th>Keterangan</th>
                    <th>Check</th>
                    <th>Keterangan</th>
                    <th>Check</th>
                    <th>Keterangan</th>
                    <th>Check</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <!-- Conveyor Section -->
                @php
                    $conveyorItems = [
                        1 => 'Conveyor',
                        2 => 'Motor Conveyor'
                    ];
                @endphp
                
                @foreach($conveyorItems as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                $hasChecker = !empty($slittingCheck->{'checker_minggu'.$j.'_id'});
                                $resultValue = $hasChecker && isset($slittingCheck->{'check_'.$j}[$i]) ? $slittingCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($slittingCheck->{'keterangan_'.$j}[$i]) ? $slittingCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                @php
                    $kelistrikanItems = [
                        3 => 'Kelistrikan',
                        4 => 'Kontaktor',
                        5 => 'Inverter'
                    ];
                @endphp
                
                @foreach($kelistrikanItems as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                $hasChecker = !empty($slittingCheck->{'checker_minggu'.$j.'_id'});
                                $resultValue = $hasChecker && isset($slittingCheck->{'check_'.$j}[$i]) ? $slittingCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($slittingCheck->{'keterangan_'.$j}[$i]) ? $slittingCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                @php
                    $vibratorItems = [
                        6 => 'Vibrator',
                        7 => 'Motor Vibrator'
                    ];
                @endphp
                
                @foreach($vibratorItems as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                $hasChecker = !empty($slittingCheck->{'checker_minggu'.$j.'_id'});
                                $resultValue = $hasChecker && isset($slittingCheck->{'check_'.$j}[$i]) ? $slittingCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($slittingCheck->{'keterangan_'.$j}[$i]) ? $slittingCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                @php
                    $motorItems = [
                        8 => 'Motor Blower',
                        9 => 'Selang angin',
                        10 => 'Flow Control',
                        11 => 'Sensor',
                        12 => 'Limit Switch'
                    ];
                @endphp
                
                @foreach($motorItems as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                $hasChecker = !empty($slittingCheck->{'checker_minggu'.$j.'_id'});
                                $resultValue = $hasChecker && isset($slittingCheck->{'check_'.$j}[$i]) ? $slittingCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($slittingCheck->{'keterangan_'.$j}[$i]) ? $slittingCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                @php
                    $cuttingItems = [
                        13 => 'Pisau Cutting',
                        14 => 'Motor Cutting',
                        15 => 'Elemen',
                        16 => 'Regulator',
                        17 => 'Air Filter'
                    ];
                @endphp
                
                @foreach($cuttingItems as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                $hasChecker = !empty($slittingCheck->{'checker_minggu'.$j.'_id'});
                                $resultValue = $hasChecker && isset($slittingCheck->{'check_'.$j}[$i]) ? $slittingCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($slittingCheck->{'keterangan_'.$j}[$i]) ? $slittingCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                <!-- Dibuat Oleh (Checker) - Menampilkan nama checker melalui relasi -->
                <tr class="checker-row">
                    <td>-</td>
                    <td style="text-align: left; font-weight: bold;">Dibuat Oleh</td>
                    
                   @for($j = 1; $j <= 4; $j++)
                        @php
                            $checkerName = $slittingCheck->getCheckerName($j) ?? '-';
                        @endphp
                        <td colspan="2" style="text-align: center;">
                            {{ $checkerName }}
                        </td>
                    @endfor
                </tr>
                
                <!-- Penanggung Jawab (Approver) - Menampilkan nama approver melalui relasi -->
                <tr class="approver-row">
                    <td>-</td>
                    <td style="text-align: left; font-weight: bold;">Penanggung Jawab</td>
                    
                    @for($j = 1; $j <= 4; $j++)
                        @php
                            $approverName = $slittingCheck->getApproverName($j) ?? '-';
                        @endphp
                        <td colspan="2" style="text-align: center;">{{ $approverName }}</td>
                    @endfor
                </tr>
            </tbody>
        </table>

        <!-- Horizontal Note Boxes -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px;">
            <tr>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Keterangan Status</div>
                    • V : Baik/Normal<br>
                    • X : Tidak Baik/Abnormal<br>
                    • - : Tidak Diisi<br>
                    • OFF : Mesin Mati<br><br>
                </td>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Catatan Pemeriksaan</div>
                    • Pengecekan mesin dilakukan setiap minggu secara berkala<br>
                    • Pastikan semua komponen dalam kondisi baik<br>
                    • Laporkan segera jika ada abnormalitas
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan - Menggunakan nama dari relasi -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Dibuat oleh:</div>
                    <div style="font-weight: bold;">
                        @php
                            // Get unique checker names using the model's helper method
                            $checkerNames = [];
                            for ($i = 1; $i <= 4; $i++) {
                                $name = $slittingCheck->getCheckerName($i);
                                if ($name && !in_array($name, $checkerNames)) {
                                    $checkerNames[] = $name;
                                }
                            }
                            $checkers = !empty($checkerNames) ? implode(', ', $checkerNames) : '-';
                        @endphp
                        {{ $checkers }}
                    </div>
                    <div>Checker</div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Disetujui oleh:</div>
                    <div style="font-weight: bold;">
                        @php
                            // Get unique approver names using the model's helper method
                            $approverNames = [];
                            for ($i = 1; $i <= 4; $i++) {
                                $name = $slittingCheck->getApproverName($i);
                                if ($name && !in_array($name, $approverNames)) {
                                    $approverNames[] = $name;
                                }
                            }
                            $approvers = !empty($approverNames) ? implode(', ', $approverNames) : '........................';
                        @endphp
                        {{ $approvers }}
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