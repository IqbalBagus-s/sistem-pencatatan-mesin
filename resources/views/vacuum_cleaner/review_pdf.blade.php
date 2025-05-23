<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pencatatan Mesin Vacuum Cleaner</title>
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
            margin-bottom: 15px;
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
        .main-table td.text-left {
            text-align: left;
        }
        .note-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 12px;
            color: #333;
        }
        .status-normal {
            font-weight: bold;
        }
        .checker-row {
            background-color: #e6f7ff;
        }
        .approver-row {
            background-color: #f0f9ff;
        }
        
        /* Perbaikan untuk horizontal note boxes - menggunakan border yang sama dengan tabel */
        .note-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            margin-top: 20px;
            border: 1px solid #000;
        }
        .note-container td {
            vertical-align: top;
            border-right: 1px solid #000;
            padding: 10px;
            width: 50%;
            font-size: 9px;
            line-height: 1.5;
        }
        .note-container td:last-child {
            border-right: none;
        }
        .note-list {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }
        .note-list li {
            margin-bottom: 4px;
            padding-left: 12px;
            position: relative;
        }
        .note-list li:before {
            content: "•";
            position: absolute;
            left: 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detail Pencatatan Mesin Vacuum Cleaner</h2>

        <table class="header-table">
            <tr>
                <td width="50%"><span class="label">No Vacuum Cleaner:</span> {{ $vacuumCheck->nomer_vacum_cleaner }}</td>
                <td width="50%" style="text-align: right;"><span class="label">Bulan:</span> {{ \Carbon\Carbon::parse($vacuumCheck->bulan)->translatedFormat('F Y') }}</td>
            </tr>
        </table>

        <!-- Tabel Minggu 02 -->
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 8%;">No</th>
                    <th rowspan="2" style="width: 40%;">Item Terperiksa</th>
                    <th colspan="1" style="width: 16%;">Minggu 02</th>
                    <th rowspan="2" style="width: 36%;">Keterangan</th>
                </tr>
                <tr>
                    <th>Check</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td class="text-left">{{ $item }}</td>
                        
                        @php
                            $hasChecker = !empty($vacuumCheck->checker_minggu2);
                            $resultValue = $hasChecker && isset($vacuumCheck->check_2[$i]) ? $vacuumCheck->check_2[$i] : '-';
                            $keteranganValue = $hasChecker && isset($vacuumCheck->keterangan_2[$i]) ? $vacuumCheck->keterangan_2[$i] : '';
                        @endphp
                        
                        <td class="status-normal">{{ $resultValue }}</td>
                        <td class="text-left">{{ $keteranganValue }}</td>
                    </tr>
                @endforeach
                
                <!-- Dibuat Oleh (Checker) -->
                <tr class="checker-row">
                    <td>-</td>
                    <td class="text-left" style="font-weight: bold;">Dibuat Oleh</td>
                    
                    @php
                        $checkedBy = $vacuumCheck->checker_minggu2 ?? '';
                        $tanggalRaw = $vacuumCheck->tanggal_dibuat_minggu2;
                        $checkedDate = $tanggalRaw
                            ? \Carbon\Carbon::parse($tanggalRaw)
                                ->locale('id')
                                ->isoFormat('D MMMM YYYY')
                            : '-';
                    @endphp
                    
                    <td colspan="2" style="text-align: center;">
                        {{ $checkedBy ?: '-' }}
                        @if($checkedBy)
                            <br>
                            <span style="font-size: 9px;">{{ $checkedDate }}</span>
                        @endif
                    </td>
                </tr>
                
                <!-- Penanggung Jawab (Approver) -->
                <tr class="approver-row">
                    <td>-</td>
                    <td class="text-left" style="font-weight: bold;">Penanggung Jawab</td>
                    
                    @php
                        $approvedBy = $vacuumCheck->approver_minggu2 ?? '-';
                    @endphp
                    
                    <td colspan="2" style="text-align: center;">{{ $approvedBy }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Tabel Minggu 04 -->
        <table class="main-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 8%;">No</th>
                    <th rowspan="2" style="width: 40%;">Item Terperiksa</th>
                    <th colspan="1" style="width: 16%;">Minggu 04</th>
                    <th rowspan="2" style="width: 36%;">Keterangan</th>
                </tr>
                <tr>
                    <th>Check</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                    <tr>
                        <td>{{ $i }}</td>
                        <td class="text-left">{{ $item }}</td>
                        
                        @php
                            $hasChecker = !empty($vacuumCheck->checker_minggu4);
                            $resultValue = $hasChecker && isset($vacuumCheck->check_4[$i]) ? $vacuumCheck->check_4[$i] : '-';
                            $keteranganValue = $hasChecker && isset($vacuumCheck->keterangan_4[$i]) ? $vacuumCheck->keterangan_4[$i] : '';
                        @endphp
                        
                        <td class="status-normal">{{ $resultValue }}</td>
                        <td class="text-left">{{ $keteranganValue }}</td>
                    </tr>
                @endforeach
                
                <!-- Dibuat Oleh (Checker) -->
                <tr class="checker-row">
                    <td>-</td>
                    <td class="text-left" style="font-weight: bold;">Dibuat Oleh</td>
                    
                    @php
                        $checkedBy = $vacuumCheck->checker_minggu4 ?? '';
                        $tanggalRaw = $vacuumCheck->tanggal_dibuat_minggu4;
                        $checkedDate = $tanggalRaw
                            ? \Carbon\Carbon::parse($tanggalRaw)
                                ->locale('id')
                                ->isoFormat('D MMMM YYYY')
                            : '-';
                    @endphp
                    
                    <td colspan="2" style="text-align: center;">
                        {{ $checkedBy ?: '-' }}
                        @if($checkedBy)
                            <br>
                            <span style="font-size: 9px;">{{ $checkedDate }}</span>
                        @endif
                    </td>
                </tr>
                
                <!-- Penanggung Jawab (Approver) -->
                <tr class="approver-row">
                    <td>-</td>
                    <td class="text-left" style="font-weight: bold;">Penanggung Jawab</td>
                    
                    @php
                        $approvedBy = $vacuumCheck->approver_minggu4 ?? '-';
                    @endphp
                    
                    <td colspan="2" style="text-align: center;">{{ $approvedBy }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Horizontal Note Boxes - SUDAH DIPERBAIKI -->
        <table class="note-container">
            <tr>
                <td>
                    <div class="note-title">Keterangan Status</div>
                        <ul class="note-list">
                            <li><strong>V</strong> : Baik/Normal</li>
                            <li><strong>X</strong> : Tidak Baik/Abnormal</li>
                            <li><strong>-</strong> : Tidak Diisi</li>
                            <li><strong>OFF</strong> : Mesin Mati</li>
                        </ul>
                </td>
                <td>
                    <div class="note-title">Daftar Vacuum Cleaner</div>
                    <ul class="note-list">
                        <li>Vacuum Cleaner No 1: Nilvis</li>
                        <li>Vacuum Cleaner No 2: Modif</li>
                        <li>Vacuum Cleaner No 3: Ransel</li>
                    </ul>
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
                                $vacuumCheck->checker_minggu2, 
                                $vacuumCheck->checker_minggu4
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
                                $vacuumCheck->approver_minggu2, 
                                $vacuumCheck->approver_minggu4
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