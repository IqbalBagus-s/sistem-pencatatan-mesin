<!-- resources/views/water-chiller/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Water Chiller')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Water Chiller</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Header dengan Info Petugas -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="bg-sky-50 p-4 rounded-md">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">{{ $waterChillerCheck->checker?->username }}<span>
            </div>
            <div class="bg-sky-50 p-4 rounded-md">
                <span class="text-gray-600 font-bold">Approver: </span>
                <span class="font-bold text-blue-700">
                    {{ $waterChillerCheck->approver?->username ?: $user->username }}
                </span>
            </div>
        </div>

        <!-- Tanggal dan Hari -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div class="w-full">
                <label class="block mb-2 text-sm font-medium text-gray-700">Hari:</label>
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    {{ $waterChillerCheck->hari }}
                </div>
            </div>
            <div class="w-full">
                <label class="block mb-2 text-sm font-medium text-gray-700">Tanggal:</label>
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    {{ \Carbon\Carbon::parse($waterChillerCheck->tanggal)->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
        
        <!-- Tabel Water Chiller -->
        <div class="mb-6">
            <div class="overflow-x-auto border border-gray-300 rounded-lg">
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-sky-50">
                            <th class="border border-gray-300 p-1 text-sm" style="min-width: 40px">No</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 100px">Nomor Mesin</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 120px">Temperatur Kompresor</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 120px">Temperatur Kabel</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 120px">Temperatur MCB</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 120px">Temperatur Air</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 120px">Temperatur Pompa</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 80px">Evaporator</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 80px">Fan Evaporator</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 80px">Freon</th>
                                <th class="border border-gray-300 p-1 text-md" style="min-width: 80px">Air</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $index => $detail)
                            <tr class="{{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                <td class="border border-gray-300 text-center p-2">{{ $index + 1 }}</td>
                                <td class="border border-gray-300 text-center p-2">CH{{ $index + 1 }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->Temperatur_Compressor }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->Temperatur_Kabel }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->Temperatur_Mcb }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->Temperatur_Air }}</td>
                                <td class="border border-gray-300 text-center p-2">{{ $detail->Temperatur_Pompa }}</td>
                                <td class="border border-gray-300 text-center p-2">
                                    <span class="{{ $detail->Evaporator == 'V' ? 'text-green-600 font-bold' : ($detail->Evaporator == 'X' ? 'text-red-600 font-bold' : '') }}">
                                        {{ $detail->Evaporator }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 text-center p-2">
                                    <span class="{{ $detail->Fan_Evaporator == 'V' ? 'text-green-600 font-bold' : ($detail->Fan_Evaporator == 'X' ? 'text-red-600 font-bold' : '') }}">
                                        {{ $detail->Fan_Evaporator }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 text-center p-2">
                                    <span class="{{ $detail->Freon == 'V' ? 'text-green-600 font-bold' : ($detail->Freon == 'X' ? 'text-red-600 font-bold' : '') }}">
                                        {{ $detail->Freon }}
                                    </span>
                                </td>
                                <td class="border border-gray-300 text-center p-2">
                                    <span class="{{ $detail->Air == 'V' ? 'text-green-600 font-bold' : ($detail->Air == 'X' ? 'text-red-600 font-bold' : '') }}">
                                        {{ $detail->Air }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Informasi Mesin dan Kriteria -->
        <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-5 rounded-lg shadow-sm mb-6 border-l-4 border-blue-400">
                <h5 class="text-lg font-semibold text-blue-700 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Pemeriksaan
                </h5>

                <div class="bg-white p-6 rounded-lg border border-blue-200 shadow-sm">
                    <h6 class="font-medium text-blue-600 mb-4 text-lg">Standar Kriteria Pemeriksaan:</h6>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="font-semibold text-blue-800 mb-1">Temperatur</p>
                            <ul class="space-y-1 text-gray-700 text-sm">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Kompresor: 30°C - 60°C</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Kabel: 30°C - 60°C</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>MCB: 30°C - 60°C</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="font-semibold text-blue-800 mb-1">Sistem</p>
                            <ul class="space-y-1 text-gray-700 text-sm">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Air: 30°C - 60°C</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Pompa: 30°C - 60°C</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="font-semibold text-blue-800 mb-1">Komponen</p>
                            <ul class="space-y-1 text-gray-700 text-sm">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Evaporator: V / X / - / OFF</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Fan Evaporator: V / X / - / OFF</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Freon: V / X / - / OFF</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Air: V / X / - / OFF</span>
                                </li>
                            </ul>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg col-span-1 md:col-span-2 lg:col-span-3">
                            <p class="font-semibold text-blue-800 mb-1">Keterangan Status:</p>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm text-gray-700">
                                <div class="flex items-center">
                                    <span class="inline-block w-5 h-5 bg-green-100 text-green-700 text-center font-bold mr-2 rounded">V</span>
                                    <span>Baik/Normal</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-block w-5 h-5 bg-red-100 text-red-700 text-center font-bold mr-2 rounded">X</span>
                                    <span>Tidak Baik/Abnormal</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-block w-5 h-5 bg-gray-100 text-gray-700 text-center font-bold mr-2 rounded">-</span>
                                    <span>Tidak Diisi</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-block w-5 h-5 bg-gray-100 text-gray-700 text-center font-bold mr-2 rounded">OFF</span>
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
                    {{ $waterChillerCheck->keterangan }}
                </div>
            </div>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="mt-8 bg-white rounded-lg shadow p-2 sm:p-4">
            <div class="flex flex-row flex-wrap items-center justify-between gap-2">
                <!-- Back Button - Left Side -->
                <div class="flex-shrink-0">
                    <a href="{{ route('water-chiller.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </a>
                </div>
                
                <!-- Action Buttons - Right Side -->
                <div class="flex flex-row flex-wrap gap-2 justify-end">
                    <!-- Conditional rendering based on approval status -->
                    @if ($waterChillerCheck->status === 'belum_disetujui' && $currentGuard === 'approver')
                        <!-- Approval Button -->
                        <form action="{{ route('water-chiller.approve', $waterChillerCheck->id) }}" method="POST">
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
                        <a href="{{ route('water-chiller.pdf', $waterChillerCheck->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>
                        
                        <!-- Download PDF Button -->
                        <a href="{{ route('water-chiller.downloadPdf', $waterChillerCheck->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
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