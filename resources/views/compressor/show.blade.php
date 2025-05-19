<!-- resources/views/compressor/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pemeriksaan Mesin Compressor')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Compressor</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Form wrapper dengan Alpine.js untuk persetujuan -->
        <form action="{{ route('compressor.approve', $check->id) }}" method="POST" id="approvalForm"
            x-data="{
                dbShift1: '{{ $check->approved_by_shift1 }}',
                dbShift2: '{{ $check->approved_by_shift2 }}',
                shift1: '{{ $check->approved_by_shift1 }}',
                shift2: '{{ $check->approved_by_shift2 }}',
                formChanged: false,
                
                pilihShift(shift) {
                    const user = '{{ Auth::user()->username }}';
                    if (shift === 1) {
                        this.shift1 = user;
                    } else if (shift === 2) {
                        this.shift2 = user;
                    }
                    this.updateFormChanged();
                },
                
                batalPilih(shift) {
                    if (shift === 1) {
                        this.shift1 = '';
                    } else if (shift === 2) {
                        this.shift2 = '';
                    }
                    this.updateFormChanged();
                },
                
                updateFormChanged() {
                    this.formChanged = (this.shift1 !== this.dbShift1) || (this.shift2 !== this.dbShift2);
                },
                
                canSubmit() {
                    return this.formChanged && (this.shift1 !== '' || this.shift2 !== '');
                }
            }">
            @csrf
            <!-- Hidden fields for form submission -->
            <input type="hidden" name="shift1" x-model="shift1">
            <input type="hidden" name="shift2" x-model="shift2">

            <!-- Header dengan Info Petugas -->
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div class="bg-sky-50 p-4 rounded-md">
                    <span class="text-gray-600 font-bold">Checker Shift 1: </span>
                    <span class="font-bold text-blue-700">{{ $check->checked_by_shift1 ?: 'Belum diisi' }}</span>
                </div>
                <div class="bg-sky-50 p-4 rounded-md">
                    <span class="text-gray-600 font-bold">Checker Shift 2: </span>
                    <span class="font-bold text-blue-700">{{ $check->checked_by_shift2 ?: 'Belum diisi' }}</span>
                </div>
            </div>

            <!-- Tanggal dan Hari -->
            <div class="grid md:grid-cols-2 gap-4 mb-6">
            <!-- Hari -->
            <div>
                <label class="block mb-1 font-medium text-gray-700">Hari</label>
                <div class="px-3 py-2 bg-white border border-blue-400 rounded-lg shadow-sm">
                {!! $check->hari
                    ? e($check->hari)
                    : '<span class="text-gray-400">Belum diisi</span>' !!}
                </div>
            </div>
            <!-- Tanggal -->
            <div>
                <label class="block mb-1 font-medium text-gray-700">Tanggal</label>
                <div class="px-3 py-2 bg-white border border-blue-400 rounded-lg shadow-sm">
                {!! $check->tanggal
                    ? e(\Carbon\Carbon::parse($check->tanggal)->translatedFormat('d F Y'))
                    : '<span class="text-gray-400">Belum diisi</span>' !!}
                </div>
            </div>
            </div>

            <!-- Informasi Compressor -->
            <div class="mb-6">
            <label class="block mb-2 font-medium text-gray-700">Informasi Compressor</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ([
                'KL Aktif'  => $check->kompressor_on_kl,
                'KH Aktif'  => $check->kompressor_on_kh,
                'Mesin ON'  => $check->mesin_on,
                'Mesin OFF' => $check->mesin_off,
                ] as $label => $value)
                <div>
                    <div class="text-sm text-gray-600 mb-1">{{ $label }}</div>
                    <div class="px-3 py-2 bg-white border border-blue-400 rounded-lg shadow-sm text-center">
                    {!! $value
                        ? e($value)
                        : '<span class="text-gray-400">Belum diisi</span>' !!}
                    </div>
                </div>
                @endforeach
            </div>
            </div>

            <!-- Kelembapan Udara -->
            <div class="mb-6">
                <label class="block mb-2 font-medium text-gray-700">Kelembapan Udara</label>
                <div class="space-y-0">
                    {{-- Wrapper besar dengan 1 border --}}
                    <div class="bg-white border border-blue-400 rounded-lg shadow-sm">
                        @foreach ([1,2] as $shift)
                            <div class="p-4">
                                <div class="font-medium text-gray-800 mb-2">Shift {{ $shift }}</div>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach (['Temperatur' => "temperatur_shift{$shift}", 'Humidity' => "humidity_shift{$shift}"] as $subLabel => $field)
                                        <div>
                                            <div class="text-xs text-gray-600 mb-1">{{ $subLabel }}</div>
                                            <div class="px-2 py-1 bg-gray-50 border border-blue-400 rounded text-center text-sm">
                                                {!! $check->{$field}
                                                ? e($check->{$field})
                                                : '<span class="text-gray-400">Belum diisi</span>' !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


            <!-- Low Compressor Table -->
            <div class="text-lg font-semibold mb-4 mt-4">
                Data Low Compressor
            </div>

            <div class="overflow-x-auto max-h-[500px]">
                <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
                    <thead class="bg-sky-50 text-center sticky top-0 z-10">
                        <tr>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">No.</th>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">Checked Items</th>
                            <th class="border border-gray-300 p-2 sticky top-0" colspan="12">Hasil Pemeriksaan</th>
                        </tr>
                        <tr class="sticky top-[41px] z-10">
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KL 10</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KL 5</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KL 6</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KL 7</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KL 8</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KL 9</th>
                        </tr>
                        <tr class="sticky top-[82px] z-10">
                            @for ($i = 0; $i < 6; $i++)
                                <th class="border border-gray-300 p-2 bg-sky-50">I</th>
                                <th class="border border-gray-300 p-2 bg-sky-50">II</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody class="text-sm text-center">
                        @php
                            $checkedItems = [
                                "Temperatur motor", "Temperatur screw", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                                "Ampere", "Skun", "Service hour", "Load hours", "Temperatur ADT"
                            ];
                        
                            // Kolom KL untuk tabel
                            $klDbColumns = ['kl_10I', 'kl_10II', 'kl_5I', 'kl_5II', 'kl_6I', 'kl_6II', 'kl_7I', 'kl_7II', 'kl_8I', 'kl_8II', 'kl_9I', 'kl_9II'];
                        @endphp
                    
                        @foreach ($lowResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                            @php 
                                $result = $resultGroup->first();
                            @endphp
                            <tr class="hover:bg-sky-50">
                                <td class="border border-gray-300 p-2">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 p-2 w-1/8 text-left">{{ $result->checked_items }}</td>
                    
                                @foreach ($klDbColumns as $klColumn)
                                    <td class="border border-gray-300 p-2 w-auto">
                                        {{ $result->$klColumn }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>                    
                </table>
            </div>

            <!-- High Compressor Table -->
            <div class="text-lg font-semibold mb-4 mt-8">
                Data High Compressor
            </div>

            <div class="overflow-x-auto max-h-[500px]">
                <table class="min-w-full border border-gray-300 shadow-lg rounded-lg bg-white border-collapse">
                    <thead class="bg-sky-50 text-center sticky top-0 z-10">
                        <tr>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">No.</th>
                            <th class="border border-gray-300 p-2 sticky top-0" rowspan="3">Checked Items</th>
                            <th class="border border-gray-300 p-2 sticky top-0" colspan="10">Hasil Pemeriksaan</th>
                        </tr>
                        <tr class="sticky top-[41px] z-10">
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KH 7</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KH 8</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KH 9</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KH 10</th>
                            <th class="border border-gray-300 p-2 bg-sky-50" colspan="2">KH 11</th>
                        </tr>
                        <tr class="sticky top-[82px] z-10">
                            @for ($i = 0; $i < 5; $i++)
                                <th class="border border-gray-300 p-2 bg-sky-50">I</th>
                                <th class="border border-gray-300 p-2 bg-sky-50">II</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="text-sm text-center">
                        @php
                            $checkedItems = [
                                "Temperatur Motor", "Temperatur Piston", "Temperatur oil", "Temperatur outlet", "Temperatur mcb",
                                "Compresor oil", "Air filter", "Oil filter", "Oil separator", "Oil radiator", 
                                "Suara mesin", "Loading", "Unloading/idle", "Temperatur kabel", "Voltage", 
                                "Ampere", "Skun", "Service hour", "Load hours", "Inlet Preasure", "Outlet Preasure"
                            ];
                        
                            // Kolom KH untuk tabel
                            $khDbColumns = ['kh_7I', 'kh_7II', 'kh_8I', 'kh_8II', 'kh_9I', 'kh_9II', 'kh_10I', 'kh_10II', 'kh_11I', 'kh_11II'];
                        @endphp
                    
                        @foreach ($highResults->groupBy('checked_items') as $itemIndex => $resultGroup)
                            @php 
                                $result = $resultGroup->first();
                            @endphp
                            <tr class="hover:bg-sky-50">
                                <td class="border border-gray-300 p-2">{{ $loop->iteration }}</td>
                                <td class="border border-gray-300 p-2 w-1/8 text-left">{{ $result->checked_items }}</td>
                    
                                @foreach ($khDbColumns as $khColumn)
                                    <td class="border border-gray-300 p-2 w-auto">
                                        {{ $result->$khColumn }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>                    
                </table>
            </div>
            
            <!-- Informasi Mesin dan Kriteria -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h5 class="text-lg font-semibold text-blue-700 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Informasi Standar Pemeriksaan
                </h5>

                <div class="grid grid-cols-1 gap-4">
                    <!-- Compressor Low -->
                    <div class="bg-white p-4 rounded-lg border border-blue-200">
                        <h6 class="font-medium text-blue-600 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Standar Kriteria Pemeriksaan Low Compressor:
                        </h6>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-gray-700 text-sm">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Motor:</strong> 50°C - 75°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Screw:</strong> 60°C - 90°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Oil:</strong> 80°C - 105°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Outlet:</strong> 30°C - 55°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur MCB:</strong> 30°C - 50°C</span>
                                </div>
                            </div>
                            <div class="space-y-2 text-gray-700 text-sm">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Oil Compressor:</strong> Penuh/Ditambah</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Filter (Air/Oil/Separator):</strong> Bersih/Kotor</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Suara Mesin:</strong> Halus/Kasar</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Kabel:</strong> 30°C - 55°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Voltage:</strong> > 380V</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur ADT:</strong> 80°C - 50°C</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Compressor High -->
                    <div class="bg-white p-4 rounded-lg border border-blue-200">
                        <h6 class="font-medium text-blue-600 mb-3 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Standar Kriteria Pemeriksaan High Compressor:
                        </h6>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2 text-gray-700 text-sm">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Motor:</strong> 50°C - 70°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Piston:</strong> 80°C - 105°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Oil:</strong> 80°C - 100°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Outlet:</strong> 30°C - 55°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur MCB:</strong> 30°C - 50°C</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Temperatur Kabel:</strong> 30°C - 55°C</span>
                                </div>
                            </div>
                            <div class="space-y-2 text-gray-700 text-sm">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Oil Compressor:</strong> Penuh/Ditambah</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Filter (Air/Oil/Separator):</strong> Bersih/Kotor</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Suara Mesin:</strong> Halus/Kasar</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Voltage:</strong> > 380V</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Inlet Pressure:</strong> 8Bar - 9Bar</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span><strong>Outlet Pressure:</strong> 22Bar - 30Bar</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kode Mesin dan Keterangan Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Detail Mesin -->
                        <div class="bg-white p-4 rounded-lg border border-blue-100">
                            <h6 class="font-medium text-blue-600 mb-3">Kode Mesin:</h6>
                            <div class="grid grid-cols-2 gap-2 text-sm text-gray-800">
                                <div>
                                    <p><strong>KL 5:</strong> Low Compressor 5</p>
                                    <p><strong>KL 6:</strong> Low Compressor 6</p>
                                    <p><strong>KL 7:</strong> Low Compressor 7</p>
                                    <p><strong>KL 8:</strong> Low Compressor 8</p>
                                    <p><strong>KL 9:</strong> Low Compressor 9</p>
                                    <p><strong>KL 10:</strong> Low Compressor 10</p>
                                </div>
                                <div>
                                    <p><strong>KH 7:</strong> High Compressor 7</p>
                                    <p><strong>KH 8:</strong> High Compressor 8</p>
                                    <p><strong>KH 9:</strong> High Compressor 9</p>
                                    <p><strong>KH 10:</strong> High Compressor 10</p>
                                    <p><strong>KH 11:</strong> High Compressor 11</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Keterangan Status -->
                        <div class="bg-white p-4 rounded-lg border border-blue-100">
                            <h6 class="font-medium text-blue-600 mb-3">Keterangan Status:</h6>
                            <div class="grid grid-cols-1 gap-2 text-sm text-gray-800">
                                <div class="flex items-center">
                                    <span class="inline-block w-6 h-6 border border-gray-300 text-gray-500 text-center font-bold mr-3 rounded">-</span>
                                    <span>Tidak Diisi</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-block w-6 h-6 border border-gray-300 text-gray-500 text-center font-bold mr-3 rounded">OFF</span>
                                    <span>Mesin Mati</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistem Persetujuan -->
            <div class="mb-6">
                <div class="border border-gray-300 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Persetujuan Laporan:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <!-- Shift 1 -->
                        <div class="p-4 bg-white shadow rounded border border-blue-300">
                            <label class="block text-gray-700 font-semibold mb-3">Shift 1</label>
                            
                            <div class="mb-3">
                                <input type="text" 
                                    class="w-full p-2 border rounded border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    x-model="shift1" readonly placeholder="Nama">
                            </div>
                            
                            <button type="button" 
                                x-show="!shift1" 
                                class="w-full bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-700 cursor-pointer" 
                                @click="pilihShift(1)">
                                Setujui
                            </button>
                            
                            <button type="button" 
                                x-show="shift1" 
                                class="w-full bg-red-500 text-white py-2 px-3 rounded hover:bg-red-600 cursor-pointer" 
                                @click="batalPilih(1)">
                                Batal Setujui
                            </button>
                        </div>

                        <!-- Shift 2 -->
                        <div class="p-4 bg-white shadow rounded border border-blue-300">
                            <label class="block text-gray-700 font-semibold mb-3">Shift 2</label>
                            
                            <div class="mb-3">
                                <input type="text" 
                                    class="w-full p-2 border rounded border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                    x-model="shift2" readonly placeholder="Nama">
                            </div>
                            
                            <button type="button" 
                                x-show="!shift2" 
                                class="w-full bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-700 cursor-pointer" 
                                @click="pilihShift(2)">
                                Setujui
                            </button>
                            
                            <button type="button" 
                                x-show="shift2" 
                                class="w-full bg-red-500 text-white py-2 px-3 rounded hover:bg-red-600 cursor-pointer" 
                                @click="batalPilih(2)">
                                Batal Setujui
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-8 bg-white rounded-lg p-2 sm:p-4">
                <div class="flex flex-row flex-wrap items-center justify-between gap-2">
                    <!-- Back Button - Left Side -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('compressor.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                    
                    <!-- Action Buttons - Right Side -->
                    <div class="flex flex-row flex-wrap gap-2 justify-end">
                        <!-- Simpan Persetujuan Button - Only shown when at least one approval is missing -->
                        @if (empty($check->approved_by_shift1) || empty($check->approved_by_shift2))
                            <button type="submit" 
                                class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out"
                                :class="{ 'opacity-50 cursor-not-allowed': !formChanged }"
                                :disabled="!formChanged">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Persetujuan
                            </button>
                        @endif
                        
                        <!-- PDF Preview and Download Buttons - Only shown when both approvals are present -->
                        @if (!empty($check->approved_by_shift1) && !empty($check->approved_by_shift2))
                            <!-- PDF Preview Button -->
                            <a href="{{ route('compressor.pdf', $check->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Preview PDF
                            </a>
                            
                            <!-- Download PDF Button -->
                            <a href="{{ route('compressor.downloadPdf', $check->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download PDF
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection