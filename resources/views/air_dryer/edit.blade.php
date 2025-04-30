@extends('layouts.edit-layout-1')

@section('title', 'Edit Pencatatan Mesin Air Dryer')

@section('page-title', 'Edit Pencatatan Mesin Air Dryer')

@section('form-action', route('air-dryer.update', $check->id))

@section('hari-value', $check->hari)

@section('tanggal-value', $check->tanggal)

@section('keterangan-value', $check->keterangan)

@section('back-route', route('air-dryer.index'))

@section('table-content')
<table class="w-full border-collapse border border-gray-300">
    <thead>
        <tr>
            <th class="border border-gray-300 bg-sky-50 p-2">No</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Nomor Mesin</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kompresor</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kabel</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur MCB</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Angin In</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Angin Out</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Evaporator</th>
            <th class="border border-gray-300 bg-sky-50 p-2">Fan Evaporator</th>
            <th class="border border-gray-300 bg-sky-50 p-2 min-w-[140px]">Auto Drain</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $index => $result)
        <tr>
            <td class="border border-gray-300 text-center p-2">{{ $index + 1 }}</td>
            <td class="border border-gray-300 text-center p-2">
                <input type="text" name="nomor_mesin[{{ $result->id }}]" value="{{ $result->nomor_mesin }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" readonly>
            </td>
            <td class="border border-gray-300 p-2">
                <input type="text" name="temperatur_kompresor[{{ $result->id }}]" value="{{ $result->temperatur_kompresor }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
            </td>
            <td class="border border-gray-300 p-2">
                <input type="text" name="temperatur_kabel[{{ $result->id }}]" value="{{ $result->temperatur_kabel }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
            </td>
            <td class="border border-gray-300 p-2">
                <input type="text" name="temperatur_mcb[{{ $result->id }}]" value="{{ $result->temperatur_mcb }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
            </td>
            <td class="border border-gray-300 p-2">
                <input type="text" name="temperatur_angin_in[{{ $result->id }}]" value="{{ $result->temperatur_angin_in }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
            </td>
            <td class="border border-gray-300 p-2">
                <input type="text" name="temperatur_angin_out[{{ $result->id }}]" value="{{ $result->temperatur_angin_out }}" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
            </td>
            <td class="border border-gray-300 p-2">
                <select name="evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                    <option value="Bersih" {{ $result->evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                    <option value="Kotor" {{ $result->evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                    <option value="OFF" {{ $result->evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                </select>
            </td>
            <td class="border border-gray-300 p-2">
                <select name="fan_evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                    <option value="Suara Halus" {{ $result->fan_evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                    <option value="Suara Kasar" {{ $result->fan_evaporator == 'Suara Kasar' ? 'selected' : '' }}>Suara Kasar</option>
                    <option value="OFF" {{ $result->fan_evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                </select>
            </td>
            <td class="border border-gray-300 p-2">
                <select name="auto_drain[{{ $result->id }}]" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                    <option value="Berfungsi" {{ $result->auto_drain == 'Berfungsi' ? 'selected' : '' }}>Berfungsi</option>
                    <option value="Tidak Berfungsi" {{ $result->auto_drain == 'Tidak Berfungsi' ? 'selected' : '' }}>Tidak Berfungsi</option>
                    <option value="OFF" {{ $result->auto_drain == 'OFF' ? 'selected' : '' }}>OFF</option>
                </select>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Detail Mesin -->
<div class="bg-gray-100 p-4 rounded-md mt-4">
    <h5 class="mb-3 font-medium">Detail Mesin:</h5>
    <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
    <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
    <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
    <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
</div>
@endsection