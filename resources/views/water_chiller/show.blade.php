@extends('layouts.show-layout-1')

@section('machine-type', 'Water Chiller')

@section('title-center', 'text-center')

@section('approval-route')
{{ route('water-chiller.approve', $check->id) }}
@endsection

@section('back-route')
{{ route('water-chiller.index') }}
@endsection

@section('pdf-route')
{{ route('water-chiller.downloadPdf', $check->id) }}
@endsection

@section('keterangan-label', 'text-gray-700 font-semibold')

@section('keterangan-rows', '3')

@section('machine-detail')
<!-- No machine detail section for Water Chiller -->
@endsection

@section('table-content')
<div class="overflow-x-auto max-h-[500px]">
    <table class="w-full border-collapse border border-gray-300 text-sm">
        <thead class="bg-gray-100 sticky top-0 z-10">
            <tr>
                <th class="border border-gray-300 p-1 w-10 sticky top-0">No</th>
                <th class="border border-gray-300 p-1 w-24 sticky top-0">Nomor Mesin</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Temperatur Kompresor</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Temperatur Kabel</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Temperatur MCB</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Temperatur Air</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Temperatur Pompa</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Evaporator</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Fan Evaporator</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Freon</th>
                <th class="border border-gray-300 p-1 w-20 sticky top-0">Air</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $index => $result)
                <tr class="bg-white">
                    <td class="border border-gray-300 p-1 text-center w-10">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 p-1 text-center w-24">{{ $result->no_mesin }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Compressor }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Kabel }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Mcb }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Air }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Temperatur_Pompa }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Evaporator }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Fan_Evaporator }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Freon }}</td>
                    <td class="border border-gray-300 p-1 text-center w-20">{{ $result->Air }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection