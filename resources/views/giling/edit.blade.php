@extends('layouts.edit-layout-1')

@section('title', 'Edit Pemeriksaan Mesin Giling')

@section('page-title', 'Edit Pemeriksaan Mesin Giling')

@section('form-action', route('giling.update', $check->id))

@section('back-route', route('giling.index'))

@section('date-time-fields')
<!-- Minggu dan Bulan fields (display only) -->
<div class="grid md:grid-cols-2 gap-4 mb-4">
    <div>
        <label for="minggu" class="block mb-2">Pilih Minggu ke- :</label>
        <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
            {{ $check->minggu }}
        </div>
        <!-- Use readonly instead of hidden, but still included in form submission -->
        <input type="text" id="minggu" name="minggu" value="{{ $check->minggu }}" readonly 
               class="hidden">
    </div>
    <div>
        <label for="bulan" class="block mb-2">Bulan:</label>
        <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
            @php
                $date = new DateTime($check->bulan);
                $formattedDate = $date->format('F Y');
            @endphp
            {{ $formattedDate }}
        </div>
        <!-- Use readonly instead of hidden, but still included in form submission -->
        <input type="text" id="bulan" name="bulan" value="{{ $check->bulan }}" readonly
               class="hidden">
    </div>
</div>
@endsection

@section('keterangan-value')
{{ $check->keterangan }}
@endsection

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
                <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-5 align-middle">No.</th>
                <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-40 align-middle">Checked Items</th>
                <th colspan="10" class="border border-gray-300 bg-sky-50 p-2 text-center">HASIL PEMERIKSAAN GILINGAN</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 10; $i++)
                    <th class="border border-gray-300 bg-sky-50 p-2 text-center w-24">G{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php
                $items = [
                    1 => 'Cek Motor Mesin Giling',
                    2 => 'Cek Vanbelt',
                    3 => 'Cek Dustcollector',
                    4 => 'Cek Safety Switch',
                    5 => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
                ];
                
                // Standard options for all items
                $options = ['Baik', 'Jelek', 'OFF', '-'];
                
                // Map from checked items to snake_case field names
                $fieldMappings = [
                    'Cek Motor Mesin Giling' => 'cek_motor_mesin_giling',
                    'Cek Vanbelt' => 'cek_vanbelt',
                    'Cek Dustcollector' => 'cek_dustcollector',
                    'Cek Safety Switch' => 'cek_safety_switch',
                    'Cek Ketajaman Pisau Putar dan Pisau Duduk' => 'cek_ketajaman_pisau_putar_dan_pisau_duduk'
                ];
            @endphp
            
            @foreach($items as $i => $item)
                @php
                    $fieldName = $fieldMappings[$item];
                    $result = isset($results[$item]) ? $results[$item] : null;
                @endphp
                <tr>
                    <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                    <td class="border border-gray-300 p-2">{{ $item }}</td>
                    
                    @for ($g = 1; $g <= 10; $g++)
                        <td class="border border-gray-300 p-2">
                            <select name="{{ $fieldName }}[G{{ $g }}]" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                @foreach($options as $option)
                                    <option value="{{ $option }}" {{ $result && $result->{"g$g"} == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('detail-mesin')
<!-- Detail Mesin -->
<div class="bg-sky-50 p-4 rounded-md md:w-auto">
    <h5 class="mb-3 font-medium">Catatan Pemeriksaan:</h5>
    <p class="mb-1">- Pengecekan Ketajaman Pisau Putar dan Pisau Duduk DIlakukan Pada minggu ke-4 di setiap bulannya</p>
</div>
@endsection