<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemeriksaan Mesin Autoloader</title>
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
        <h2>Detail Pemeriksaan Mesin Autoloader</h2>

        <table class="header-table" style="width:100%; margin-bottom:10px; font-size:0.95em;">
            <tr>
                <td><strong>Nomor Autoloader:</strong> {{ $check->nomer_autoloader }}</td>
                <td style="text-align:right;"><strong>Bulan:</strong> {{ \Carbon\Carbon::parse($check->bulan)->translatedFormat('F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Shift:</strong> {{ $check->shift }}</td>
                <td style="text-align:right;"><strong>Checker:</strong> 
                    @php
                        $checkers = collect($results)
                            ->whereNotNull('checker_name')
                            ->pluck('checker_name')
                            ->unique()
                            ->filter()
                            ->implode(', ');
                    @endphp
                    {{ $checkers }}
                </td>
            </tr>
        </table>

        <!-- Tabel untuk tanggal 1-15 -->
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
                @php
                    $items = [
                        1 => 'Filter',
                        2 => 'Selang',
                        3 => 'Panel Kelistrikan',
                        4 => 'Kontaktor',
                        5 => 'Thermal Overload',
                        6 => 'MCB',
                    ];
                @endphp
                
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
                                    ->whereNotNull('checker_name')
                                    ->pluck('checker_name')
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
                                    ->whereNotNull('approver_name')
                                    ->pluck('approver_name')
                                    ->first();
                                echo $approver ?: '-';
                            @endphp
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>

        <!-- Tabel untuk tanggal 16-31 -->
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
                                    ->whereNotNull('checker_name')
                                    ->pluck('checker_name')
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
                                    ->whereNotNull('approver_name')
                                    ->pluck('approver_name')
                                    ->first();
                                echo $approver ?: '-';
                            @endphp
                        </td>
                    @endfor
                </tr>
            </tbody>
        </table>

        <!-- Kriteria Standar Pemeriksaan Mesin Autoloader -->
        <table style="width: 100%; table-layout: fixed; margin-bottom: 10px;">
            <tr>
                <td style="vertical-align: top; border: 1px solid #000; padding: 5px; width: 50%;">
                    <div class="note-title">Standar Kriteria Pemeriksaan</div>
                    • Filter: Kebersihan<br>
                    • Selang: Tidak bocor<br>
                    • Panel Kelistrikan: Berfungsi<br>
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
        @php
            $checkedNames = collect($results)->whereNotNull('checker_name')->pluck('checker_name')->unique()->filter()->values();
            $approvedNames = collect($results)->whereNotNull('approver_name')->pluck('approver_name')->unique()->filter()->values();
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
                <td style="text-align: left;"><strong>Nomor Form:</strong> {{ $form->nomor_form ?? 'AUTO-FORM-'.date('Ymd') }}</td>
                <td style="text-align: right;"><strong>Tanggal Efektif:</strong> {{ $formattedTanggalEfektif ?? \Carbon\Carbon::now()->format('d-m-Y') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>