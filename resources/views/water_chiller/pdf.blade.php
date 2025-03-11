<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin Water Chiller</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid black;
            padding: 2px;
            text-align: center;
            font-size: 7px;
            height: auto;
            word-wrap: break-word;
        }
        th {
            background-color: #f2f2f2;
        }
        .item-column {
            width: 80px;
            white-space: normal;
            height: auto;
        }
        .no-column {
            width: 5px !important;
            min-width: 5px !important;
            max-width: 5px !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .standard-column {
            width: 60px;
            white-space: normal;
        }
        .ch-column {
            width: 18px;
        }
        .temp-text {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            white-space: nowrap;
            height: 60px;
            vertical-align: middle;
            text-align: center;
        }
        .header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 5px;
            font-size: 10px;
        }
        .footer {
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
        }
        .approval-box {
            border: 1px solid black;
            width: 120px;
            height: 60px;
            text-align: center;
            padding-top: 10px;
        }
        .form-info {
            font-size: 8px;
            margin-top: 15px;
        }
        .name-text {
            text-align: center;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        Approval Pencatatan Mesin Water Chiller
    </div>

    <div class="info">
        <strong>Approver:</strong> {{ Auth::user()->username }} <br>
        <strong>Checker:</strong> {{ $check->checked_by }} <br>
        <strong>Tanggal:</strong> {{ $check->tanggal }} <br>
        <strong>Hari:</strong> {{ $check->hari }} <br>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="no-column"> No</th>
                <th rowspan="2" class="item-column">ITEM YANG DIPERIKSA</th>
                <th rowspan="2" class="standard-column">STANDART</th>
                <th colspan="32">HASIL PEMERIKSAAN</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 32; $i++)
                <th class="ch-column">CH{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td class="item-column">Tem<br>pera<br>Tur<br>Com<br>Pres<br>or</td>
                <td>30 °c - 60°c</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[0]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>2</td>
                <td class="item-column">Tem<br>pera<br>tur<br>Kabel</td>
                <td>30 °c - 45°c</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[1]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>3</td>
                <td class="item-column">Tem<br>pera<br>tur<br>Mcb</td>
                <td>30 °c - 50°c</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[2]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>4</td>
                <td class="item-column">Tem<br>pera<br>tur<br>Air</td>
                <td>Sesuai<br>Setelan</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[3]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>5</td>
                <td class="item-column">Tem<br>pera<br>tur<br>Pompa</td>
                <td>40 °c - 50°c</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[4]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>6</td>
                <td class="item-column">Evaporator</td>
                <td>Bersih</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[5]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>7</td>
                <td class="item-column">Fan<br>Evapo<br>rator</td>
                <td>Suara Halus</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[6]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>8</td>
                <td class="item-column">Freon</td>
                <td>Cukup</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[7]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
            <tr>
                <td>9</td>
                <td class="item-column">Air</td>
                <td>Cukup</td>
                @for ($j = 1; $j <= 32; $j++)
                <td>{{ $results[8]->{"CH{$j}"} ?? '' }}</td>
                @endfor
            </tr>
        </tbody>
    </table>

    <div class="info" style="margin-top: 10px;">
        <strong>Keterangan:</strong> {{ $check->keterangan }}
    </div>
</body>
</html>