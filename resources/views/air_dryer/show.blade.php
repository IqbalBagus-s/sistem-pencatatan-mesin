<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin Air Dryer</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style> 
        .footer {
            background-color: #ffffff;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
            margin-top: auto;
            width: 100%;
        }
    </style>
</head>
<body class="bg-light p-3">

    <div class="container bg-white p-4 rounded shadow">
        <h2 class="mb-4 fw-bold">Approval Pencatatan Mesin Air Dryer</h2>

        <form action="{{ route('air-dryer.approve', $check->id) }}" method="POST">
            @csrf

            <!-- Tampilkan nama approver yang sedang login -->
            <div class="mb-4 p-3 bg-light rounded">
                @if(!$check->approved_by)
                <p class="fs-5 fw-semibold">Approver: 
                    <span class="text-primary">{{ Auth::user()->username }}</span></p>
                @else
                <p class="fs-5 fw-semibold">Approver: 
                    <span class="text-primary">{{ $check->approved_by }}</span></p>
                @endif
            </div>

            <!-- Tampilkan nama checker yang mengisi data -->
            <div class="mb-4 p-3 bg-light rounded">
                <p class="fs-5 fw-semibold">Checker: 
                    <span class="text-success">{{ $check->checked_by }}</span></p>
            </div>

            {{-- tanggal form di isi --}}
            <div class="mb-3">
                <label class="form-label">Tanggal:</label>
                <input type="date" value="{{ $check->tanggal }}" class="form-control bg-light" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Hari:</label>
                <input type="text" value="{{ $check->hari }}" class="form-control bg-light" readonly>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nomor Mesin</th>
                            <th>Temperatur Kompresor</th>
                            <th>Temperatur Kabel</th>
                            <th>Temperatur MCB</th>
                            <th>Temperatur Angin In</th>
                            <th>Temperatur Angin Out</th>
                            <th>Evaporator</th>
                            <th>Fan Evaporator</th>
                            <th>Auto Drain</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ $result->nomor_mesin }}</td>
                            <td class="text-center">{{ $result->temperatur_kompresor }}</td>
                            <td class="text-center">{{ $result->temperatur_kabel }}</td>
                            <td class="text-center">{{ $result->temperatur_mcb }}</td>
                            <td class="text-center">{{ $result->temperatur_angin_in }}</td>
                            <td class="text-center">{{ $result->temperatur_angin_out }}</td>
                            <td class="text-center">{{ $result->evaporator }}</td>
                            <td class="text-center">{{ $result->fan_evaporator }}</td>
                            <td class="text-center">{{ $result->auto_drain }}</td>
                            <td class="text-center">{{ $result->keterangan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 p-3 bg-light rounded col-md-6">
                <h3 class="fs-5 fw-semibold mb-2">Detail Mesin:</h3>
                <p>AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                <p>AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                <p>AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                <p>AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                @if(!$check->approved_by)
                <a href="{{ route('air-dryer.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
                <button type="submit" class="btn btn-success">
                    Setujui
                </button>
                @else
                <a href="{{ route('air-dryer.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
                <a href="{{ route('air-dryer.downloadPdf', $check->id) }}" class="btn btn-primary">
                    Download PDF
                </a>
                <button type="submit" class="btn btn-secondary" disabled>
                    Telah Disetujui
                </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0 fw-bold">2025 © PT ASIA PRAMULIA</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>