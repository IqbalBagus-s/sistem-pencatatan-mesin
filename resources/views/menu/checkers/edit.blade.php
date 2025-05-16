@extends('layouts.create-edit-host-layout')

@section('title', 'Edit Checker')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 max-w-md mx-auto">
    <div class="flex items-center mb-6">
        <img src="{{ asset('images/logo-aspra.png') }}" alt="PT Asia Pramulia" class="h-10 mr-3">
        <h1 class="text-xl font-bold text-gray-800">Edit Checker</h1>
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

    <form action="{{ route('host.checkers.update', $checker->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        
        <div class="mb-5">
            <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
            <input type="text" id="username" name="username" 
                class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('username') @enderror" 
                value="{{ old('username', $checker->username) }}" 
                placeholder="Masukkan username" required>
            @error('username')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-5" x-data="{ showPassword: false }">
            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
            <div class="relative">
                <input 
                    :type="showPassword ? 'text' : 'password'" 
                    id="password" 
                    name="password" 
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') @enderror pr-10" 
                    placeholder="Kosongkan jika tidak ingin mengubah password">
                <button 
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 flex items-center px-3 focus:outline-none text-gray-600 hover:text-gray-800"
                >
                    <svg 
                        x-show="!showPassword"
                        class="h-5 w-5" 
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg 
                        x-show="showPassword"
                        class="h-5 w-5" 
                        xmlns="http://www.w3.org/2000/svg" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <p class="text-gray-500 text-sm mt-1">Kosongkan jika tidak ingin mengubah password</p>
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-5">
            <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
            <div class="relative">
                <select id="status" name="status" 
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 appearance-none @error('status') @enderror" 
                    required>
                    <option value="" disabled>Pilih status</option>
                    <option value="aktif" {{ old('status', $checker->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="tidak_aktif" {{ old('status', $checker->status) == 'tidak_aktif' ? 'selected' : '' }}>Non-Aktif</option>
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
            <a href="{{ route('host.checkers.index') }}" 
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2 rounded-md font-medium transition-colors duration-200">
                Batal
            </a>
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-medium transition-colors duration-200">
                Perbarui Checker
            </button>
        </div>
    </form>
</div>
@endsection