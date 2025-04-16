@extends('layouts.create-layout-1')

@section('title', 'Form Pencatatan Mesin Air Dryer')

@section('page-title', 'Pencatatan Mesin Air Dryer')

@section('form-action', route('air-dryer.store'))

@section('back-route', route('air-dryer.index'))

@section('keterangan-container-class', 'flex flex-col md:flex-row gap-4 mt-5')

@section('keterangan-rows', '5')

@section('table-content')
@endsection

@section('detail-mesin')
@endsection