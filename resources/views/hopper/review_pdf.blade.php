<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Hopper</title>
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
        <h2>Detail Pencatatan Mesin Hopper</h2>

        <table class="header-table">
            <tr>
                <td width="50%"><span class="label">No Hopper:</span> {{ $hopperCheck->nomer_hopper }}</td>
                <td width="50%" style="text-align: right;"><span class="label">Bulan:</span> {{ \Carbon\Carbon::parse($hopperCheck->bulan)->translatedFormat('F Y') }}</td>
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
                @php
                    $items = [
                        1 => 'Filter',
                        2 => 'Selang'
                    ];
                @endphp
                
                @foreach($items as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                $hasChecker = !empty($hopperCheck->{'checked_by_minggu'.$j});
                                $resultValue = $hasChecker && isset($hopperCheck->{'check_'.$j}[$i]) ? $hopperCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($hopperCheck->{'keterangan_'.$j}[$i]) ? $hopperCheck->{'keterangan_'.$j}[$i] : '';
                            @endphp
                            <td class="status-normal">{{ $resultValue }}</td>
                            <td style="text-align: left;">{{ $keteranganValue }}</td>
                        @endfor
                    </tr>
                @endforeach
                
                <!-- Panel Kelistrikan Header -->
                <tr>
                    <td colspan="10" style="background-color: #f2f2f2; font-weight: bold;">Panel Kelistrikan</td>
                </tr>
                
                @php
                    $items = [
                        3 => 'Kontraktor',
                        4 => 'Temperatur Kontrol',
                        5 => 'MCB'
                    ];
                @endphp
                
                @foreach($items as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 4; $j++)
                            @php
                                $hasChecker = !empty($hopperCheck->{'checked_by_minggu'.$j});
                                $resultValue = $hasChecker && isset($hopperCheck->{'check_'.$j}[$i]) ? $hopperCheck->{'check_'.$j}[$i] : '-';
                                $keteranganValue = $hasChecker && isset($hopperCheck->{'keterangan_'.$j}[$i]) ? $hopperCheck->{'keterangan_'.$j}[$i] : '';
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
                    
                   @for($j = 1; $j <= 4; $j++)
                        @php
                            $checkedBy = optional($hopperCheck->{'checkerMinggu'.$j})->username ?? '-';
                            $tanggalRaw = $hopperCheck->{'tanggal_minggu'.$j};
                            $checkedDate = $tanggalRaw
                                ? \Carbon\Carbon::parse($tanggalRaw)
                                    ->locale('id')            // set locale ke Bahasa Indonesia
                                    ->isoFormat('D MMMM YYYY') // contoh: 9 Mei 2025
                                : '-';
                        @endphp
                        <td colspan="2" style="text-align: center;">
                            {{ $checkedBy ?: '-' }}
                            @if($checkedBy && $checkedBy !== '-')
                                <br>
                                <span style="font-size: 9px;">{{ $checkedDate }}</span>
                            @endif
                        </td>
                    @endfor
                </tr>
                
                <!-- Penanggung Jawab (Approver) -->
                <tr class="approver-row">
                    <td>-</td>
                    <td style="text-align: left; font-weight: bold;">Penanggung Jawab</td>
                    
                    @for($j = 1; $j <= 4; $j++)
                        @php
                            $approvedBy = optional($hopperCheck->{'approverMinggu'.$j})->username ?? '-';
                        @endphp
                        <td colspan="2" style="text-align: center;">{{ $approvedBy }}</td>
                    @endfor
                </tr>
            </tbody>
        </table>

        <!-- Horizontal Note Boxes -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px;">
            <tr>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Standar Kriteria Pemeriksaan</div>
                    • Filter: Kebersihan<br>
                    • Selang: Tidak bocor<br>
                    • Kontraktor: Baik<br>
                    • Temperatur Kontrol: Baik<br>
                    • MCB: Baik
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
                                optional($hopperCheck->checkerMinggu1)->username, 
                                optional($hopperCheck->checkerMinggu2)->username, 
                                optional($hopperCheck->checkerMinggu3)->username, 
                                optional($hopperCheck->checkerMinggu4)->username
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
                                optional($hopperCheck->approverMinggu1)->username, 
                                optional($hopperCheck->approverMinggu2)->username, 
                                optional($hopperCheck->approverMinggu3)->username, 
                                optional($hopperCheck->approverMinggu4)->username
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