@extends('layouts.create-host-layout')

@section('title', 'Tambah Approver - PT Asia Pramulia')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-md mx-auto">
    <div class="flex items-center mb-6">
        <img src="{{ asset('images/logo-aspra.png') }}" alt="PT Asia Pramulia" class="h-10 mr-3">
        <h1 class="text-xl font-bold text-gray-800">Tambah Approver Baru</h1>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('host.approvers.store') }}" method="POST" autocomplete="off">
        @csrf
        
        <div class="mb-5">
            <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
            <input type="text" id="username" name="username" 
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('username') border-red-500 @enderror" 
                value="{{ old('username') }}" 
                placeholder="Masukkan username" required>
            @error('username')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-5">
            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
            <input type="password" id="password" name="password" 
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                placeholder="Masukkan password" required>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-5">
            <label for="role" class="block text-gray-700 font-medium mb-2">Peran</label>
            <div class="relative">
                <select id="role" name="role" 
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 appearance-none @error('role') border-red-500 @enderror" 
                    required>
                    <option value="" disabled selected>Pilih peran</option>
                    <option value="Penanggung Jawab" {{ old('role') == 'Penanggung Jawab' ? 'selected' : '' }}>Penanggung Jawab</option>
                    <option value="Kepala Regu" {{ old('role') == 'Kepala Regu' ? 'selected' : '' }}>Kepala Regu</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-5">
            <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
            <div class="relative">
                <select id="status" name="status" 
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 appearance-none @error('status') border-red-500 @enderror" 
                    required>
                    <option value="" disabled selected>Pilih status</option>
                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                    </svg>
                </div>
            </div>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex justify-between mt-8">
            <a href="{{ route('host.approvers.index') }}" 
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-md font-medium transition-colors duration-200">
                Batal
            </a>
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-medium transition-colors duration-200">
                Simpan Approver
            </button>
        </div>
    </form>
</div>
@endsection