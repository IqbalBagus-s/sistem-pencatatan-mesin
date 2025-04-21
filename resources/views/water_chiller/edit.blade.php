@extends('layouts.edit-layout-1')

@section('title', 'Edit Pencatatan Mesin Water Chiller')

@section('page-title', 'Edit Pencatatan Mesin Water Chiller')

@section('form-action', route('water-chiller.update', $check->id))

@section('hari-value', $check->hari)

@section('tanggal-value', $check->tanggal)

@section('keterangan-value', $check->keterangan)

@section('back-route', route('water-chiller.index'))