<!-- resources/views/air-dryer/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Pencatatan Mesin Air Dryer')
@section('page-title', 'Edit Pencatatan Mesin Air Dryer')

@section('show-checker', true)


@section('content')
    <!-- Form Input -->
    <form action="{{ route('air-dryer.update', $airDryer->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <!-- Input Hari dan Tanggal -->
            <div>
                <label class="block mb-2">Hari:</label>
                <input type="text" id="hari" name="hari" class="w-full px-3 py-2 bg-white border border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md" value="{{ $airDryer->hari }}" readonly>
            </div>
            <div>
                <label class="block mb-2">Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" class="w-full px-3 py-2 bg-white border border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md" value="{{ $airDryer->tanggal }}" readonly>
            </div>
        </div>
        
        <!-- Tabel Air Dryer -->
        <div class="mb-6">
            <div class="overflow-x-auto mb-6 border border-gray-300">
                <table class="w-full border-collapse">
                    <thead class="sticky top-0 z-10 bg-sky-50">
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">No</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Nomor Mesin</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Kompresor</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Kabel</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur MCB</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Angin In</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Temperatur Angin Out</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Evaporator</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 sticky top-0">Fan Evaporator</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-[140px] sticky top-0">Auto Drain</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 1; $i <= 8; $i++)
                            <tr>
                                <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                <td class="border border-gray-300 text-center p-2">AD{{ $i }}</td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_kompresor[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C" value="{{ $details->where('nomor_mesin', 'AD'.$i)->first()->temperatur_kompresor ?? '' }}">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_kabel[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C" value="{{ $details->where('nomor_mesin', 'AD'.$i)->first()->temperatur_kabel ?? '' }}">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_mcb[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C" value="{{ $details->where('nomor_mesin', 'AD'.$i)->first()->temperatur_mcb ?? '' }}">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_angin_in[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C" value="{{ $details->where('nomor_mesin', 'AD'.$i)->first()->temperatur_angin_in ?? '' }}">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <input type="text" name="temperatur_angin_out[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C" value="{{ $details->where('nomor_mesin', 'AD'.$i)->first()->temperatur_angin_out ?? '' }}">
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        @php
                                            $evaporator = $details->where('nomor_mesin', 'AD'.$i)->first()->evaporator ?? '';
                                        @endphp
                                        <option value="V" {{ $evaporator == 'V' ? 'selected' : '' }}>V</option>
                                        <option value="X" {{ $evaporator == 'X' ? 'selected' : '' }}>X</option>
                                        <option value="-" {{ $evaporator == '-' ? 'selected' : '' }}>-</option>
                                        <option value="OFF" {{ $evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        @php
                                            $fan_evaporator = $details->where('nomor_mesin', 'AD'.$i)->first()->fan_evaporator ?? '';
                                        @endphp
                                        <option value="V" {{ $fan_evaporator == 'V' ? 'selected' : '' }}>V</option>
                                        <option value="X" {{ $fan_evaporator == 'X' ? 'selected' : '' }}>X</option>
                                        <option value="-" {{ $fan_evaporator == '-' ? 'selected' : '' }}>-</option>
                                        <option value="OFF" {{ $fan_evaporator == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                                <td class="border border-gray-300 p-2">
                                    <select name="auto_drain[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                        @php
                                            $auto_drain = $details->where('nomor_mesin', 'AD'.$i)->first()->auto_drain ?? '';
                                        @endphp
                                        <option value="V" {{ $auto_drain == 'V' ? 'selected' : '' }}>V</option>
                                        <option value="X" {{ $auto_drain == 'X' ? 'selected' : '' }}>X</option>
                                        <option value="-" {{ $auto_drain == '-' ? 'selected' : '' }}>-</option>
                                        <option value="OFF" {{ $auto_drain == 'OFF' ? 'selected' : '' }}>OFF</option>
                                    </select>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                    
                </table>
            </div>
        </div>
        
        <!-- Informasi Mesin dan Kriteria -->
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h5 class="text-lg font-semibold text-blue-700 mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informasi Standar Pemeriksaan
            </h5>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Kriteria Pemeriksaan -->
                <div class="bg-white p-4 rounded-lg border border-blue-100">
                    <h6 class="font-medium text-blue-600 mb-3">Standar Kriteria Pemeriksaan:</h6>
                    <ul class="space-y-2 text-gray-700 text-sm">
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Temperatur Kompresor:</strong> 30°C - 60°C</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Temperatur Kabel:</strong> 30°C - 60°C</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Temperatur MCB:</strong> 30°C - 60°C</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Temperatur Angin In:</strong> 30°C - 60°C</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Temperatur Angin Out:</strong> 30°C - 60°C</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Evaporator:</strong> Bersih/Kotor</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Fan Evaporator:</strong> Suara Halus/Kasar</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><strong>Auto Drain:</strong> Berfungsi/Tidak Berfungsi</span>
                        </li>
                    </ul>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Detail Mesin -->
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h6 class="font-medium text-blue-600 mb-3">Detail Mesin:</h6>
                        <div class="space-y-1 text-sm text-gray-800">
                            <p>AD 1 : HIGH PRESS 1</p>
                            <p>AD 2 : HIGH PRESS 2</p>
                            <p>AD 3 : LOW PRESS 1</p>
                            <p>AD 4 : LOW PRESS 2</p>
                            <p>AD 5 : SUPPLY INJECT</p>
                            <p>AD 6 : LOW PRESS 3</p>
                            <p>AD 7 : LOW PRESS 4</p>
                            <p>AD 8 : LOW PRESS 5</p>
                        </div>
                    </div>
                    
                    <!-- Keterangan Status -->
                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                        <h6 class="font-medium text-blue-600 mb-3">Keterangan Status:</h6>
                        <div class="grid grid-cols-1 gap-2 text-sm text-gray-800">
                            <div class="flex items-center">
                                <span class="inline-block w-6 h-6 bg-green-100 text-green-700 text-center font-bold mr-3 rounded">V</span>
                                <span>Baik/Normal</span>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-block w-6 h-6 bg-red-100 text-red-700 text-center font-bold mr-3 rounded">X</span>
                                <span>Tidak Baik/Abnormal</span>
                            </div>
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
        
        <!-- Catatan Tambahan -->
        <div class="mb-6">
            <label for="catatan" class="block mb-2 text-sm font-medium text-gray-700">Catatan Tambahan:</label>
            <textarea id="catatan" name="catatan" rows="5" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" placeholder="Tulis catatan tambahan di sini jika diperlukan...">{{ $airDryer->keterangan }}</textarea>
        </div>
        
        <!-- Tombol Submit dan Kembali -->
        @include('components.edit-form-buttons', ['backRoute' => route('air-dryer.index')])


    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Add any additional scripts needed
    });
    
    // Add script to automatically set day based on selected date
    document.addEventListener('DOMContentLoaded', function() {
        const tanggalInput = document.getElementById('tanggal');
        const hariInput = document.getElementById('hari');
        
        // Days in Indonesian
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        
        // Update hari when date changes
        tanggalInput.addEventListener('change', function() {
            const date = new Date(this.value);
            if (!isNaN(date)) {
                const dayIndex = date.getDay();
                hariInput.value = days[dayIndex];
            } else {
                hariInput.value = '';
            }
        });
        
        // Set initial day if date is already selected
        if (tanggalInput.value) {
            const date = new Date(tanggalInput.value);
            if (!isNaN(date)) {
                const dayIndex = date.getDay();
                hariInput.value = days[dayIndex];
            }
        }
    });
</script>
@endsection