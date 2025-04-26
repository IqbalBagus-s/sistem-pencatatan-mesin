<!-- resources/views/dehum-matras/edit.blade.php -->
@extends('layouts.create-layout-2')

@section('title', 'Edit Pencatatan Mesin Dehum Matras')

@section('content')
<h2 class="mb-4 text-xl font-bold">Edit Pencatatan Mesin Dehum Matras</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
        </div>

        <!-- Form Input -->
        <form action="{{ route('dehum-matras.update', $check->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Info Display (Not Editable) -->
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- No Dehum Matras Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Dehum Matras:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        Dehum Matras {{ $check->nomer_dehum_matras }}
                    </div>
                    <input type="hidden" name="nomer_dehum_matras" value="{{ $check->nomer_dehum_matras }}">
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
        
            // Placeholder text untuk setiap item
            $placeholders = [
                1 => '50° C - 70° C',
                2 => '35° C - 45° C',
                3 => '35° C - 50° C',
                4 => '40° C - 55° C',
                5 => '31° C - 33° C',
                6 => '32° C - 36° C',
                7 => '18° C - 28° C',
            ];
            
            // Helper function untuk mendapatkan hasil check berdasarkan tanggal dan item
            function getCheckResult($results, $date, $itemId) {
                // Filter hasil berdasarkan tanggal dan item_id
                $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                
                // Jika hasil ditemukan, kembalikan nilai result, jika tidak kembalikan null
                return $result && isset($result['result']) ? $result['result'] : '';
            }
        
            // Helper function untuk mendapatkan nama checker berdasarkan tanggal
            function getCheckerName($results, $date) {
                // Filter hasil berdasarkan tanggal
                $result = $results->where('tanggal', $date)->first();
                
                // Jika nama checker ditemukan, kembalikan nilainya, jika tidak kembalikan string kosong
                return $result && isset($result['checked_by']) ? $result['checked_by'] : '';
            }
        
            // Helper function untuk memeriksa apakah tanggal tertentu sudah diperiksa oleh user
            function wasCheckedByUser($results, $date) {
                // Filter hasil berdasarkan tanggal
                $result = $results->where('tanggal', $date)->first();
                
                // Return true jika result ditemukan dan memiliki checked_by yang tidak kosong
                return $result && !empty($result['checked_by']);
            }
        
            // Helper function untuk mendapatkan nama penanggung jawab berdasarkan tanggal
            function getApprovedBy($results, $date) {
                // Filter hasil berdasarkan tanggal
                $result = $results->where('tanggal', $date)->first();
                
                // Jika nama penanggung jawab ditemukan, kembalikan nilainya, jika tidak kembalikan "-"
                return $result && isset($result['approved_by']) && !empty($result['approved_by']) ? $result['approved_by'] : '-';
            }
        
            // Helper function untuk memeriksa apakah kolom harus readonly berdasarkan penanggung jawab
            function isReadOnly($results, $date) {
                $approvedBy = getApprovedBy($results, $date);
                return $approvedBy !== '-' && !empty($approvedBy);
            }
            @endphp
            
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <!-- Tabel untuk tanggal 1-11 -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                
                                @for ($i = 1; $i <= 11; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24">{{ $num }}</th>
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
                                        @php $isReadOnly = isReadOnly($results, $j); @endphp
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                value="{{ getCheckResult($results, $j, $i) }}"
                                                placeholder="{{ $placeholders[$i] }}"
                                                class="w-full h-8 px-2 py-0 text-xs {{ $isReadOnly ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400"
                                                {{ $isReadOnly ? 'readonly' : '' }}>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris dibuat oleh --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    @php $isReadOnly = isReadOnly($results, $j); @endphp
                                    <td class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ getCheckerName($results, $j) }}', isExistingData: {{ getCheckerName($results, $j) ? 'true' : 'false' }}, isReadOnly: {{ $isReadOnly ? 'true' : 'false' }} }">
                                            <!-- Show just the name if data already exists -->
                                            <div x-show="isExistingData" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                                {{ getCheckerName($results, $j) }}
                                                <input type="hidden" name="checked_by_{{ $j }}" value="{{ getCheckerName($results, $j) }}">
                                                <input type="hidden" name="check_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show the form if selected but not existing data -->
                                            <div x-show="selected && !isExistingData && !isReadOnly" class="w-full">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show message if it's read-only -->
                                            <div x-show="!isExistingData && isReadOnly" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center text-gray-500">
                                                Sudah dikunci
                                            </div>
                                            
                                            <!-- Tombol Pilih/Batal Pilih selalu di bawah -->
                                            <div x-show="!isReadOnly" class="mt-1">
                                                <button type="button" 
                                                    @click="
                                                        if(isExistingData) {
                                                            isExistingData = false;
                                                            selected = false;
                                                        } else {
                                                            selected = !selected;
                                                            if(selected) {
                                                                userName = '{{ Auth::user()->username }}'; 
                                                                $refs.user{{ $j }}.value = userName;
                                                                $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                            } else {
                                                                userName = '';
                                                                $refs.user{{ $j }}.value = '';
                                                                $refs.checkNum{{ $j }}.value = '';
                                                            }
                                                        }
                                                    "
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                    :class="selected || isExistingData ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                    <span x-text="selected || isExistingData ? 'Batal Pilih' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
                                        <div class="w-full px-2 py-1 text-sm">
                                            <input type="text" name="approved_by_{{ $j }}" value="{{ getApprovedBy($results, $j) }}"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="approve_num_{{ $j }}" value="{{ $j }}">
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
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                
                                @for ($i = 12; $i <= 22; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24">{{ $num }}</th>
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
                                        @php $isReadOnly = isReadOnly($results, $j); @endphp
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                value="{{ getCheckResult($results, $j, $i) }}"
                                                placeholder="{{ $placeholders[$i] }}"
                                                class="w-full h-8 px-2 py-0 text-xs {{ $isReadOnly ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400"
                                                {{ $isReadOnly ? 'readonly' : '' }}>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris dibuat oleh --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 12; $j <= 22; $j++)
                                    @php $isReadOnly = isReadOnly($results, $j); @endphp
                                    <td class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ getCheckerName($results, $j) }}', isExistingData: {{ getCheckerName($results, $j) ? 'true' : 'false' }}, isReadOnly: {{ $isReadOnly ? 'true' : 'false' }} }">
                                            <!-- Show just the name if data already exists -->
                                            <div x-show="isExistingData" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                                {{ getCheckerName($results, $j) }}
                                                <input type="hidden" name="checked_by_{{ $j }}" value="{{ getCheckerName($results, $j) }}">
                                                <input type="hidden" name="check_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show the form if selected but not existing data -->
                                            <div x-show="selected && !isExistingData && !isReadOnly" class="w-full">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show message if it's read-only -->
                                            <div x-show="!isExistingData && isReadOnly" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center text-gray-500">
                                                Sudah dikunci
                                            </div>
                                            
                                            <!-- Tombol Pilih/Batal Pilih selalu di bawah -->
                                            <div x-show="!isReadOnly" class="mt-1">
                                                <button type="button" 
                                                    @click="
                                                        if(isExistingData) {
                                                            isExistingData = false;
                                                            selected = false;
                                                        } else {
                                                            selected = !selected;
                                                            if(selected) {
                                                                userName = '{{ Auth::user()->username }}'; 
                                                                $refs.user{{ $j }}.value = userName;
                                                                $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                            } else {
                                                                userName = '';
                                                                $refs.user{{ $j }}.value = '';
                                                                $refs.checkNum{{ $j }}.value = '';
                                                            }
                                                        }
                                                    "
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                    :class="selected || isExistingData ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                    <span x-text="selected || isExistingData ? 'Batal Pilih' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 12; $j <= 22; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
                                        <div class="w-full px-2 py-1 text-sm">
                                            <input type="text" name="approved_by_{{ $j }}" value="{{ getApprovedBy($results, $j) }}"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="approve_num_{{ $j }}" value="{{ $j }}">
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
                                <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                                
                                @for ($i = 23; $i <= 31; $i++)
                                    @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                    <th class="border border-gray-300 bg-sky-50 p-2 w-24">{{ $num }}</th>
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
                                        @php $isReadOnly = isReadOnly($results, $j); @endphp
                                        <td class="border border-gray-300 p-1 h-10">
                                            <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                value="{{ getCheckResult($results, $j, $i) }}"
                                                placeholder="{{ $placeholders[$i] }}"
                                                class="w-full h-8 px-2 py-0 text-xs {{ $isReadOnly ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400"
                                                {{ $isReadOnly ? 'readonly' : '' }}>
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris dibuat oleh --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                                
                                @for($j = 23; $j <= 31; $j++)
                                    @php $isReadOnly = isReadOnly($results, $j); @endphp
                                    <td class="border border-gray-300 p-1 bg-sky-50">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ getCheckerName($results, $j) }}', isExistingData: {{ getCheckerName($results, $j) ? 'true' : 'false' }}, isReadOnly: {{ $isReadOnly ? 'true' : 'false' }} }">
                                            <!-- Show just the name if data already exists -->
                                            <div x-show="isExistingData" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                                {{ getCheckerName($results, $j) }}
                                                <input type="hidden" name="checked_by_{{ $j }}" value="{{ getCheckerName($results, $j) }}">
                                                <input type="hidden" name="check_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show the form if selected but not existing data -->
                                            <div x-show="selected && !isExistingData && !isReadOnly" class="w-full">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show message if it's read-only -->
                                            <div x-show="!isExistingData && isReadOnly" class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center text-gray-500">
                                                Sudah dikunci
                                            </div>
                                            
                                            <!-- Tombol Pilih/Batal Pilih selalu di bawah -->
                                            <div x-show="!isReadOnly" class="mt-1">
                                                <button type="button" 
                                                    @click="
                                                        if(isExistingData) {
                                                            isExistingData = false;
                                                            selected = false;
                                                        } else {
                                                            selected = !selected;
                                                            if(selected) {
                                                                userName = '{{ Auth::user()->username }}'; 
                                                                $refs.user{{ $j }}.value = userName;
                                                                $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                            } else {
                                                                userName = '';
                                                                $refs.user{{ $j }}.value = '';
                                                                $refs.checkNum{{ $j }}.value = '';
                                                            }
                                                        }
                                                    "
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                    :class="selected || isExistingData ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                    <span x-text="selected || isExistingData ? 'Batal Pilih' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab --}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 23; $j <= 31; $j++)
                                    <td class="border border-gray-300 p-1 bg-green-50">
                                        <div class="w-full px-2 py-1 text-sm">
                                            <input type="text" name="approved_by_{{ $j }}" value="{{ getApprovedBy($results, $j) }}"
                                                class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="approve_num_{{ $j }}" value="{{ $j }}">
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                </div>
                @include('components.edit-form-buttons', ['backRoute' => route('dehum-matras.index')])
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

            
            