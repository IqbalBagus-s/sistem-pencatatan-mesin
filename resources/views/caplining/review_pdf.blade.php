<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Caplining</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pencatatan Mesin Caplining</h2>

        <table class="header-table">
            <tr>
                <td width="50%"><span class="label">No Caplining:</span> Caplining {{ $capliningCheck->nomer_caplining }}</td>
            </tr>
        </table>

        <!-- Tabel Utama Inspeksi -->
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Item Terperiksa</th>
                    @for($j = 1; $j <= 5; $j++)
                        @php
                            $tanggalField = "tanggal_check{$j}";
                            $tanggalValue = $capliningCheck->$tanggalField;
                            $displayDate = $tanggalValue ? \Carbon\Carbon::parse($tanggalValue)->locale('id')->isoFormat('D MMM YY') : 'Check ' . $j;
                        @endphp
                        <th colspan="2">{{ $displayDate }}</th>
                    @endfor
                </tr>
                <tr>
                    @for($j = 1; $j <= 5; $j++)
                        <th>Check</th>
                        <th>Keterangan</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @php
                    // Items pertama (1-10)
                    $firstItems = [
                        1 => 'Kelistrikan',
                        2 => 'MCB',
                        3 => 'PLC',
                        4 => 'Power Supply',
                        5 => 'Relay',
                        6 => 'Selenoid',
                        7 => 'Selang Angin',
                        8 => 'Regulator',
                        9 => 'Pir',
                        10 => 'Motor',
                    ];
                @endphp
                
                @foreach($firstItems as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 5; $j++)
                            @php
                                $tanggalField = "tanggal_check{$j}";
                                $hasChecker = !empty($capliningCheck->$tanggalField);
                                $resultValue = $hasChecker && isset($capliningCheck->{'check_'.$j}[$i]) ? $capliningCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($capliningCheck->{'keterangan_'.$j}[$i]) ? $capliningCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                @php
                    // Items kedua (11-20) - Mekanik
                    $secondItems = [
                        11 => 'Vanbelt',
                        12 => 'Conveyor',
                        13 => 'Motor Conveyor',
                        14 => 'Vibrator',
                        15 => 'Motor Vibrator',
                        16 => 'Gear Box',
                        17 => 'Rantai',
                        18 => 'Stang Penggerak',
                        19 => 'Suction Pad',
                        20 => 'Sensor',
                    ];
                @endphp
                
                @foreach($secondItems as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 5; $j++)
                            @php
                                $tanggalField = "tanggal_check{$j}";
                                $hasChecker = !empty($capliningCheck->$tanggalField);
                                $resultValue = $hasChecker && isset($capliningCheck->{'check_'.$j}[$i]) ? $capliningCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($capliningCheck->{'keterangan_'.$j}[$i]) ? $capliningCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                <!-- Dibuat Oleh (Checker) -->
                <tr class="checker-row">
                    <td>-</td>
                    <td style="text-align: left; font-weight: bold;">Dibuat Oleh</td>
                    
                   @for($j = 1; $j <= 5; $j++)
                        @php
                            $checkedByField = "checked_by{$j}";
                            $tanggalField = "tanggal_check{$j}";
                            $checkedBy = $capliningCheck->$checkedByField ?? '';
                            $tanggalRaw = $capliningCheck->$tanggalField;
                            $checkedDate = $tanggalRaw
                                ? \Carbon\Carbon::parse($tanggalRaw)
                                    ->locale('id')            // set locale ke Bahasa Indonesia
                                    ->isoFormat('D MMMM YYYY') // contoh: 9 Mei 2025
                                : '-';
                        @endphp
                        <td colspan="2" style="text-align: center;">
                            {{ $checkedBy ?: '-' }}
                        </td>
                    @endfor
                </tr>
                
                <!-- Penanggung Jawab (Approver) -->
                <tr class="approver-row">
                    <td>-</td>
                    <td style="text-align: left; font-weight: bold;">Penanggung Jawab</td>
                    
                    @for($j = 1; $j <= 5; $j++)
                        @php
                            $approvedByField = "approved_by{$j}";
                            $approvedBy = $capliningCheck->$approvedByField ?? '-';
                        @endphp
                        <td colspan="2" style="text-align: center;">{{ $approvedBy }}</td>
                    @endfor
                </tr>
            </tbody>
        </table>

        <!-- Horizontal Note Boxes -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px; font-size: 11px;">
            <tr>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Catatan Pemeriksaan</div>
                    • Pengecekan mesin, empat hari sebelum mesin dijadwalkan jalan<br>
                    • Pastikan semua komponen dalam kondisi baik<br>
                    • Laporkan segera jika ada kerusakan atau abnormalitas
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

        <!-- Tanda Tangan -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Dibuat oleh:</div>
                    <div style="font-weight: bold;">
                        @php
                            // Get unique checker names
                            $checkers = collect([
                                $capliningCheck->checked_by1, 
                                $capliningCheck->checked_by2, 
                                $capliningCheck->checked_by3, 
                                $capliningCheck->checked_by4,
                                $capliningCheck->checked_by5
                            ])->filter()->unique()->values()->implode(', ') ?? '-';
                        @endphp
                        {{ $checkers }}
                    </div>
                    <div>Checker</div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="margin-bottom: 40px;">Disetujui oleh:</div>
                    <div style="font-weight: bold;">
                        @php
                            // Get unique approver names
                            $approvers = collect([
                                $capliningCheck->approved_by1, 
                                $capliningCheck->approved_by2, 
                                $capliningCheck->approved_by3, 
                                $capliningCheck->approved_by4,
                                $capliningCheck->approved_by5
                            ])->filter()->unique()->values()->implode(', ') ?? '........................';
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