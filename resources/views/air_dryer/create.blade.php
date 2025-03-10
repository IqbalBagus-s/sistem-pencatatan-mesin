<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pencatatan Mesin Air Dryer</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f5ff;
            font-family: Arial, sans-serif;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .checker-section {
            background-color: #f0f5ff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .checker-label {
            color: #6c757d;
            font-weight: bold;
        }
        .checker-name {
            font-weight: bold;
            color: #2963B8;
        }
        .footer {
            background-color: #ffffff;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
            margin-top: auto;
            width: 100%;
        }
        .table th {
            background-color: #f0f5ff;
        }
        .auto-drain-column {
            min-width: 140px;
        }
        .btn-primary {
            background-color: #2963B8;
            border-color: #2963B8;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        .detail-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .form-control, .form-select {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff;
            border-color: #2963B8;
            box-shadow: 0 0 0 0.25rem rgba(41, 99, 184, 0.25);
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Pencatatan Mesin Air Dryer</h2>

        <div class="card">
            <div class="card-body">
                <!-- Menampilkan Nama Checker -->
                <div class="checker-section">
                    <span class="checker-label">Checker: </span>
                    <span class="checker-name">{{ Auth::user()->username }}</span>
                </div>

                <!-- Form Input -->
                <form action="{{ route('air-dryer.store') }}" method="POST">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Hari:</label>
                            <input type="text" id="hari" name="hari" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal:</label>
                            <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                        </div>
                    </div>

                    <!-- Tabel Inspeksi -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
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
                                    <th class="auto-drain-column">Auto Drain</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="table-body"></tbody>
                        </table>
                    </div>

                    <!-- Detail Mesin -->
                    <div class="detail-section col-md-6">
                        <h5 class="mb-3">Detail Mesin:</h5>
                        <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
                        <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
                        <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
                        <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('air-dryer.index') }}" class="btn btn-secondary">
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p class="mb-0 fw-bold">2025 © PT ASIA PRAMULIA</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data mesin air dryer
        const jumlahMesin = 8;
        const tableBody = document.getElementById("table-body");

        for (let i = 1; i <= jumlahMesin; i++) {
            let nomorMesin = `AD${i}`;

            let row = `<tr>
                <td class="text-center">${i}</td>
                <td class="text-center">
                    <input type="text" name="nomor_mesin[${i}]" value="${nomorMesin}" class="form-control form-control-sm bg-light" readonly>
                </td>
                <td>
                    <input type="text" name="temperatur_kompresor[${i}]" 
                        class="form-control form-control-sm"
                        placeholder="30°C - 60°C" required>
                </td>
                <td>
                    <input type="text" name="temperatur_kabel[${i}]" 
                        class="form-control form-control-sm"
                        placeholder="30°C - 60°C" required>
                </td>
                <td>
                    <input type="text" name="temperatur_mcb[${i}]" 
                        class="form-control form-control-sm"
                        placeholder="30°C - 60°C" required>
                </td>
                <td>
                    <input type="text" name="temperatur_angin_in[${i}]" 
                        class="form-control form-control-sm"
                        placeholder="30°C - 60°C" required>
                </td>
                <td>
                    <input type="text" name="temperatur_angin_out[${i}]" 
                        class="form-control form-control-sm"
                        placeholder="30°C - 60°C" required> 
                </td>
                <td>
                    <select name="evaporator[${i}]" class="form-select form-select-sm">
                        <option value="Bersih">Bersih</option>
                        <option value="Kotor">Kotor</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td>
                    <select name="fan_evaporator[${i}]" class="form-select form-select-sm">
                        <option value="Suara Halus">Suara Halus</option>
                        <option value="Suara Kasar">Suara Kasar</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td class="auto-drain-column">
                    <select name="auto_drain[${i}]" class="form-select form-select-sm">
                        <option value="Berfungsi">Berfungsi</option>
                        <option value="Tidak Berfungsi">Tidak Berfungsi</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="keterangan[${i}]" class="form-control form-control-sm">
                </td>
            </tr>`;

            tableBody.innerHTML += row;
        }

        document.getElementById("tanggal").addEventListener("change", function() {
            let tanggal = new Date(this.value);
            let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
            document.getElementById("hari").value = hari;
        });
    </script>
</body>
</html>