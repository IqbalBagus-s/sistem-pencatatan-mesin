@extends('layouts.create-layout-1')

@section('title', 'Form Pencatatan Mesin Air Dryer')

@section('page-title', 'Pencatatan Mesin Air Dryer')

@section('form-action', route('air-dryer.store'))

@section('back-route', route('air-dryer.index'))

@section('keterangan-container-class', 'flex flex-col md:flex-row gap-4 mt-5')

@section('keterangan-rows', '5')

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
        @for ($i = 1; $i <= 8; $i++)
            <tr>
                <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                <td class="border border-gray-300 text-center p-2">
                    <input type="text" name="nomor_mesin[{{ $i }}]" value="AD{{ $i }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" readonly>
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_kompresor[{{ $i }}]" value="{{ old("temperatur_kompresor.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_kabel[{{ $i }}]" value="{{ old("temperatur_kabel.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_mcb[{{ $i }}]" value="{{ old("temperatur_mcb.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_angin_in[{{ $i }}]" value="{{ old("temperatur_angin_in.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <input type="text" name="temperatur_angin_out[{{ $i }}]" value="{{ old("temperatur_angin_out.$i") }}" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded" placeholder="30°C - 60°C">
                </td>
                <td class="border border-gray-300 p-2">
                    <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                        <option value="Bersih">Bersih</option>
                        <option value="Kotor">Kotor</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td class="border border-gray-300 p-2">
                    <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                        <option value="Suara Halus">Suara Halus</option>
                        <option value="Suara Kasar">Suara Kasar</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
                <td class="border border-gray-300 p-2">
                    <select name="auto_drain[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded">
                        <option value="Berfungsi">Berfungsi</option>
                        <option value="Tidak Berfungsi">Tidak Berfungsi</option>
                        <option value="OFF">OFF</option>
                    </select>
                </td>
            </tr>
        @endfor
    </tbody>
</table>
@endsection

@section('detail-mesin')
<!-- Detail Mesin -->
<div class="bg-gray-100 p-4 rounded-md md:w-auto">
    <h5 class="mb-3 font-medium">Detail Mesin:</h5>
    <p class="mb-1">AD 1 : HIGH PRESS 1 &nbsp;&nbsp;&nbsp; AD 5 : SUPPLY INJECT</p>
    <p class="mb-1">AD 2 : HIGH PRESS 2 &nbsp;&nbsp;&nbsp; AD 6 : LOW PRESS 3</p>
    <p class="mb-1">AD 3 : LOW PRESS 1 &nbsp;&nbsp;&nbsp;&nbsp; AD 7 : LOW PRESS 4</p>
    <p class="mb-1">AD 4 : LOW PRESS 2 &nbsp;&nbsp;&nbsp;&nbsp; AD 8 : LOW PRESS 5</p>
</div>
@endsection

@section('additional-scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const warningMessage = "{{ session('warning') }}";
        
        if (warningMessage) {
            // Create a custom popup or use a library like SweetAlert
            alert(warningMessage);
        }
    });
</script>
@endsection