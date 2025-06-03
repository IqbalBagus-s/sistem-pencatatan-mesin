<!DOCTYPE html>

<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Compressor</title>
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
        
        /* Perbaikan untuk kriteria boxes */
        .criteria-container {
            width: 100%; 
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
            font-size: 9px;
        }
        .criteria-cell {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }
        .criteria-box {
            border: 1px solid #333;
            padding: 8px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .criteria-header {
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 10px;
            text-align: center;
        }
        .criteria-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .criteria-list li {
            margin-bottom: 3px;
            font-size: 9px;
            line-height: 1.3;
        }
        .criteria-list li strong {
            display: inline-block;
            width: 110px;
            font-size: 9px;
        }
        .criteria-list li {
            margin-bottom: 2px;
            font-size: 9px;
            line-height: 1.2;
        }
        
        /* Tanda tangan */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .signature-cell {
            width: 25%;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pencatatan Mesin Compressor</h2>

        <table class="header-table" style="width:100%; margin-bottom:10px; font-size:0.95em;">
            <tr>
                <td><strong>Hari:</strong> {{ $check->hari }}</td>
                <td style="text-align:right;"><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($check->tanggal)->translatedFormat('d F Y') }}</td>
            </tr>
        </table>

        <div class="box" style="margin-bottom: 3px;">
            <div class="section-title">Informasi Compressor</div>
            <table class="info-table">
                <tr>
                    <td>KL Aktif<br><strong>{{ $check->kompressor_on_kl }}</strong></td>
                    <td>KH Aktif<br><strong>{{ $check->kompressor_on_kh }}</strong></td>
                    <td>Mesin ON<br><strong>{{ $check->mesin_on }}</strong></td>
                    <td>Mesin OFF<br><strong>{{ $check->mesin_off }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="box" style="margin-top: 0;">
            <div class="section-title">Kelembapan Udara</div>
            <table class="info-table">
                <tr>
                    <td>
                        Shift 1<br>
                        Temperature: <strong>{{ $check->temperatur_shift1 ?: '-' }}°C</strong><br>
                        Humidity: <strong>{{ $check->humidity_shift1 ?: '-' }}°C</strong>
                    </td>
                    <td>
                        Shift 2<br>
                        Temperature: <strong>{{ $check->temperatur_shift2 ?: '-' }}°C</strong><br>
                        Humidity: <strong>{{ $check->humidity_shift2 ?: '-' }}°C</strong>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </table>
        </div>

        <!-- Low Compressor -->
        <div class="section-title">Data Low Compressor</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="3">No.</th>
                    <th rowspan="3">Checked Items</th>
                    <th colspan="12">Hasil Pemeriksaan</th>
                </tr>
                <tr>
                    <th colspan="2">KL 10</th>
                    <th colspan="2">KL 5</th>
                    <th colspan="2">KL 6</th>
                    <th colspan="2">KL 7</th>
                    <th colspan="2">KL 8</th>
                    <th colspan="2">KL 9</th>
                </tr>
                <tr>
                    @for ($i = 0; $i < 6; $i++)
                        <th>I</th>
                        <th>II</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($lowResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                    @php 
                        $result = $resultGroup->first();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: left;">{{ $result->checked_items }}</td>
                        
                        @php
                            $klDbColumns = ['kl_10I', 'kl_10II', 'kl_5I', 'kl_5II', 'kl_6I', 'kl_6II', 'kl_7I', 'kl_7II', 'kl_8I', 'kl_8II', 'kl_9I', 'kl_9II'];
                        @endphp
                        
                        @foreach ($klDbColumns as $klColumn)
                            <td class="status-normal">{{ $result->$klColumn ?: '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- High Compressor -->
        <div class="section-title">Data High Compressor</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="3">No.</th>
                    <th rowspan="3">Checked Items</th>
                    <th colspan="10">Hasil Pemeriksaan</th>
                </tr>
                <tr>
                    <th colspan="2">KH 7</th>
                    <th colspan="2">KH 8</th>
                    <th colspan="2">KH 9</th>
                    <th colspan="2">KH 10</th>
                    <th colspan="2">KH 11</th>
                </tr>
                <tr>
                    @for ($i = 0; $i < 5; $i++)
                        <th>I</th>
                        <th>II</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($highResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                    @php 
                        $result = $resultGroup->first();
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td style="text-align: left;">{{ $result->checked_items }}</td>
                        
                        @php
                            $khDbColumns = ['kh_7I', 'kh_7II', 'kh_8I', 'kh_8II', 'kh_9I', 'kh_9II', 'kh_10I', 'kh_10II', 'kh_11I', 'kh_11II'];
                        @endphp
                        
                        @foreach ($khDbColumns as $khColumn)
                            <td class="status-normal">{{ $result->$khColumn ?: '-' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Horizontal Note Boxes (UPDATED & DIPERBAIKI) -->
        <table class="criteria-container">
            <tr>
                <!-- Low Compressor -->
                <td class="criteria-cell">
                    <div class="criteria-box">
                        <div class="criteria-header">Standar Kriteria Pemeriksaan Low Compressor</div>
                        <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                            <tr>
                                <td style="width: 26%; vertical-align: top; padding-right: 3px;">
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Motor:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Screw:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Oil:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Outlet:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur MCB:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Kabel:</strong></div>
                                </td>
                                <td style="width: 24%; vertical-align: top; text-align: left;">
                                    <div style="margin-bottom: 2px;">50–75 °C</div>
                                    <div style="margin-bottom: 2px;">60–90 °C</div>
                                    <div style="margin-bottom: 2px;">80–105 °C</div>
                                    <div style="margin-bottom: 2px;">30–55 °C</div>
                                    <div style="margin-bottom: 2px;">30–50 °C</div>
                                    <div style="margin-bottom: 2px;">30–55 °C</div>
                                </td>
                                <td style="width: 26%; vertical-align: top; padding-left: 3px;">
                                    <div style="margin-bottom: 2px;"><strong>Oil Compressor:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Filter (Air/Oil):</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Suara Mesin:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Voltage:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur ADT:</strong></div>
                                </td>
                                <td style="width: 24%; vertical-align: top; text-align: left;">
                                    <div style="margin-bottom: 2px;">Penuh/Ditambah</div>
                                    <div style="margin-bottom: 2px;">Bersih/Kotor</div>
                                    <div style="margin-bottom: 2px;">Halus/Kasar</div>
                                    <div style="margin-bottom: 2px;">&gt; 380 V</div>
                                    <div style="margin-bottom: 2px;">80–50 °C</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>

                <!-- High Compressor -->
                <td class="criteria-cell">
                    <div class="criteria-box">
                        <div class="criteria-header">Standar Kriteria Pemeriksaan High Compressor</div>
                        <table style="width: 100%; border-collapse: collapse; margin-top: 5px;">
                            <tr>
                                <td style="width: 26%; vertical-align: top; padding-right: 3px;">
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Motor:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Piston:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Oil:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Outlet:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur MCB:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Temperatur Kabel:</strong></div>
                                </td>
                                <td style="width: 24%; vertical-align: top; text-align: left;">
                                    <div style="margin-bottom: 2px;">50–70 °C</div>
                                    <div style="margin-bottom: 2px;">80–105 °C</div>
                                    <div style="margin-bottom: 2px;">80–100 °C</div>
                                    <div style="margin-bottom: 2px;">30–55 °C</div>
                                    <div style="margin-bottom: 2px;">30–50 °C</div>
                                    <div style="margin-bottom: 2px;">30–55 °C</div>
                                </td>
                                <td style="width: 26%; vertical-align: top; padding-left: 3px;">
                                    <div style="margin-bottom: 2px;"><strong>Oil Compressor:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Filter (Air/Oil):</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Suara Mesin:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Voltage:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Inlet Pressure:</strong></div>
                                    <div style="margin-bottom: 2px;"><strong>Outlet Pressure:</strong></div>
                                </td>
                                <td style="width: 24%; vertical-align: top; text-align: left;">
                                    <div style="margin-bottom: 2px;">Penuh/Ditambah</div>
                                    <div style="margin-bottom: 2px;">Bersih/Kotor</div>
                                    <div style="margin-bottom: 2px;">Halus/Kasar</div>
                                    <div style="margin-bottom: 2px;">&gt; 380 V</div>
                                    <div style="margin-bottom: 2px;">8 Bar–9 Bar</div>
                                    <div style="margin-bottom: 2px;">22 Bar–30 Bar</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan (DIPERBAIKI) -->
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="signature-title">Dibuat oleh:</div>
                    <div class="signature-name">
                        {{ $check->checkerShift1 ? $check->checkerShift1->username : '.............' }}
                    </div>
                    <div class="signature-role">Checker Shift 1</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-title">Dibuat oleh:</div>
                    <div class="signature-name">
                        {{ $check->checkerShift2 ? $check->checkerShift2->username : '.............' }}
                    </div>
                    <div class="signature-role">Checker Shift 2</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-title">Disetujui oleh:</div>
                    <div class="signature-name">
                        {{ $check->approverShift1 ? $check->approverShift1->username : '.............' }}
                    </div>
                    <div class="signature-role">Penanggung Jawab Shift 1</div>
                </td>
                <td class="signature-cell">
                    <div class="signature-title">Disetujui oleh:</div>
                    <div class="signature-name">
                        {{ $check->approverShift2 ? $check->approverShift2->username : '.............' }}
                    </div>
                    <div class="signature-role">Penanggung Jawab Shift 2</div>
                </td>
            </tr>
        </table>

        <table style="width: 100%; margin-bottom: 10px; margin-top: 20px;">
            <tr>
                <td style="text-align: left;"><strong>Nomor Form:</strong> {{ $form->nomor_form ?? 'FM-MT-CP-001' }}</td>
                <td style="text-align: right;"><strong>Tanggal Efektif:</strong> {{ $formattedTanggalEfektif ?? date('d F Y') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>