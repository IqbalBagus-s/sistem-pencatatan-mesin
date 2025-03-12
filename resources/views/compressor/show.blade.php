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
        <h2 class="mb-4 fw-bold">Approval Pencatatan Mesin Water Chiller</h2>

        <form action="{{ route('water-chiller.approve', $check->id) }}" method="POST">
            @csrf
            {{-- approver yang sedang login --}}
            <div class="mb-4 p-3 bg-light rounded">
                @if(!$check->approved_by)
                <p class="fs-5 fw-semibold">Approver: 
                    <span class="text-primary">{{ Auth::user()->username }}</span></p>
                @else
                <p class="fs-5 fw-semibold">Approver: 
                    <span class="text-primary">{{ $check->approved_by }}</span></p>
                @endif
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
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Nomor Mesin</th>
                            <th class="text-center">Temperatur Kompresor</th>
                            <th class="text-center">Temperatur Kabel</th>
                            <th class="text-center">Temperatur MCB</th>
                            <th class="text-center">Temperatur Air</th>
                            <th class="text-center">Temperatur Pompa</th>
                            <th class="text-center">Evaporator</th>
                            <th class="text-center">Fan Evaporator</th>
                            <th class="text-center">Freon</th>
                            <th class="text-center">Air</th>
                        </tr>
                    </thead>
                    <tbody> 
                        @foreach($results as $index => $result)
                            <tr class="bg-white">
                                <td class="border border-gray-300 p-2 text-center">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-24 p-1 border border-gray-300 rounded bg-gray-200 text-center" 
                                        value="{{ $result->no_mesin }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-20 p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Compressor }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-20 p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Kabel }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-20 p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Mcb }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-20 p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Air }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <input type="text" class="w-20 p-1 border border-gray-300 rounded text-center bg-gray-200" 
                                        value="{{ $result->Temperatur_Pompa }}" readonly>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-24 p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Bersih" {{ $result->Evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                                        <option value="Kotor" {{ $result->Evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                                        <option value="OFF" {{ $result->Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-24 p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Suara Halus" {{ $result->Fan_Evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                                        <option value="Suara Keras" {{ $result->Fan_Evaporator == 'Suara Keras' ? 'selected' : '' }}>Suara Keras</option>
                                        <option value="OFF" {{ $result->Fan_Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-24 p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Cukup" {{ $result->Freon == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="Tidak Cukup" {{ $result->Freon == 'Tidak Cukup' ? 'selected' : '' }}>Tidak Cukup</option>
                                        <option value="OFF" {{ $result->Freon == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2 text-center">
                                    <select class="w-24 p-1 border border-gray-300 rounded text-center bg-gray-200" disabled>
                                        <option value="Cukup" {{ $result->Air == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="Tidak Cukup" {{ $result->Air == 'Tidak Cukup' ? 'selected' : '' }}>Tidak Cukup</option>
                                        <option value="OFF" {{ $result->Air == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
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
</body>
</html>
