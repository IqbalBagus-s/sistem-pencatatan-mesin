<!-- resources/views/caplining/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Caplining')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Caplining</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('caplining.approve', $check->id) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    @php
                        // Extract all unique checker names from the results collection
                        $checkers = $results->pluck('checked_by')->filter()->unique()->implode(', ');
                    @endphp
                    {{ $checkers ?: 'Belum ada checker' }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-1 gap-4 mb-4">
                <!-- No Caplining Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Caplining:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Caplining {{ $check->nomer_caplining }}
                    </div>
                </div>
            </div>                 
            @php
                // Items yang perlu di-check (sesuai dengan halaman create)
                $items = [
                    1 => 'Kelistrikan',
                    2 => 'MCB',
                    3 => 'PLC',
                    4 => 'Power Supply',
                    5 => 'Relay',
                    6 => 'Selenoid',
                    7 => 'Selang Angin',
                    8 => 'Regulator',
                    9 => 'Pir',
                    10 => 'Motor',
                    11 => 'Vanbelt',
                    12 => 'Conveyor',
                    13 => 'Motor Conveyor',
                    14 => 'Vibrator',
                    15 => 'Motor Vibrator',
                    16 => 'Gear Box',
                    17 => 'Rantai',
                    18 => 'Stang Penggerak',
                    19 => 'Suction Pad',
                    20 => 'Sensor',
                ];

                // Opsi check dengan ikon
                $options = [
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];
                
                // Helper function untuk mendapatkan hasil check berdasarkan tanggal check dan item
                function getCheckResult($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal_check dan item_id
                    $result = $results->where('tanggal_check', $date)->where('item_id', $itemId)->first();
                    return $result && isset($result['result']) ? $result['result'] : null;
                }

                // Helper function untuk mendapatkan keterangan berdasarkan tanggal check dan item
                function getKeterangan($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal_check dan item_id
                    $result = $results->where('tanggal_check', $date)->where('item_id', $itemId)->first();
                    return $result && isset($result['keterangan']) ? $result['keterangan'] : '';
                }

                // Helper function untuk mendapatkan nama checker berdasarkan tanggal check
                function getCheckerName($results, $date) {
                    // Filter hasil berdasarkan tanggal_check
                    $result = $results->where('tanggal_check', $date)->first();
                    return $result && isset($result['checked_by']) ? $result['checked_by'] : '';
                }
                
                // Helper function untuk mendapatkan nama penanggung jawab berdasarkan tanggal check
                function getApprovedBy($results, $date) {
                    // Filter hasil berdasarkan tanggal_check
                    $result = $results->where('tanggal_check', $date)->first();
                    return $result && isset($result['approved_by']) ? $result['approved_by'] : '';
                }
                
                // Helper function untuk mendapatkan tanggal dari hasil check
                function getTanggal($results, $date) {
                    // Filter hasil berdasarkan tanggal_check
                    $result = $results->where('tanggal_check', $date)->first();
                    return $result && isset($result['tanggal']) ? $result['tanggal'] : '';
                }
            @endphp
            
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 1; $i <= 5; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">
                                        {{ getTanggal($results, $i) ?: '-' }}
                                    </th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-40" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 1; $i <= 5; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-20">Cek</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10">
                                        <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                    </td>
                                    
                                    @for($j = 1; $j <= 5; $j++)
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @php
                                                $result = getCheckResult($results, $j, $i);
                                                echo isset($options[$result]) ? $options[$result] : '';
                                            @endphp
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            {{ getKeterangan($results, $j, $i) }}
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 5; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        {{ getCheckerName($results, $j) ?: '-' }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 1; $j <= 5; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-green-50">
                                        @php
                                            $approvedBy = getApprovedBy($results, $j);
                                        @endphp
                                        
                                        @if($approvedBy)
                                            <!-- Jika sudah ada penanggung jawab, tampilkan saja namanya -->
                                            <div class="w-full px-2 py-1 text-sm">
                                                <input type="text" name="approved_by_{{ $j }}" value="{{ $approvedBy }}"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="approve_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                        @else
                                            <!-- Jika belum ada penanggung jawab, tampilkan tombol pilih -->
                                            <div x-data="{ selected: false, userName: '' }">
                                                <div x-show="!selected">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userName = '{{ Auth::user()->username }}'; 
                                                            $refs.approver{{ $j }}.value = userName;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div class="mt-1" x-show="selected">
                                                    <input type="text" name="approved_by_{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userName"
                                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center mb-1"
                                                        readonly>
                                                    <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="{{ $j }}">
                                                    <button type="button" 
                                                        @click="selected = false; 
                                                            userName = ''; 
                                                            $refs.approver{{ $j }}.value = '';
                                                            $refs.approveNum{{ $j }}.value = '';"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                        Batal Pilih
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- catatan pemeriksaan --}}
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-5 rounded-lg shadow-sm mb-6 border-l-4 border-blue-400">
                <h5 class="text-lg font-semibold text-blue-700 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Pemeriksaan
                </h5>
                
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span> Pengecekan mesin, empat hari sebelum mesin dijadwalkan jalan.</span>
                    </li>
                </ul>
            </div>
            
            <!-- Button Controls -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('caplining.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Kembali
                </a>
                <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded hover:bg-blue-800">
                    Setujui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection