<!-- resources/views/air-dryer/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Air Dryer')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Air Dryer</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Header dengan Info Petugas -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="bg-sky-50 p-4 rounded-md">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">{{ $airDryer->checked_by }}</span>
            </div>
            <div class="bg-sky-50 p-4 rounded-md">
                <span class="text-gray-600 font-bold">Approver: </span>
                <span class="font-bold text-blue-700">
                    {{ $airDryer->approved_by ?: Auth::user()->username }}
                </span>
            </div>
        </div>

        <!-- Tanggal dan Hari -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="w-full">
                <label class="block mb-2 text-sm font-medium text-gray-700">Hari:</label>
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    {{ $airDryer->hari }}
                </div>
            </div>
            <div class="w-full">
                <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal:</label>
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    {{ \Carbon\Carbon::parse($airDryer->tanggal)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
        
        <!-- Tabel Air Dryer -->
        <div class="mb-6">
            <div class="overflow-x-auto border border-gray-300 rounded-lg">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-sky-50">
                            <th class="border border-gray-300 p-2 text-center">No</th>
                            <th class="border border-gray-300 p-2 text-center">Nomor Mesin</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Kompresor</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Kabel</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur MCB</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Angin In</th>
                            <th class="border border-gray-300 p-2 text-center">Temperatur Angin Out</th>
                            <th class="border border-gray-300 p-2 text-center">Evaporator</th>
                            <th class="border border-gray-300 p-2 text-center">Fan Evaporator</th>
                            <th class="border border-gray-300 p-2 text-center">Auto Drain</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $index => $detail)
                            <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="border border-gray-300 text-center p-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->nomor_mesin }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->temperatur_kompresor }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->temperatur_kabel }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->temperatur_mcb }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->temperatur_angin_in }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->temperatur_angin_out }}</td>
                                <td class="border border-gray-300 text-center p-2">
                                    <span class="{{ $detail->evaporator == 'V' ? 'text-green-600 font-bold' : ($detail->evaporator == 'X' ? 'text-red-600 font-bold' : '') }}">
                                        {{ $detail->evaporator }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 text-center p-2">
                                    <span class="{{ $detail->fan_evaporator == 'V' ? 'text-green-600 font-bold' : ($detail->fan_evaporator == 'X' ? 'text-red-600 font-bold' : '') }}">
                                        {{ $detail->fan_evaporator }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 text-center p-2">
                                    <span class="{{ $detail->auto_drain == 'V' ? 'text-green-600 font-bold' : ($detail->auto_drain == 'X' ? 'text-red-600 font-bold' : '') }}">
                                        {{ $detail->auto_drain }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
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
            <div class="border border-gray-300 rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-2">Catatan Tambahan:</h3>
                <div class="bg-gray-50 p-3 rounded">
                    {{ $airDryer->keterangan }}
                </div>
            </div>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="mt-8 bg-white rounded-lg shadow p-2 sm:p-4">
            <div class="flex flex-row flex-wrap items-center justify-between gap-2">
                <!-- Back Button - Left Side -->
                <div class="flex-shrink-0">
                    <a href="{{ route('air-dryer.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
                
                <!-- Action Buttons - Right Side -->
                <div class="flex flex-row flex-wrap gap-2 justify-end">
                    <!-- Conditional rendering based on approval status -->
                    @if (empty($airDryer->approved_by))
                        <!-- Approval Button -->
                        <form action="{{ route('air-dryer.approve', $airDryer->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Setujui
                            </button>
                        </form>
                    @else
                        <!-- PDF Preview Button -->
                        <a href="{{ route('air-dryer.pdf', $airDryer->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>
                        
                        <!-- Download PDF Button -->
                        <a href="{{ route('air-dryer.downloadPdf', $airDryer->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection