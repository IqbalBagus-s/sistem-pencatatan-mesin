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