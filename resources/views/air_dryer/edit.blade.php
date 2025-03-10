<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pencatatan Mesin Air Dryer</title>
 
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <style>
        .auto-drain-column {
            min-width: 140px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .footer {
            background-color: #ffffff;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
            margin-top: auto;
            width: 100%;
        }
        .form-select-auto-drain {
            width: 100%;
        }
    </style>
</head>
<body class="bg-light p-3">

    <div class="container bg-white p-4 rounded shadow">
        <h2 class="fw-semibold text-dark mb-4">Edit Pencatatan Mesin Air Dryer</h2>

        <form action="{{ route('air-dryer.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Tanggal:</label>
                <input type="date" name="tanggal" value="{{ $check->tanggal }}" class="form-control bg-light" readonly>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Hari:</label>
                <input type="text" name="hari" value="{{ $check->hari }}" class="form-control bg-light" readonly>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 120px;">Nomor Mesin</th>
                            <th class="text-center">Temperatur Kompresor</th>
                            <th class="text-center">Temperatur Kabel</th>
                            <th class="text-center">Temperatur MCB</th>
                            <th class="text-center">Temperatur Angin In</th>
                            <th class="text-center">Temperatur Angin Out</th>
                            <th class="text-center" style="width: 110px;">Evaporator</th>
                            <th class="text-center" style="width: 140px;">Fan Evaporator</th>
                            <th class="text-center auto-drain-column">Auto Drain</th>
                            <th class="text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center">
                                <input type="text" name="nomor_mesin[{{ $result->id }}]" value="{{ $result->nomor_mesin }}" class="form-control form-control-sm bg-light" readonly>
                            </td>
                            <td>
                                <input type="text" name="temperatur_kompresor[{{ $result->id }}]" value="{{ $result->temperatur_kompresor }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="text" name="temperatur_kabel[{{ $result->id }}]" value="{{ $result->temperatur_kabel }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="text" name="temperatur_mcb[{{ $result->id }}]" value="{{ $result->temperatur_mcb }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="text" name="temperatur_angin_in[{{ $result->id }}]" value="{{ $result->temperatur_angin_in }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <input type="text" name="temperatur_angin_out[{{ $result->id }}]" value="{{ $result->temperatur_angin_out }}" class="form-control form-control-sm" required>
                            </td>
                            <td>
                                <select name="evaporator[{{ $result->id }}]" class="form-select form-select-sm">
                                    <option value="Bersih" {{ $result->evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                    <option value="Kotor" {{ $result->evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                    <option value="OFF" {{ $result->evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td>
                                <select name="fan_evaporator[{{ $result->id }}]" class="form-select form-select-sm">
                                    <option value="Suara Halus" {{ $result->fan_evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                    <option value="Suara Kasar" {{ $result->fan_evaporator == 'Suara Kasar' ? 'selected' : '' }}>Suara Kasar</option>
                                    <option value="OFF" {{ $result->fan_evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td class="auto-drain-column">
                                <select name="auto_drain[{{ $result->id }}]" class="form-select form-select-sm form-select-auto-drain">
                                    <option value="Berfungsi" {{ $result->auto_drain == 'Berfungsi' ? 'selected' : '' }}>Berfungsi</option>
                                    <option value="Tidak Berfungsi" {{ $result->auto_drain == 'Tidak Berfungsi' ? 'selected' : '' }}>Tidak Berfungsi</option>
                                    <option value="OFF" {{ $result->auto_drain == 'OFF' ? 'selected' : '' }}>OFF</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" name="keterangan[{{ $result->id }}]" value="{{ $result->keterangan }}" class="form-control form-control-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Detail Mesin -->
            <div class="mt-4 p-3 bg-light rounded w-50">
                <h3 class="fs-5 fw-semibold text-dark mb-2">Detail Mesin:</h3>
                <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('air-dryer.index') }}" class="btn btn-secondary">
                    Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>
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