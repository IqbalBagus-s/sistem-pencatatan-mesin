@extends('layouts.create-layout-1')

@section('title', 'Form Pencatatan Mesin Water Chiller')

@section('page-title', 'Pencatatan Mesin Water Chiller')

@section('form-action', route('water-chiller.store'))

@section('back-route', route('water-chiller.index'))

@section('additional-scripts')
<style>
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endsection

@section('table-content')
<div class="overflow-x-auto max-h-[600px]">
    <table class="w-full border-collapse border border-gray-300">
        <thead class="sticky-header">
            <tr>
                <th class="border border-gray-300 bg-sky-50 p-2 w-12">NO.</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-20">No Mesin</th>
                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Compressor</th>
                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Kabel</th>
                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Mcb</th>
                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Air</th>
                <th class="border border-gray-300 bg-sky-50 p-2">Temperatur Pompa</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-24">Evaporator</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-28">Fan Evaporator</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-24">Freon</th>
                <th class="border border-gray-300 bg-sky-50 p-2 w-24">Air</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 32; $i++)
                <tr>
                    <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                    <td class="border border-gray-300 text-center p-2">
                        <input type="text" name="no_mesin[{{ $i }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center" 
                            value="CH{{ $i }}" readonly>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_1[{{ $i }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_2[{{ $i }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_3[{{ $i }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_4[{{ $i }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <input type="text" name="temperatur_5[{{ $i }}]" 
                            class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                            placeholder="30°C - 60°C">
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                            <option value="Bersih">Bersih</option>
                            <option value="Kotor">Kotor</option>
                            <option value="OFF">OFF</option>
                        </select>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                            <option value="Suara Halus">Suara Halus</option>
                            <option value="Suara Keras">Suara Keras</option>
                            <option value="OFF">OFF</option>
                        </select>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="freon[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                            <option value="Cukup">Cukup</option>
                            <option value="Tidak Cukup">Tidak Cukup</option>
                            <option value="OFF">OFF</option>
                        </select>
                    </td>
                    <td class="border border-gray-300 p-2">
                        <select name="air[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                            <option value="Cukup">Cukup</option>
                            <option value="Tidak Cukup">Tidak Cukup</option>
                            <option value="OFF">OFF</option>
                        </select>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
@endsection