<!-- resources/views/autoloader/edit.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Edit Pencatatan Mesin Autoloader')

@section('content')
<h2 class="mb-4 text-xl font-bold">Edit Pencatatan Mesin Autoloader</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('autoloader.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Info Display (Not Editable) -->
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- No Autoloader Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Autoloader:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Autoloader {{ $check->nomer_autoloader }}
                    </div>
                    <input type="hidden" name="nomer_autoloader" value="{{ $check->nomer_autoloader }}">
                </div>
                
                <!-- Shift Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Shift:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Shift {{ $check->shift }}
                    </div>
                    <input type="hidden" name="shift" value="{{ $check->shift }}">
                </div>

                <!-- Bulan Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($check->bulan)->format('F Y') }}
                    </div>
                    <input type="hidden" name="bulan" value="{{ $check->bulan }}">
                </div>
            </div>                 
            @php
                // Items yang perlu di-check
                $items = [
                    1 => 'Filter',
                    2 => 'Selang',
                    3 => 'Panel Kelistrikan',
                    4 => 'Kontaktor',
                    5 => 'Thermal Overload',
                    6 => 'MCB',
                ];

                // Opsi check
                $options = [
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];
                
                // Helper function untuk mendapatkan hasil check berdasarkan tanggal dan item
                function getCheckResult($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal dan item_id
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    
                    // Debug untuk melihat data yang dikembalikan
                    // echo "Date: $date, Item: $itemId, Result: " . ($result ? $result['result'] : 'null') . "<br>";
                    
                    // Jika hasil ditemukan, kembalikan nilai result, jika tidak kembalikan null
                    return $result && isset($result['result']) ? $result['result'] : null;
                }

                // Helper function untuk mendapatkan keterangan berdasarkan tanggal dan item
                function getKeterangan($results, $date, $itemId) {
                    // Filter hasil berdasarkan tanggal dan item_id
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    
                    // Jika keterangan ditemukan, kembalikan nilainya, jika tidak kembalikan string kosong
                    return $result && isset($result['keterangan']) ? $result['keterangan'] : '';
                }

                // Helper function untuk memeriksa apakah tanggal tertentu sudah diperiksa oleh user
                function wasCheckedByUser($results, $date) {
                    // Filter hasil berdasarkan tanggal
                    $result = $results->where('tanggal', $date)->first();
                    
                    // Return true jika result ditemukan dan memiliki checked_by yang tidak kosong
                    return $result && !empty($result['checked_by']);
                }

                // Helper function untuk mendapatkan nama checker berdasarkan tanggal
                function getCheckerName($results, $date) {
                    // Filter hasil berdasarkan tanggal
                    $result = $results->where('tanggal', $date)->first();
                    
                    // Jika nama checker ditemukan, kembalikan nilainya, jika tidak kembalikan string kosong
                    return $result && isset($result['checked_by']) ? $result['checked_by'] : '';
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
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-40" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 1; $i <= 11; $i++)
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
                                    
                                    @for($j = 1; $j <= 11; $j++)
                                        <td class="border border-gray-300 p-1 h-10">
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}" {{ getCheckResult($results, $j, $i) == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                value="{{ getKeterangan($results, $j, $i) }}"
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan">
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
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ getCheckerName($results, $j) }}' }">
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ Auth::user()->username }}'; 
                                                        $refs.user{{ $j }}.value = userName;
                                                        $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                    } else {
                                                        userName = '';
                                                        $refs.user{{ $j }}.value = '';
                                                        $refs.checkNum{{ $j }}.value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                            <div class="mt-1" x-show="selected">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                        </div>
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
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-40" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 12; $i <= 22; $i++)
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
                                    
                                    @for($j = 12; $j <= 22; $j++)
                                        <td class="border border-gray-300 p-1 h-10">
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}" {{ getCheckResult($results, $j, $i) == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                value="{{ getKeterangan($results, $j, $i) }}"
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan">
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
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ getCheckerName($results, $j) }}' }">
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ Auth::user()->username }}'; 
                                                        $refs.user{{ $j }}.value = userName;
                                                        $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                    } else {
                                                        userName = '';
                                                        $refs.user{{ $j }}.value = '';
                                                        $refs.checkNum{{ $j }}.value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                            <div class="mt-1" x-show="selected">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            
                <!-- Tabel untuk tanggal 23-31 -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                                
                                @for ($i = 23; $i <= 31; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24" colspan="1">{{ $num }}</th>
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-40" rowspan="2">Keterangan</th>
                                @endfor
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                @for ($i = 23; $i <= 31; $i++)
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
                                    
                                    @for($j = 23; $j <= 31; $j++)
                                        <td class="border border-gray-300 p-1 h-10">
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}" {{ getCheckResult($results, $j, $i) == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                value="{{ getKeterangan($results, $j, $i) }}"
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan">
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
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ getCheckerName($results, $j) }}' }">
                                            <button type="button" 
                                                @click="selected = !selected; 
                                                    if(selected) {
                                                        userName = '{{ Auth::user()->username }}'; 
                                                        $refs.user{{ $j }}.value = userName;
                                                        $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                    } else {
                                                        userName = '';
                                                        $refs.user{{ $j }}.value = '';
                                                        $refs.checkNum{{ $j }}.value = '';
                                                    }"
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                            <div class="mt-1" x-show="selected">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

            @include('components.form-buttons', ['backRoute' => route('autoloader.index')])
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById("tanggal")) {
            document.getElementById("tanggal").addEventListener("change", function() {
                let tanggal = new Date(this.value);
                let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
                document.getElementById("hari").value = hari;
            });
        }
    });
</script>
@endsection