@extends('layouts.index-host-layout')

@section('title', 'Form Management')

@section('page-title', 'Form Management')

@section('form-action')
    {{ route('host.forms.index') }}
@endsection

@section('custom-filters')
<div x-data="{ 
    nomorForm: '{{ request('nomor_form') }}',
    namaForm: '{{ request('nama_form') }}',
    showDropdownNomor: false,
    showDropdownNama: false,
    submitForm() {
        this.$refs.filterForm.submit();
    }
    }">
    <form x-ref="filterForm" action="{{ route('host.forms.index') }}" method="GET">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <!-- Nomor Form dropdown -->
            <div class="relative">
                <label for="nomor_form" class="block font-medium text-gray-700 mb-1">Nomor Form:</label>
                <div class="relative">
                    <input type="text" name="nomor_form" id="nomor_form" placeholder="Pilih nomor form..."
                        x-model="nomorForm" @click="showDropdownNomor = !showDropdownNomor" @keyup.enter="submitForm()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary cursor-pointer">
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                <!-- Dropdown nomor form -->
                <div x-show="showDropdownNomor" @click.away="showDropdownNomor = false"
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 focus:outline-none max-h-60 overflow-auto">
                    <div class="px-2 py-2 border-b border-gray-200">
                        <button type="button" @click="nomorForm = ''; showDropdownNomor = false"
                            class="w-full text-left text-sm text-gray-700 hover:bg-gray-100 px-2 py-1 rounded">
                            Semua Nomor Form
                        </button>
                    </div>
                    @foreach($uniqueNomorForms as $nomor)
                    <div class="px-2 py-1">
                        <button type="button" @click="nomorForm = '{{ $nomor }}'; showDropdownNomor = false; submitForm()"
                            class="w-full text-left text-sm text-gray-700 hover:bg-gray-100 px-2 py-1 rounded">
                            {{ $nomor }}
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Nama Form dropdown -->
            <div class="relative">
                <label for="nama_form" class="block font-medium text-gray-700 mb-1">Nama Form:</label>
                <div class="relative">
                    <input type="text" name="nama_form" id="nama_form" placeholder="Pilih nama form..."
                        x-model="namaForm" @click="showDropdownNama = !showDropdownNama" @keyup.enter="submitForm()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary cursor-pointer">
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                <!-- Dropdown nama form -->
                <div x-show="showDropdownNama" @click.away="showDropdownNama = false"
                    class="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 focus:outline-none max-h-60 overflow-auto">
                    <div class="px-2 py-2 border-b border-gray-200">
                        <button type="button" @click="namaForm = ''; showDropdownNama = false"
                            class="w-full text-left text-sm text-gray-700 hover:bg-gray-100 px-2 py-1 rounded">
                            Semua Nama Form
                        </button>
                    </div>
                    @foreach($uniqueNamaForms as $nama)
                    <div class="px-2 py-1">
                        <button type="button" @click="namaForm = '{{ $nama }}'; showDropdownNama = false; submitForm()"
                            class="w-full text-left text-sm text-gray-700 hover:bg-gray-100 px-2 py-1 rounded">
                            {{ $nama }}
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Buttons group -->
            <div class="flex space-x-2">
                <!-- Filter button -->
                <button type="submit"
                    class="flex-1 h-10 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200 ease-in-out flex items-center justify-center cursor-pointer">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                
                <!-- Create button -->
                <a href="{{ route('host.forms.create') }}" 
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
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Nomor Form</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Nama Form</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Tanggal Efektif</th>
                <th class="py-3 px-4 border-b border-gray-200 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($forms as $index => $form)
                <tr class="text-center hover:bg-gray-50">
                    <td class="py-3 px-4 border-b border-gray-200">{{ $forms->firstItem() + $loop->index }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">{{ $form->nomor_form }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">{{ $form->nama_form }}</td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        {{ \Carbon\Carbon::parse($form->tanggal_efektif)->format('d/m/Y') }}
                    </td>
                    <td class="py-3 px-4 border-b border-gray-200">
                        <div class="flex justify-center space-x-3">
                            <a href="{{ route('host.forms.edit', $form->id) }}" title="Edit" class="text-amber-500 hover:text-amber-700">
                                <i class="fas fa-pen"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4">Tidak ada data form ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@section('pagination-data')
    @if(method_exists($forms, 'links') && $forms->hasPages())
        {{-- Menggunakan komponen pagination yang sudah dibuat --}}
        @include('components.pagination', ['paginator' => $forms])
    @endif
@endsection

@section('back-route')
    {{ route('dashboard') }}
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection