@extends('layouts.edit-layout-1')

@section('title', 'Edit Pencatatan Mesin Air Dryer')

@section('page-title', 'Edit Pencatatan Mesin Air Dryer')

@section('form-action', route('air-dryer.update', $check->id))

@section('hari-value', $check->hari)

@section('tanggal-value', $check->tanggal)

@section('keterangan-value', $check->keterangan)

@section('back-route', route('air-dryer.index'))

@section('air-dryer-table')
@endsection

@section('detail-mesin', true)