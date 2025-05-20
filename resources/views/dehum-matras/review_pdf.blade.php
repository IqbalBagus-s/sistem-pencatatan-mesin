<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemeriksaan Dehum Matras</title>
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
            font-size: 9px;
        }
        .info-table td {
            padding: 4px;
            border: 1px solid #000;
        }
        .label {
            font-weight: bold;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 9px;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        .main-table th {
            background-color: #f2f2f2;
        }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
            font-size: 12px;
            background-color: #f2f2f2;
            padding: 5px;
            border: 1px solid #000;
            text-align: center;
        }
        .checker-row {
            background-color: #f2f2f2;
        }
        .approver-row {
            background-color: #e6f7ff;
        }
        
        /* Kriteria boxes - Diperbarui */
        .criteria-container {
            width: 100%;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .criteria-box {
            border: 1px solid #000;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .criteria-header {
            border-bottom: 1px solid #000;
            font-weight: bold;
            padding: 3px;
            text-align: center;
            font-size: 9px;
            background-color: #f2f2f2;
        }

        .criteria-content {
            display: flex;
            padding: 10px;
        }

        .criteria-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        
        .criteria-table td {
            padding: 3px 5px;
            vertical-align: top;
        }
        
        .criteria-label {
            font-weight: bold;
            width: 30%;
        }

        .criteria-value {
            width: 20%;
            text-align: right;
        }
        
        /* Tanda tangan */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 5px;
        }
        .signature-title {
            margin-bottom: 40px;
        }
        .signature-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .signature-role {
            font-size: 11px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .status-ok {
            color: green;
            font-weight: bold;
        }
        
        .status-perhatikan {
            color: orange;
            font-weight: bold;
        }
        
        .status-tidak-ok {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pemeriksaan Dehum Matras</h2>

        <table class="header-table" style="width:100%; margin-bottom:10px; font-size:0.95em;">
            <tr>
                <td><strong>Nomor Dehum Matras:</strong> {{ $dehumMatras->nomer_dehum_matras }}</td>
                <td style="text-align:right;"><strong>Bulan:</strong> {{ \Carbon\Carbon::parse($dehumMatras->bulan)->translatedFormat('F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Shift:</strong> {{ $dehumMatras->shift }}</td>
                <td style="text-align:right;"><strong>Checker:</strong> 
                    @php
                        $checkers = collect($results)
                            ->pluck('checked_by')
                            ->filter()
                            ->unique()
                            ->implode(', ');
                    @endphp
                    {{ $checkers }}
                </td>
            </tr>
        </table>

        <!-- Tabel untuk Minggu 1 dan 2 (Tanggal 1-15) -->
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Item Terperiksa</th>
                    <th colspan="15">Tanggal</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= 15; $i++)
                        <th>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 1; $j <= 15; $j++)
                            <td>
                                @php
                                    $result = collect($results)
                                        ->where('tanggal', $j)
                                        ->where('item_id', $i)
                                        ->first();
                                    echo $result ? $result['result'] : '-';
                                @endphp
                            </td>
                        @endfor
                    </tr>
                @endforeach
                <tr class="checker-row">
                    <td>-</td>
                    <td style="text-align: left;">Dibuat Oleh</td>
                    @for($j = 1; $j <= 15; $j++)
                        <td>
                            @php
                                $checker = collect($results)
                                    ->where('tanggal', $j)
                                    ->pluck('checked_by')
                                    ->filter()
                                    ->first();
                                echo $checker ?: '-';
                            @endphp
                        </td>
                    @endfor
                </tr>
                <tr class="approver-row">
                    <td>-</td>
                    <td style="text-align: left;">Penanggung Jawab</td>
                    @for($j = 1; $j <= 15; $j++)
                        <td>
                            @php
                                $approver = collect($results)
                                    ->where('tanggal', $j)
                                    ->pluck('approved_by')
                                    ->filter()
                                    ->first();
                                echo $approver ?: '-';
                            @endphp
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>

        <!-- Tabel untuk Minggu 3-5 (Tanggal 16-31) -->
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Item Terperiksa</th>
                    <th colspan="16">Tanggal</th>
                </tr>
                <tr>
                    @for ($i = 16; $i <= 31; $i++)
                        <th>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td style="text-align: left;">{{ $item }}</td>
                        
                        @for($j = 16; $j <= 31; $j++)
                            <td>
                                @php
                                    $result = collect($results)
                                        ->where('tanggal', $j)
                                        ->where('item_id', $i)
                                        ->first();
                                    echo $result ? $result['result'] : '-';
                                @endphp
                            </td>
                        @endfor
                    </tr>
                @endforeach
                <tr class="checker-row">
                    <td>-</td>
                    <td style="text-align: left;">Dibuat Oleh</td>
                    @for($j = 16; $j <= 31; $j++)
                        <td>
                            @php
                                $checker = collect($results)
                                    ->where('tanggal', $j)
                                    ->pluck('checked_by')
                                    ->filter()
                                    ->first();
                                echo $checker ?: '-';
                            @endphp
                        </td>
                    @endfor
                </tr>
                <tr class="approver-row">
                    <td>-</td>
                    <td style="text-align: left;">Penanggung Jawab</td>
                    @for($j = 16; $j <= 31; $j++)
                        <td>
                            @php
                                $approver = collect($results)
                                    ->where('tanggal', $j)
                                    ->pluck('approved_by')
                                    ->filter()
                                    ->first();
                                echo $approver ?: '-';
                            @endphp
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>

        <!-- Kriteria Standar Pemeriksaan yang sudah diperbarui -->
        <div class="criteria-container">
            <div class="criteria-box">
                <div class="criteria-header">Standar Kriteria Pemeriksaan Dehum Matras</div>
                <div class="criteria-content">
                    <table class="criteria-table">
                        <tr>
                            <td class="criteria-label">Kompressor:</td>
                            <td class="criteria-value">50°C - 70°C</td>
                            <td class="criteria-label">Water Cooler in:</td>
                            <td class="criteria-value">31°C - 33°C</td>
                        </tr>
                        <tr>
                            <td class="criteria-label">Kabel:</td>
                            <td class="criteria-value">35°C - 45°C</td>
                            <td class="criteria-label">Water Cooler Out:</td>
                            <td class="criteria-value">32°C - 36°C</td>
                        </tr>
                        <tr>
                            <td class="criteria-label">NFB:</td>
                            <td class="criteria-value">35°C - 50°C</td>
                            <td class="criteria-label">Temperatur Output Udara:</td>
                            <td class="criteria-value">18°C - 28°C</td>
                        </tr>
                        <tr>
                            <td class="criteria-label">Motor:</td>
                            <td class="criteria-value">40°C - 55°C</td>
                            <td colspan="2"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tanda Tangan -->
        @php
            $checkedNames = $results->pluck('checked_by')->unique()->filter()->values();
            $approvedNames = $results->pluck('approved_by')->unique()->filter()->values();
        @endphp

        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="signature-title">Mengetahui:</div>
                    <div class="signature-name">
                        @foreach ($checkedNames as $index => $name)
                            {{ $name }}@if (!$loop->last), @endif
                        @endforeach
                    </div>
                    <div class="signature-role">Checker</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-title">Mengetahui:</div>
                    <div class="signature-name">
                        @foreach ($approvedNames as $index => $name)
                            {{ $name }}@if (!$loop->last), @endif
                        @endforeach
                    </div>
                    <div class="signature-role">Penanggung jawab</div>
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