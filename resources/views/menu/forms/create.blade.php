@extends('layouts.create-edit-host-layout')

@section('title', 'Tambah Form')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-md mx-auto">
    <div class="flex items-center mb-6">
        <img src="{{ asset('images/logo-aspra.png') }}" alt="PT Asia Pramulia" class="h-10 mr-3">
        <h1 class="text-xl font-bold text-gray-800">Tambah Form Baru</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('host.forms.store') }}" method="POST" autocomplete="off">
        @csrf
        
        <div class="mb-5">
            <label for="nomor_form" class="block text-gray-700 font-medium mb-2">Nomor Form</label>
            <input type="text" id="nomor_form" name="nomor_form" 
                class="w-full px-3 py-2 bg-white border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('nomor_form') border-red-500 @enderror"
                value="{{ old('nomor_form') }}" 
                placeholder="Masukkan nomor form" required>
            @error('nomor_form')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-5">
            <label for="nama_form" class="block text-gray-700 font-medium mb-2">Nama Form</label>
            <input type="text" id="nama_form" name="nama_form" 
                class="w-full px-3 py-2 bg-white border rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('nama_form') border-red-500 @enderror"
                value="{{ old('nama_form') }}" 
                placeholder="Masukkan nama form" required>
            @error('nama_form')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-5">
            <label for="tanggal_efektif" class="block text-gray-700 font-medium mb-2">Tanggal Efektif</label>
            <input type="date" id="tanggal_efektif" name="tanggal_efektif" 
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('tanggal_efektif') @enderror" 
                value="{{ old('tanggal_efektif') }}"
                required>
            @error('tanggal_efektif')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex justify-between mt-8">
            <a href="{{ route('host.forms.index') }}" 
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-md font-medium transition-colors duration-200">
                Kembali
            </a>
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-medium transition-colors duration-200">
                Simpan Form
            </button>
        </div>
    </form>
</div>
@endsection