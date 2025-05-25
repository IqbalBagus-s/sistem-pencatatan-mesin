@extends('layouts.index-host-layout')

@section('title', 'Menu Checker')

@section('page-title', 'Menu Checker')

@section('form-action')
    {{ route('host.checkers.index') }}
@endsection

@section('custom-filters')
<div x-data="{ 
    search: '{{ request('search') }}',
    status: '{{ request('status') }}',
    submitForm() {
        this.$refs.filterForm.submit();
    }
    }">
    <form x-ref="filterForm" action="{{ route('host.checkers.index') }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <!-- Search field -->
            <div>
                <label for="search" class="block font-medium text-gray-700 mb-1">Cari Username:</label>
                <input type="text" name="search" id="search" placeholder="Masukkan username..."
                    x-model="search" @keyup.enter="submitForm()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
            </div>

            <!-- Status field -->
            <div>
                <label for="status" class="block font-medium text-gray-700 mb-1">Status:</label>
                <select id="status" name="status" x-model="status" @change="submitForm()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="tidak aktif">Tidak Aktif</option>
                </select>
            </div>

            <!-- Buttons group -->
            <div class="flex space-x-2">
                <!-- Filter button -->
                <button type="submit"
                    class="flex-1 h-10 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 ease-in-out flex items-center justify-center cursor-pointer">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                
                <!-- Create button -->
                <a href="{{ route('host.checkers.create') }}" 
                    class="flex-1 h-10 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition duration-200 ease-in-out flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>Tambah
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('table-content')
    <table class="table-auto w-full">
        <thead class="bg-gray-100">
            <tr class="text-center">
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">No</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Username</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Status</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Tanggal Dibuat</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($checkers as $index => $checker)
                <tr class="text-center hover:bg-gray-50">
                    <td class="py-3 px-4 border-b border-gray-200">{{ $loop->iteration }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">{{ $checker->username }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        @if($checker->status == 'aktif')
                            <span class="bg-approved text-approvedText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                Aktif
                            </span>
                        @else
                            <span class="bg-pending text-pendingText px-4 py-1 rounded-full text-sm font-medium inline-block">
                                Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="py-3 px-4 border-b border-gray-200">{{ $checker->created_at->format('j F Y') }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        <div class="flex justify-center space-x-3">
                            <a href="{{ route('host.checkers.edit', $checker->id) }}" title="Edit" class="text-amber-500 hover:text-amber-700">
                                <i class="fas fa-pen"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Tidak ada data ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@section('pagination-data')
    @if(method_exists($checkers, 'links') && $checkers->hasPages())
        {{-- Menggunakan komponen pagination yang sudah dibuat --}}
        @include('components.pagination', ['paginator' => $checkers])
    @endif
@endsection

@section('back-route')
    {{ route('host.dashboard') }}
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection