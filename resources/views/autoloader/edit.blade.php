<!-- resources/views/autoloader/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Pencatatan Mesin Autoloader')
@section('page-title', 'Edit Pencatatan Mesin Autoloader')
@section('show-checker', true)

@section('content')
<!-- Form Input -->
<form action="{{ route('autoloader.update', $check->id) }}" method="POST" autocomplete="off">
    @csrf
    @method('PUT')
    <!-- Info Display (Not Editable) -->
    <div class="grid md:grid-cols-3 gap-4 mb-4">
        <!-- No Autoloader Display -->
        <div class="w-full">
            <label class="block mb-2 text-sm font-medium text-gray-700">No Autoloader:</label>
            <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                Autoloader {{ $check->nomer_autoloader }}
            </div>
            <input type="hidden" name="nomer_autoloader" value="{{ $check->nomer_autoloader }}">
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
            'V' => 'V',
            'X' => 'X',
            '-' => '-',
            'OFF' => 'OFF'
        ];
        
        // Helper function untuk mendapatkan hasil check berdasarkan tanggal dan item
        function getCheckResult($results, $date, $itemId) {
            // Filter hasil berdasarkan tanggal dan item_id
            $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
            
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
            
            // Return true jika result ditemukan dan memiliki checker_id yang tidak kosong
            return $result && !empty($result['checker_id']);
        }
    
        // Helper function untuk mendapatkan nama checker berdasarkan tanggal
        function getCheckerName($results, $date) {
            // Filter hasil berdasarkan tanggal
            $result = $results->where('tanggal', $date)->first();
            
            // Jika nama checker ditemukan, kembalikan nilainya, jika tidak kembalikan string kosong
            return $result && isset($result['checker_id']) ? $result['checker_id'] : '';
        }
    
        // Helper function untuk mendapatkan nama penanggung jawab berdasarkan tanggal
        function getApprovedBy($results, $date) {
            // Filter hasil berdasarkan tanggal
            $result = $results->where('tanggal', $date)->first();
            
            // Jika nama penanggung jawab ditemukan, kembalikan nilainya, jika tidak kembalikan "-"
            return $result && isset($result['approver_id']) && !empty($result['approver_id']) ? $result['approver_id'] : '-';
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
        <!-- Notifikasi scroll horizontal untuk mobile -->
        <div class="md:hidden text-sm text-gray-500 italic mb-2">
            ← Geser ke kanan untuk melihat semua kolom →
        </div>
        <div class="overflow-x-auto mb-6 border border-gray-300">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                        <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                        
                        @for ($i = 1; $i <= 11; $i++)
                            @php 
                                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $isApproved = isReadOnly($results, $i);
                            @endphp
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-24" colspan="1">{{ $num }}</th>
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-40" rowspan="2">Keterangan</th>
                        @endfor
                    </tr>
                    <tr>
                        <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                        @for ($i = 1; $i <= 11; $i++)
                            @php $isApproved = isReadOnly($results, $i); @endphp
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 min-w-20">Cek</th>
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
                                @php $isApproved = isReadOnly($results, $j); @endphp
                                <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                    @if($isApproved)
                                        <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center justify-center">
                                            {!! $options[getCheckResult($results, $j, $i)] ?? '—' !!}
                                        </div>
                                        <input type="hidden" name="check_{{ $j }}[{{ $i }}]" value="{{ getCheckResult($results, $j, $i) }}">
                                    @else
                                        <select name="check_{{ $j }}[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}" {{ getCheckResult($results, $j, $i) == $value ? 'selected' : '' }}>{!! $symbol !!}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                    @if($isApproved)
                                        <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center">
                                            {{ getKeterangan($results, $j, $i) }}
                                        </div>
                                        <input type="hidden" name="keterangan_{{ $j }}[{{ $i }}]" value="{{ getKeterangan($results, $j, $i) }}">
                                    @else
                                        <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                            value="{{ getKeterangan($results, $j, $i) }}"
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
                {{-- baris dibuat oleh --}}
                <tbody class="bg-white">
                    <tr class="bg-sky-50">
                        <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                        <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                        
                        <!-- Modifikasi pada bagian "Dibuat Oleh" -->
                        @for($j = 1; $j <= 11; $j++)
                            @php 
                                $isApproved = isReadOnly($results, $j);
                                $checkedBy = getCheckerName($results, $j);
                                $isChecked = !empty($checkedBy);
                            @endphp
                            <td colspan="2" class="border border-gray-300 p-1 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }}">
                                <div x-data="{ 
                                    selected: {{ $isChecked ? 'true' : 'false' }}, 
                                    userName: '{{ $checkedBy }}',
                                    isApproved: {{ $isApproved ? 'true' : 'false' }},
                                    existingData: {{ $isChecked ? 'true' : 'false' }}
                                }">
                                    <!-- Tampilkan nama pengguna jika telah dipilih atau sudah disetujui -->
                                    <div class="w-full px-2 py-1 mt-1 text-sm {{ $isApproved ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center"
                                        x-show="selected || isApproved">
                                        <span x-text="userName"></span>
                                        <input type="hidden" name="checked_by_{{ $j }}" :value="userName">
                                        <input type="hidden" name="check_num_{{ $j }}" :value="selected ? '{{ $j }}' : ''">
                                    </div>
                                    
                                    @if($isApproved)
                                        <div class="mt-1 text-xs text-green-600 text-center">
                                            Disetujui oleh: {{ getApprovedBy($results, $j) }}
                                        </div>
                                    @endif
                                    
                                    <!-- Tombol Pilih/Batal Pilih hanya jika belum diapprove dan tombol Batal hanya jika belum ada data -->
                                    @if(!$isApproved)
                                        <div class="mt-1">
                                            <button type="button" 
                                                x-show="!selected || (selected && !existingData)"
                                                @click="
                                                    selected = !selected;
                                                    if(selected) {
                                                        userName = '{{ $user->username }}'; 
                                                    } else {
                                                        userName = '';
                                                    }
                                                "
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                        </div>
                                    @endif
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
        <div class="overflow-x-auto mb-6 border border-gray-300">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                        <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                        
                        @for ($i = 12; $i <= 22; $i++)
                            @php 
                                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $isApproved = isReadOnly($results, $i);
                            @endphp
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-24" colspan="1">{{ $num }}</th>
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-40" rowspan="2">Keterangan</th>
                        @endfor
                    </tr>
                    <tr>
                        <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                        @for ($i = 12; $i <= 22; $i++)
                            @php $isApproved = isReadOnly($results, $i); @endphp
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 min-w-20">Cek</th>
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
                                @php $isApproved = isReadOnly($results, $j); @endphp
                                <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                    @if($isApproved)
                                        <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center justify-center">
                                            {!! $options[getCheckResult($results, $j, $i)] ?? '—' !!}
                                        </div>
                                        <input type="hidden" name="check_{{ $j }}[{{ $i }}]" value="{{ getCheckResult($results, $j, $i) }}">
                                    @else
                                        <select name="check_{{ $j }}[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}" {{ getCheckResult($results, $j, $i) == $value ? 'selected' : '' }}>{!! $symbol !!}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                    @if($isApproved)
                                        <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center">
                                            {{ getKeterangan($results, $j, $i) }}
                                        </div>
                                        <input type="hidden" name="keterangan_{{ $j }}[{{ $i }}]" value="{{ getKeterangan($results, $j, $i) }}">
                                    @else
                                        <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                            value="{{ getKeterangan($results, $j, $i) }}"
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
                {{-- baris dibuat oleh --}}
                <tbody class="bg-white">
                    <tr class="bg-sky-50">
                        <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                        <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                        
                        <!-- Modifikasi pada bagian "Dibuat Oleh" -->
                        @for($j = 12; $j <= 22; $j++)
                            @php 
                                $isApproved = isReadOnly($results, $j);
                                $checkedBy = getCheckerName($results, $j);
                                $isChecked = !empty($checkedBy);
                            @endphp
                            <td colspan="2" class="border border-gray-300 p-1 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }}">
                                <div x-data="{ 
                                    selected: {{ $isChecked ? 'true' : 'false' }}, 
                                    userName: '{{ $checkedBy }}',
                                    isApproved: {{ $isApproved ? 'true' : 'false' }},
                                    existingData: {{ $isChecked ? 'true' : 'false' }}
                                }">
                                    <!-- Tampilkan nama pengguna jika telah dipilih atau sudah disetujui -->
                                    <div class="w-full px-2 py-1 mt-1 text-sm {{ $isApproved ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center"
                                        x-show="selected || isApproved">
                                        <span x-text="userName"></span>
                                        <input type="hidden" name="checked_by_{{ $j }}" :value="userName">
                                        <input type="hidden" name="check_num_{{ $j }}" :value="selected ? '{{ $j }}' : ''">
                                    </div>
                                    
                                    @if($isApproved)
                                        <div class="mt-1 text-xs text-green-600 text-center">
                                            Disetujui oleh: {{ getApprovedBy($results, $j) }}
                                        </div>
                                    @endif
                                    
                                    <!-- Tombol Pilih/Batal Pilih hanya jika belum diapprove dan tombol Batal hanya jika belum ada data -->
                                    @if(!$isApproved)
                                        <div class="mt-1">
                                            <button type="button" 
                                                x-show="!selected || (selected && !existingData)"
                                                @click="
                                                    selected = !selected;
                                                    if(selected) {
                                                        userName = '{{ $user->username }}'; 
                                                    } else {
                                                        userName = '';
                                                    }
                                                "
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                        </div>
                                    @endif
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
        <div class="overflow-x-auto mb-6 border border-gray-300">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border border-gray-300 bg-sky-50 p-2 w-10 sticky left-0 z-10" rowspan="2">No.</th>
                        <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10" colspan="1">Tanggal</th>
                        
                        @for ($i = 23; $i <= 31; $i++)
                            @php 
                                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                                $isApproved = isReadOnly($results, $i);
                            @endphp
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-24" colspan="1">{{ $num }}</th>
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-40" rowspan="2">Keterangan</th>
                        @endfor
                    </tr>
                    <tr>
                        <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                        @for ($i = 23; $i <= 31; $i++)
                            @php $isApproved = isReadOnly($results, $i); @endphp
                            <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 min-w-20">Cek</th>
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
                                @php $isApproved = isReadOnly($results, $j); @endphp
                                <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                    @if($isApproved)
                                        <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center justify-center">
                                            {!! $options[getCheckResult($results, $j, $i)] ?? '—' !!}
                                        </div>
                                        <input type="hidden" name="check_{{ $j }}[{{ $i }}]" value="{{ getCheckResult($results, $j, $i) }}">
                                    @else
                                        <select name="check_{{ $j }}[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}" {{ getCheckResult($results, $j, $i) == $value ? 'selected' : '' }}>{!! $symbol !!}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </td>
                                <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                    @if($isApproved)
                                        <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center">
                                            {{ getKeterangan($results, $j, $i) }}
                                        </div>
                                        <input type="hidden" name="keterangan_{{ $j }}[{{ $i }}]" value="{{ getKeterangan($results, $j, $i) }}">
                                    @else
                                        <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                            value="{{ getKeterangan($results, $j, $i) }}"
                                            class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan">
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
                {{-- baris dibuat oleh --}}
                <tbody class="bg-white">
                    <tr class="bg-sky-50">
                        <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                        <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                        
                        <!-- Modifikasi pada bagian "Dibuat Oleh" -->
                        @for($j = 23; $j <= 31; $j++)
                            @php 
                                $isApproved = isReadOnly($results, $j);
                                $checkedBy = getCheckerName($results, $j);
                                $isChecked = !empty($checkedBy);
                            @endphp
                            <td colspan="2" class="border border-gray-300 p-1 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }}">
                                <div x-data="{ 
                                    selected: {{ $isChecked ? 'true' : 'false' }}, 
                                    userName: '{{ $checkedBy }}',
                                    isApproved: {{ $isApproved ? 'true' : 'false' }},
                                    existingData: {{ $isChecked ? 'true' : 'false' }}
                                }">
                                    <!-- Tampilkan nama pengguna jika telah dipilih atau sudah disetujui -->
                                    <div class="w-full px-2 py-1 mt-1 text-sm {{ $isApproved ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center"
                                        x-show="selected || isApproved">
                                        <span x-text="userName"></span>
                                        <input type="hidden" name="checked_by_{{ $j }}" :value="userName">
                                        <input type="hidden" name="check_num_{{ $j }}" :value="selected ? '{{ $j }}' : ''">
                                    </div>
                                    
                                    @if($isApproved)
                                        <div class="mt-1 text-xs text-green-600 text-center">
                                            Disetujui oleh: {{ getApprovedBy($results, $j) }}
                                        </div>
                                    @endif
                                    
                                    <!-- Tombol Pilih/Batal Pilih hanya jika belum diapprove dan tombol Batal hanya jika belum ada data -->
                                    @if(!$isApproved)
                                        <div class="mt-1">
                                            <button type="button" 
                                                x-show="!selected || (selected && !existingData)"
                                                @click="
                                                    selected = !selected;
                                                    if(selected) {
                                                        userName = '{{ $user->username }}'; 
                                                    } else {
                                                        userName = '';
                                                    }
                                                "
                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        @endfor
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    {{-- catatan pemeriksaan --}}
    <div class="bg-gradient-to-r from-sky-50 to-blue-50 p-6 rounded-xl shadow-md mb-8 border-l-4 border-blue-500">
        <h5 class="text-xl font-bold text-blue-700 mb-5 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Catatan Pemeriksaan
        </h5>
        
        <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0 items-center justify-center">
            <!-- Kriteria Pemeriksaan -->
            <div class="bg-white p-6 rounded-lg border border-blue-200 shadow-sm w-full lg:w-2/3">
                <h6 class="text-lg font-semibold text-blue-600 mb-4">Standar Kriteria Pemeriksaan:</h6>
                <ul class="space-y-4 text-gray-800 text-sm">
                    @foreach ([
                        ['Filter', 'Kebersihan'],
                        ['Selang', 'Tidak bocor'],
                        ['Panel Kelistrikan', 'Berfungsi'],
                        ['Kontraktor', 'Baik'],
                        ['Temperatur Kontrol', 'Baik'],
                        ['MCB', 'Baik']
                    ] as [$title, $desc])
                        <li class="flex items-start">
                            <svg class="h-5 w-5 mr-2 text-green-500 mt-1 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span><strong>{{ $title }}:</strong> {{ $desc }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <!-- Keterangan Status -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-blue-200 w-full lg:w-1/3">
                <p class="text-lg font-semibold text-blue-800 mb-4">Keterangan Status:</p>
                <div class="grid grid-cols-2 gap-3 text-sm text-gray-800">
                    @foreach ([
                        ['V', 'Baik/Normal', 'green'],
                        ['X', 'Tidak Baik/Abnormal', 'red'],
                        ['-', 'Tidak Diisi', 'gray'],
                        ['OFF', 'Mesin Mati', 'gray']
                    ] as [$symbol, $label, $color])
                        <div class="flex items-center">
                            <span class="inline-block w-7 h-7 bg-{{ $color }}-100 text-{{ $color }}-700 text-center font-bold mr-3 rounded">{{ $symbol }}</span>
                            <span>{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @include('components.edit-form-buttons', ['backRoute' => route('autoloader.index')])
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