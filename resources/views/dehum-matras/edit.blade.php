<!-- resources/views/dehum-matras/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Pencatatan Mesin Dehum Matras')
@section('page-title', 'Edit Pencatatan Mesin Dehum Matras')
@section('show-checker')
    <div></div>
@endsection

@section('content')
    <!-- Form Input -->
    <form action="{{ route('dehum-matras.update', $check->id) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')
            <!-- Info Display (Not Editable) -->
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- No Dehum Matras Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Dehum Matras:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Dehum Matras {{ $check->nomer_dehum_matras }}
                    </div>
                    <input type="hidden" name="nomer_dehum_matras" value="{{ $check->nomer_dehum_matras }}">
                </div>
                
                <!-- Shift Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Shift:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Shift {{ $check->shift }}
                    </div>
                    <input type="hidden" name="shift" value="{{ $check->shift }}">
                </div>

                <!-- Bulan Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($check->bulan)->translatedFormat('F Y') }}
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

            // Helper function untuk memberikan kelas CSS berdasarkan status approval
            function getRowClass($results, $date) {
                $approvedBy = getApprovedBy($results, $date);
                return $approvedBy !== '-' && !empty($approvedBy) ? 'bg-green-50' : '';
            }

            // Helper function untuk memberikan kelas CSS pada input field berdasarkan status approval
            function getInputClass($results, $date) {
                $approvedBy = getApprovedBy($results, $date);
                return $approvedBy !== '-' && !empty($approvedBy) ? 'bg-green-100' : 'bg-white';
            }
            @endphp
            
            <!-- Tabel Inspeksi -->
            <div class="mb-1">
                <!-- Tabel untuk tanggal 1-11 -->
                <!-- Notifikasi scroll horizontal untuk mobile -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6">
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
                                        @php 
                                        $isReadOnly = isReadOnly($results, $j); 
                                        $rowClass = getRowClass($results, $j);
                                        $inputClass = getInputClass($results, $j);
                                        @endphp
                                        <td class="border border-gray-300 p-1 h-10 {{ $rowClass }}">
                                            <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                value="{{ getCheckResult($results, $j, $i) }}"
                                                placeholder="{{ $placeholders[$i] }}"
                                                class="w-full h-8 px-2 py-0 text-xs {{ $inputClass }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400"
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
                                    @php 
                                    $isReadOnly = isReadOnly($results, $j); 
                                    $rowClass = $isReadOnly ? 'bg-green-50' : 'bg-sky-50';
                                    $checkerName = getCheckerName($results, $j);
                                    $hasCheckerData = !empty($checkerName);
                                    @endphp
                                    <td class="border border-gray-300 p-1 {{ $rowClass }}">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ $checkerName }}', isExistingData: {{ $hasCheckerData ? 'true' : 'false' }}, isReadOnly: {{ $isReadOnly ? 'true' : 'false' }} }">
                                            <!-- Show just the name if data already exists -->
                                            <div x-show="isExistingData" class="w-full px-2 py-1 text-sm {{ $isReadOnly ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center">
                                                {{ $checkerName }}
                                                <input type="hidden" name="checked_by_{{ $j }}" value="{{ $checkerName }}">
                                                <input type="hidden" name="check_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show the form if selected but not existing data -->
                                            <div x-show="selected && !isExistingData && !isReadOnly" class="w-full">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show message if it's read-only -->
                                            <div x-show="!isExistingData && isReadOnly" class="w-full px-2 py-1 text-sm bg-green-100 border border-gray-300 rounded text-center text-gray-600">
                                                Sudah dikunci
                                            </div>
                                            
                                            <!-- Tampilkan informasi penanggung jawab jika sudah diapprove -->
                                            @if($isReadOnly)
                                                <div class="mt-1 text-xs text-green-600 text-center">
                                                    Disetujui oleh: {{ getApprovedBy($results, $j) }}
                                                </div>
                                                <input type="hidden" name="approved_by_{{ $j }}" value="{{ getApprovedBy($results, $j) }}">
                                            @endif
                                            
                                            <!-- Tombol Pilih/Batal Pilih hanya ditampilkan jika belum readonly dan belum ada data -->
                                            <div x-show="!isReadOnly && !isExistingData" class="mt-1">
                                                <button type="button" 
                                                    @click="
                                                        selected = !selected;
                                                        if(selected) {
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.user{{ $j }}.value = userName;
                                                            $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                        } else {
                                                            userName = '';
                                                            $refs.user{{ $j }}.value = '';
                                                            $refs.checkNum{{ $j }}.value = '';
                                                        }
                                                    "
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                    :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                    <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tabel untuk tanggal 12-22 -->
                <!-- Notifikasi scroll horizontal untuk mobile -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6">
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
                                        @php 
                                        $isReadOnly = isReadOnly($results, $j); 
                                        $rowClass = getRowClass($results, $j);
                                        $inputClass = getInputClass($results, $j);
                                        @endphp
                                        <td class="border border-gray-300 p-1 h-10 {{ $rowClass }}">
                                            <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                value="{{ getCheckResult($results, $j, $i) }}"
                                                placeholder="{{ $placeholders[$i] }}"
                                                class="w-full h-8 px-2 py-0 text-xs {{ $inputClass }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400"
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
                                    @php 
                                    $isReadOnly = isReadOnly($results, $j); 
                                    $rowClass = $isReadOnly ? 'bg-green-50' : 'bg-sky-50';
                                    $checkerName = getCheckerName($results, $j);
                                    $hasCheckerData = !empty($checkerName);
                                    @endphp
                                    <td class="border border-gray-300 p-1 {{ $rowClass }}">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ $checkerName }}', isExistingData: {{ $hasCheckerData ? 'true' : 'false' }}, isReadOnly: {{ $isReadOnly ? 'true' : 'false' }} }">
                                            <!-- Show just the name if data already exists -->
                                            <div x-show="isExistingData" class="w-full px-2 py-1 text-sm {{ $isReadOnly ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center">
                                                {{ $checkerName }}
                                                <input type="hidden" name="checked_by_{{ $j }}" value="{{ $checkerName }}">
                                                <input type="hidden" name="check_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show the form if selected but not existing data -->
                                            <div x-show="selected && !isExistingData && !isReadOnly" class="w-full">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show message if it's read-only -->
                                            <div x-show="!isExistingData && isReadOnly" class="w-full px-2 py-1 text-sm bg-green-100 border border-gray-300 rounded text-center text-gray-600">
                                                Sudah dikunci
                                            </div>
                                            
                                            <!-- Tampilkan informasi penanggung jawab jika sudah diapprove -->
                                            @if($isReadOnly)
                                                <div class="mt-1 text-xs text-green-600 text-center">
                                                    Disetujui oleh: {{ getApprovedBy($results, $j) }}
                                                </div>
                                                <input type="hidden" name="approved_by_{{ $j }}" value="{{ getApprovedBy($results, $j) }}">
                                            @endif
                                            
                                            <!-- Tombol Pilih/Batal Pilih hanya ditampilkan jika belum readonly dan belum ada data -->
                                            <div x-show="!isReadOnly && !isExistingData" class="mt-1">
                                                <button type="button" 
                                                    @click="
                                                        selected = !selected;
                                                        if(selected) {
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.user{{ $j }}.value = userName;
                                                            $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                        } else {
                                                            userName = '';
                                                            $refs.user{{ $j }}.value = '';
                                                            $refs.checkNum{{ $j }}.value = '';
                                                        }
                                                    "
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                    :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                    <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Tabel untuk tanggal 23-31 -->
                <!-- Notifikasi scroll horizontal untuk mobile -->
                <div class="md:hidden text-sm text-gray-500 italic mb-2">
                    ← Geser ke kanan untuk melihat semua kolom →
                </div>
                <div class="overflow-x-auto mb-6">
                    <table class="w-full border-collapse table-fixed" style="width: max-content;">
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
                                        @php 
                                        $isReadOnly = isReadOnly($results, $j); 
                                        $rowClass = getRowClass($results, $j);
                                        $inputClass = getInputClass($results, $j);
                                        @endphp
                                        <td class="border border-gray-300 p-1 h-10 {{ $rowClass }}">
                                            <input type="text" name="check_{{ $j }}[{{ $i }}]" 
                                                value="{{ getCheckResult($results, $j, $i) }}"
                                                placeholder="{{ $placeholders[$i] }}"
                                                class="w-full h-8 px-2 py-0 text-xs {{ $inputClass }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white placeholder:text-xs placeholder:text-gray-400"
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
                                    @php 
                                    $isReadOnly = isReadOnly($results, $j); 
                                    $rowClass = $isReadOnly ? 'bg-green-50' : 'bg-sky-50';
                                    $checkerName = getCheckerName($results, $j);
                                    $hasCheckerData = !empty($checkerName);
                                    @endphp
                                    <td class="border border-gray-300 p-1 {{ $rowClass }}">
                                        <div x-data="{ selected: {{ wasCheckedByUser($results, $j) ? 'true' : 'false' }}, userName: '{{ $checkerName }}', isExistingData: {{ $hasCheckerData ? 'true' : 'false' }}, isReadOnly: {{ $isReadOnly ? 'true' : 'false' }} }">
                                            <!-- Show just the name if data already exists -->
                                            <div x-show="isExistingData" class="w-full px-2 py-1 text-sm {{ $isReadOnly ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center">
                                                {{ $checkerName }}
                                                <input type="hidden" name="checked_by_{{ $j }}" value="{{ $checkerName }}">
                                                <input type="hidden" name="check_num_{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show the form if selected but not existing data -->
                                            <div x-show="selected && !isExistingData && !isReadOnly" class="w-full">
                                                <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" x-bind:value="userName"
                                                    class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center"
                                                    readonly>
                                                <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $j }}">
                                            </div>
                                            
                                            <!-- Show message if it's read-only -->
                                            <div x-show="!isExistingData && isReadOnly" class="w-full px-2 py-1 text-sm bg-green-100 border border-gray-300 rounded text-center text-gray-600">
                                                Sudah dikunci
                                            </div>
                                            
                                            <!-- Tampilkan informasi penanggung jawab jika sudah diapprove -->
                                            @if($isReadOnly)
                                                <div class="mt-1 text-xs text-green-600 text-center">
                                                    Disetujui oleh: {{ getApprovedBy($results, $j) }}
                                                </div>
                                                <input type="hidden" name="approved_by_{{ $j }}" value="{{ getApprovedBy($results, $j) }}">
                                            @endif
                                            
                                            <!-- Tombol Pilih/Batal Pilih hanya ditampilkan jika belum readonly dan belum ada data -->
                                            <div x-show="!isReadOnly && !isExistingData" class="mt-1">
                                                <button type="button" 
                                                    @click="
                                                        selected = !selected;
                                                        if(selected) {
                                                            userName = '{{ $user->username }}'; 
                                                            $refs.user{{ $j }}.value = userName;
                                                            $refs.checkNum{{ $j }}.value = '{{ $j }}';
                                                        } else {
                                                            userName = '';
                                                            $refs.user{{ $j }}.value = '';
                                                            $refs.checkNum{{ $j }}.value = '';
                                                        }
                                                    "
                                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                    :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                    <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                    </table>
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
                        <ul class="space-y-3 text-gray-700 text-sm leading-relaxed">
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Kompressor:</strong> 50°C - 70°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Kabel:</strong> 35°C - 45°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>NFB:</strong> 35°C - 50°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Motor:</strong> 40°C - 55°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Water Cooler in:</strong> 31°C - 33°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Water Cooler Out:</strong> 32°C - 36°C</span>
                            </li>
                            <li class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><strong>Temperatur Output Udara:</strong> 18°C - 28°C</span>
                            </li>
                        </ul>
                    </div>
                </div>
                @include('components.edit-form-buttons', ['backRoute' => route('dehum-matras.index')])
            </div>
    </form>
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

            
            