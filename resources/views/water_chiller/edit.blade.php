@extends('layouts.edit-layout-1')

@section('title', 'Edit Pencatatan Mesin Water Chiller')

@section('page-title', 'Edit Pencatatan Mesin Water Chiller')

@section('form-action', route('water-chiller.update', $check->id))

@section('hari-value', $check->hari)

@section('tanggal-value', $check->tanggal)

@section('keterangan-value', $check->keterangan)

@section('back-route', route('water-chiller.index'))

@section('table-content')
<div class="overflow-x-auto max-h-[500px]">
    <table class="w-full border-collapse border border-gray-300">
        <thead class="sticky top-0 z-10 bg-sky-50">
            <tr>
                <th class="border border-gray-300 bg-sky-50 p-2 w-12 sticky top-0">NO.</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-20 sticky top-0">No Mesin</th>
                <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Compressor</th>
                <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Kabel</th>
                <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Mcb</th>
                <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Air</th>
                <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Pompa</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-24 sticky top-0">Evaporator</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-28 sticky top-0">Fan Evaporator</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-24 sticky top-0">Freon</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-24 sticky top-0">Air</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $result)
                <tr class="bg-white">
                    <td class="border border-gray-300 text-center p-2">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 text-center p-2">
                        <input type="text" name="no_mesin[{{ $result->id }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center" 
                            value="{{ $result->no_mesin }}" readonly>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_1[{{ $result->id }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C" value="{{ $result->Temperatur_Compressor }}">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_2[{{ $result->id }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C" value="{{ $result->Temperatur_Kabel }}">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_3[{{ $result->id }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C" value="{{ $result->Temperatur_Mcb }}">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_4[{{ $result->id }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C" value="{{ $result->Temperatur_Air }}">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_5[{{ $result->id }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C" value="{{ $result->Temperatur_Pompa }}">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                            <option value="Bersih" {{ $result->Evaporator == 'Bersih' ? 'selected' : '' }}>Bersih</option>
                            <option value="Kotor" {{ $result->Evaporator == 'Kotor' ? 'selected' : '' }}>Kotor</option>
                            <option value="OFF" {{ $result->Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                        </select>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="fan_evaporator[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                            <option value="Suara Halus" {{ $result->Fan_Evaporator == 'Suara Halus' ? 'selected' : '' }}>Suara Halus</option>
                            <option value="Suara Keras" {{ $result->Fan_Evaporator == 'Suara Keras' ? 'selected' : '' }}>Suara Keras</option>
                            <option value="OFF" {{ $result->Fan_Evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                        </select>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="freon[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                            <option value="Cukup" {{ $result->Freon == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                            <option value="Tidak Cukup" {{ $result->Freon == 'Tidak Cukup' ? 'selected' : '' }}>Tidak Cukup</option>
                            <option value="OFF" {{ $result->Freon == 'OFF' ? 'selected' : '' }}>OFF</option>
                        </select>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="air[{{ $result->id }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
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
@endsection