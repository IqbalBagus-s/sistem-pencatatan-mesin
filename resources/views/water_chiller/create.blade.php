<!-- resources/views/water-chiller/create.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Form Pencatatan Mesin Water Chiller')

@section('content')
<h2 class="mb-4 text-xl font-bold">Pencatatan Mesin Water Chiller</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('water-chiller.store') }}" method="POST" autocomplete="off">
            @csrf
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Input Hari dan Tanggal -->
                <div>
                    <label class="block mb-2">Hari:</label>
                    <input type="text" id="hari" name="hari" class="w-full px-3 py-2 bg-white border border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md" readonly>
                </div>
                <div>
                    <label class="block mb-2">Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" class="w-full px-3 py-2 bg-white border border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-500 rounded-md" required>
                </div>
            </div>
            
            <!-- Tabel Water Chiller -->
            <div class="mb-6 relative">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <div class="md:hidden text-sm text-gray-500 italic mb-2">
                        ← Geser ke kanan untuk melihat semua kolom →
                    </div>
                    <table class="w-full border-collapse">
                        <thead class="bg-sky-50 sticky top-0 z-10" id="tableHeader">
                            <tr>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 40px">No</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 100px">Nomor Mesin</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 120px">Temperatur Kompresor</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 120px">Temperatur Kabel</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 120px">Temperatur MCB</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 120px">Temperatur Air</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 120px">Temperatur Pompa</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 80px">Evaporator</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 80px">Fan Evaporator</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 80px">Freon</th>
                                <th class="border border-gray-300 p-1 text-sm" style="width: 80px">Air</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 32; $i++)
                                <tr>
                                    <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                                    <td class="border border-gray-300 text-center p-2">CH{{ $i }}</td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_kompresor[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_kabel[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_mcb[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_air[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <input type="text" name="temperatur_pompa[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" placeholder="30°C - 60°C">
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <select name="evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            <option value="V">V</option>
                                            <option value="X">X</option>
                                            <option value="-">-</option>
                                            <option value="OFF">OFF</option>
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <select name="fan_evaporator[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            <option value="V">V</option>
                                            <option value="X">X</option>
                                            <option value="-">-</option>
                                            <option value="OFF">OFF</option>
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <select name="freon[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            <option value="V">V</option>
                                            <option value="X">X</option>
                                            <option value="-">-</option>
                                            <option value="OFF">OFF</option>
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 p-2">
                                        <select name="air[{{ $i }}]" class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            <option value="V">V</option>
                                            <option value="X">X</option>
                                            <option value="-">-</option>
                                            <option value="OFF">OFF</option>
                                        </select>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- catatan pemeriksaan --}}
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
                <label for="catatan" class="block mb-2 text-sm font-medium text-gray-700">Catatan Tambahan:</label>
                <textarea id="catatan" name="catatan" rows="5" class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" placeholder="Tulis catatan tambahan di sini jika diperlukan..."></textarea>
            </div>
            
            <!-- Tombol Submit dan Kembali -->
            @include('components.create-form-buttons', ['backRoute' => route('water-chiller.index')])

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        // Add any additional scripts needed
    });
</script>
@endsection