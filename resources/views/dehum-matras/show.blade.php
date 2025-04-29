<!-- resources/views/dehum-matras/show.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Detail Pencatatan Mesin Dehum Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Dehum Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('dehum-matras.approve', $check->id) }}" id="approveForm" autocomplete="off">
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
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- No Dehum Matras Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Dehum Matras:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Dehum Matras {{ $check->nomer_dehum_matras }}
                    </div>
                </div>
                
                <!-- Shift Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Shift:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Shift {{ $check->shift }}
                    </div>
                </div>

                <!-- Bulan Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($check->bulan)->format('F Y') }}
                    </div>
                </div>
            </div>                 
            @php
                // Items yang perlu di-check untuk Dehum Matras
                $items = [
                    1 => 'Kompressor',
                    2 => 'Kabel',
                    3 => 'NFB',
                    4 => 'Motor',
                    5 => 'Water Cooler in',
                    6 => 'Water Cooler Out',
                    7 => 'Temperatur Output Udara',
                ];
                
                // Helper function untuk mendapatkan hasil check berdasarkan tanggal dan item
                function getCheckResult($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal dan item_id
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    return $result && isset($result['result']) ? $result['result'] : null;
                }

                // Helper function untuk mendapatkan keterangan berdasarkan tanggal dan item
                function getKeterangan($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal dan item_id
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    return $result && isset($result['keterangan']) ? $result['keterangan'] : '';
                }

                // Helper function untuk mendapatkan nama checker berdasarkan tanggal
                function getCheckerName($results, $date) {
                    // Filter hasil berdasarkan tanggal
                    $result = $results->where('tanggal', $date)->first();
                    return $result && isset($result['checked_by']) ? $result['checked_by'] : '';
                }
                
                // Helper function untuk mendapatkan nama penanggung jawab berdasarkan tanggal
                function getApprovedBy($results, $date) {
                    // Filter hasil berdasarkan tanggal
                    $result = $results->where('tanggal', $date)->first();
                    return $result && isset($result['approved_by']) ? $result['approved_by'] : '';
                }
            @endphp
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <!-- Tabel untuk tanggal 1-11 -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 1; $i <= 11; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 1; $i <= 11; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-20">Hasil</th>
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
                                    
                                    @for($j = 1; $j <= 11; $j++)
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            {{ getCheckResult($results, $j, $i) ?: '-' }}
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    <td class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        {{ getCheckerName($results, $j) ?: '-' }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 1--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
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
                
                <!-- Tabel untuk tanggal 12-22 -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 12; $i <= 22; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 12; $i <= 22; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-20">Hasil</th>
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
                                    
                                    @for($j = 12; $j <= 22; $j++)
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            {{ getCheckResult($results, $j, $i) ?: '-' }}
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 12; $j <= 22; $j++)
                                    <td class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        {{ getCheckerName($results, $j) ?: '-' }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 2--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 12; $j <= 22; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
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
                <!-- Tabel untuk tanggal 23-31 -->
                <div class="overflow-x-auto mb-6">
                    <table class="w-full border-collapse table-fixed" style="width: max-content;">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 23; $i <= 31; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 23; $i <= 31; $i++)
                                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-20">Hasil</th>
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
                                    
                                    @for($j = 23; $j <= 31; $j++)
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            {{ getCheckResult($results, $j, $i) ?: '-' }}
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 23; $j <= 31; $j++)
                                    <td class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        {{ getCheckerName($results, $j) ?: '-' }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 3--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 23; $j <= 31; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
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

            <!-- Button Controls -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('dehum-matras.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
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