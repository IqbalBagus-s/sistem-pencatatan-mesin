<!-- resources/views/giling/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Giling')

@section('page-title', 'Pencatatan Mesin Giling')

@section('form-action', route('giling.store'))

@section('back-route', route('giling.index'))

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Giling</h2>

<div class="bg-white rounded-lg shadow-md mb-5" x-data="gilingForm()">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ $user->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('giling.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Input Minggu -->
                <div>
                    <label class="block mb-2">Minggu:</label>
                    <div class="relative">
                        <select 
                            x-model="selectedWeek" 
                            name="minggu" 
                            class="w-full px-3 py-2 bg-white border border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md pr-10 appearance-none"
                        >
                            <option value="">Pilih Minggu</option>
                            <option value="1">Minggu ke-1</option>
                            <option value="2">Minggu ke-2</option>
                            <option value="3">Minggu ke-3</option>
                            <option value="4">Minggu ke-4</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                            <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Input Bulan -->
                <div>
                    <label for="bulan" class="block mb-2">Bulan:</label>
                    <div class="relative">
                        <input type="month" id="bulan" name="bulan" class="w-full px-3 py-2 bg-white border border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md" required>
                        <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                            <!-- Icon calendar sudah ada di browser secara default untuk input type="month" -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabel Mesin Giling -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <div class="md:hidden text-sm text-gray-500 italic mb-2">
                        ← Geser ke kanan untuk melihat semua kolom →
                    </div>
                    <table class="w-full border-collapse min-w-[1000px]">
                        <thead class="sticky top-0 z-10">
                            <tr>
                                <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-5 align-middle sticky top-0">No.</th>
                                <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-40 align-middle sticky top-0">Item Pemeriksaan</th>
                                <th colspan="10" class="border border-gray-300 bg-sky-50 p-2 text-center sticky top-0">HASIL PENCATATAN GILINGAN</th>
                            </tr>
                            <tr>
                                @for ($i = 1; $i <= 10; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 text-center w-24 sticky top-8">G{{ $i }}</th>
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
                                
                                // Standard options 
                                $options = ['V', 'X', '-', 'OFF'];
                            @endphp
                            
                            @foreach($items as $i => $item)
                                <tr>
                                    <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                    <td class="border border-gray-300 p-2">{{ $item }}</td>
                                    
                                    @for ($g = 1; $g <= 10; $g++)
                                        <td class="border border-gray-300 p-2">
                                            <select 
                                                name="{{ Str::snake($item) }}[G{{ $g }}]" 
                                                class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            >
                                                @foreach($options as $option)
                                                    @if ($i == 5 && in_array($option, ['V', 'X', 'OFF']))
                                                        <option value="{{ $option }}">
                                                            {{ $option }}
                                                        </option>
                                                    @elseif ($i != 5)
                                                        <option value="{{ $option }}" 
                                                            {{ $option == 'V' ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                                
                                                @if ($i == 5)
                                                    <option value="-" selected>-</option>
                                                @endif
                                            </select>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Catatan Pemeriksaan --}}
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-6 rounded-xl shadow-md mb-8 border-l-4 border-blue-500">
                <h5 class="text-xl font-bold text-blue-700 mb-5 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Pemeriksaan
                </h5>

                <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0 items-center justify-center">
                    <!-- Kriteria Pemeriksaan -->
                    <div class="bg-white p-6 rounded-lg border border-blue-200 shadow-sm w-full lg:w-2/3">
                        <h6 class="text-lg font-semibold text-blue-600 mb-4">Standar Kriteria Pemeriksaan:</h6>
                        <ul class="space-y-4 text-gray-800 text-sm">
                            @foreach ([
                                ['Motor Mesin Giling', 'Suara halus, tidak panas berlebih'],
                                ['Vanbelt', 'Tidak pecah/retak, kekencangan sesuai standar'],
                                ['Dustcollector', 'Berfungsi normal, tidak tersumbat'],
                                ['Safety Switch', 'Berfungsi dengan baik saat diuji'],
                                ['Ketajaman Pisau', 'Tajam dan tidak tumpul, tidak ada kerusakan (Pemeriksaan pada minggu keempat setiap bulan)']
                            ] as [$title, $desc])
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 mr-2 text-green-500 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span><strong>{{ $title }}:</strong> {{ $desc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Keterangan Status -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-blue-200 w-full lg:w-1/3">
                        <p class="text-lg font-semibold text-blue-800 mb-4">Keterangan Status:</p>
                        <div class="grid grid-cols-2 gap-3 text-sm text-gray-800">
                            @foreach ([
                                ['V', 'Baik/Normal', 'green'],
                                ['X', 'Tidak Baik/Abnormal', 'red'],
                                ['-', 'Tidak Diisi', 'white'],
                                ['OFF', 'Mesin Mati', 'white']
                            ] as [$symbol, $label, $color])
                                <div class="flex items-center">
                                    <span class="inline-block w-7 h-7 bg-{{ $color }}-100 text-{{ $color }}-700 text-center font-bold mr-3 rounded">{{ $symbol }}</span>
                                    <span>{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Catatan Tambahan -->
            <div class="mb-6">
                <label for="catatan" class="block mb-2 text-sm font-medium text-gray-700">Catatan Tambahan:</label>
                <textarea id="catatan" name="catatan" rows="5" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" placeholder="Tulis catatan tambahan di sini jika diperlukan..."></textarea>
            </div>
            
            <!-- Tombol Submit dan Kembali -->
            @include('components.create-form-buttons', ['backRoute' => route('giling.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function gilingForm() {
        return {
            selectedWeek: '',
        }
    }
</script>
@endsection