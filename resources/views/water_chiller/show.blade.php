<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Pencatatan Mesin Water Chiller</title>

    <link rel="icon" href="{{ asset('images/logo-aspra.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3">
    <div class="container bg-white p-4 rounded shadow">
        <h2 class="mb-4 fw-bold">Approval Pencatatan Mesin Air Dryer</h2>

        <form action="{{ route('water-chiller.approve', $check->id) }}" method="POST">
            @csrf

            <div class="mb-4 p-3 bg-light rounded">
                <p class="fs-5 fw-semibold">Approver: <span class="text-primary">{{ Auth::user()->username }}</span></p>
            </div>

            <div class="mb-4 p-3 bg-light rounded">
                <p class="fs-5 fw-semibold">Checker: <span class="text-success">{{ $check->checked_by }}</span></p>
            </div>

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
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center" style="min-width: 220px;">ITEM YANG DIPERIKSA</th>
                            <th class="text-center" style="min-width: 145px;">STANDART</th>
                            @for ($i = 1; $i <= 32; $i++)
                                <th class="text-center text-nowrap" style="min-width: 10px; font-size: 12px;">CH{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                            <tr>
                                <td class="text-center text-nowrap" style="padding: 0.2rem;">{{ $index + 1 }}</td>
                                <td>
                                    <input type="text" class="text-center form-control bg-light" value="{{ $result->checked_items }}" readonly>
                                </td>
                                <td>
                                    <input type="text" class="text-center form-control bg-light" value="{{ $result->standart }}" readonly>
                                </td>
                                @for ($j = 1; $j <= 32; $j++)
                                    @php 
                                        $key = "CH{$j}"; 
                                        $value = $result->$key ?? '-';
                                    @endphp
                                    <td class="border border-gray-300 text-center align-middle text-nowrap" style="padding: 0.2rem;">
                                        {{ $value }}
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>                    
                                    
                </table>
            </div>
            
            <div class="mt-4">
                <label for="keterangan" class="form-label">Keterangan:</label>
                <textarea id="keterangan" name="keterangan" rows="3" class="form-control" placeholder="Tambahkan keterangan jika diperlukan..." readonly>{{ $check->keterangan }}</textarea>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <a href="{{ route('water-chiller.index') }}" class="btn btn-secondary">Kembali</a>
                @if(!$check->approved_by)
                    <button type="submit" class="btn btn-success">Setujui</button>
                @else
                    <a href="{{ route('water-chiller.downloadPdf', $check->id) }}" class="btn btn-primary">Download PDF</a>
                    <button type="submit" class="btn btn-secondary" disabled>Telah Disetujui</button>
                @endif
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const standardValues = [
                "30 °C - 60 °C",
                "30 °C - 45 °C",
                "30 °C - 50 °C",
                "Sesuai Setelan",
                "40 °C - 50 °C",
                "Bersih",
                "Suara Halus",
                "Cukup",
                "Cukup"
            ];
    
            const standardInputs = document.querySelectorAll('td:nth-child(3) input');
            
            standardInputs.forEach((input, index) => {
                if (standardValues[index]) {
                    input.value = standardValues[index];
                }
            });
        });
    </script>
</body>
</html>
