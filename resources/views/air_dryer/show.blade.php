@extends('layouts.show-layout-1')

@section('machine-type', 'Air Dryer')

@section('title-center', '')

@section('approval-route')
{{ route('air-dryer.approve', $check->id) }}
@endsection

@section('back-route')
{{ route('air-dryer.index') }}
@endsection

@section('pdf-route')
{{ route('air-dryer.downloadPdf', $check->id) }}
@endsection

@section('keterangan-container', 'flex-1')

@section('keterangan-rows', '5')

@section('table-content')
<table class="w-full border-collapse border border-gray-300">
    <thead class="bg-gray-100">
        <tr>
            <th class="border border-gray-300 p-2">No</th>
            <th class="border border-gray-300 p-2">Nomor Mesin</th>
            <th class="border border-gray-300 p-2">Temperatur Kompresor</th>
            <th class="border border-gray-300 p-2">Temperatur Kabel</th>
            <th class="border border-gray-300 p-2">Temperatur MCB</th>
            <th class="border border-gray-300 p-2">Temperatur Angin In</th>
            <th class="border border-gray-300 p-2">Temperatur Angin Out</th>
            <th class="border border-gray-300 p-2">Evaporator</th>
            <th class="border border-gray-300 p-2">Fan Evaporator</th>
            <th class="border border-gray-300 p-2">Auto Drain</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $index => $result)
        <tr>
            <td class="text-center border border-gray-300 p-2">{{ $index + 1 }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->nomor_mesin }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_kompresor }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_kabel }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_mcb }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_angin_in }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->temperatur_angin_out }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->evaporator }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->fan_evaporator }}</td>
            <td class="text-center border border-gray-300 p-2">{{ $result->auto_drain }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

@section('machine-detail')
<div class="flex flex-col  gap-4 mt-5">
    <!-- Detail Mesin -->
    <div class="bg-sky-50 p-4 rounded-md ">
        <h5 class="mb-3 font-medium">Detail Mesin:</h5>
        <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
        <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
        <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
        <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
    </div>
</div>
@endsection

