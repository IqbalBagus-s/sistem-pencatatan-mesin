@extends('layouts.edit-layout-2')

@section('title', 'Edit Form Pencatatan Mesin Hopper')

@section('page-title', 'Edit Pencatatan Mesin Hopper')

@section('show-checker', true)

@section('content')
    <!-- Form Input -->
    <form action="{{ route('hopper.update', $hopperCheck->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <!-- Dropdown Pilih No Hopper - Already selected -->
            <div class="relative w-full">
                <label class="block mb-2 text-sm font-medium text-gray-700">
                    No Hopper: 
                </label>
                <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-left flex items-center">
                    <span>Hopper {{ $hopperCheck->nomer_hopper }}</span>
                </div>
                <input type="hidden" name="nomer_hopper" value="{{ $hopperCheck->nomer_hopper }}">
            </div>
        
            <div>
                <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">
                    Bulan:
                </label>
                <!-- Mengubah input month menjadi tampilan read-only -->
                <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm text-left flex items-center">
                    <span>{{ date('F Y', strtotime($hopperCheck->bulan)) }}</span>
                </div>
                <input type="hidden" name="bulan" value="{{ $hopperCheck->bulan }}">
            </div>
        </div>                  
        @php
            // Items yang perlu di-check
            $items = [
                1 => 'Filter',
                2 => 'Selang',
                3 => 'Kontraktor',
                4 => 'Temperatur Kontrol',
                5 => 'MCB'
            ];

            // Opsi check baru
            $options = [
                'V' => 'V',
                'X' => 'X',
                '-' => '—',
                'OFF' => 'OFF'
            ];
        @endphp
        
        <!-- Input untuk menyimpan semua checked items -->
        @foreach($items as $i => $item)
            <input type="hidden" name="checked_items[{{ $i }}]" value="{{ $item }}">
        @endforeach

        <!-- Tabel Inspeksi Mingguan -->
        <div class="mb-6">
            <div class="overflow-x-auto mb-6 border border-gray-300">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 w-10 text-sm sticky left-0 z-10" rowspan="2">No.</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10" colspan="1">Minggu</th>
                            
                            @for ($i = 1; $i <= 4; $i++)
                                @php
                                    $isApproved = !empty($hopperCheck->{'approved_by_minggu'.$i}) && $hopperCheck->{'approved_by_minggu'.$i} != '-';
                                @endphp
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 text-sm" colspan="1">0{{ $i }}</th>
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-32 text-sm" rowspan="2">Keterangan</th>
                            @endfor
                        </tr>
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10">Item Terperiksa</th>
                            @for ($i = 1; $i <= 4; $i++)
                                @php
                                    $isApproved = !empty($hopperCheck->{'approved_by_minggu'.$i}) && $hopperCheck->{'approved_by_minggu'.$i} != '-';
                                @endphp
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 text-sm">Check</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i => $item)
                            @if($i == 3)
                                <tr>
                                    <td colspan="10" class="border border-gray-300 text-center p-2 h-8 bg-gray-100 font-medium text-sm">
                                        Panel Kelistrikan
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="border border-gray-300 text-center p-1 h-10 text-xs sticky left-0 bg-white z-10">{{ $i }}</td>
                                <td class="border border-gray-300 p-1 h-10 sticky left-10 bg-white z-10">
                                    <div class="w-full h-8 px-1 py-0 text-xs flex items-center">{{ $item }}</div>
                                </td>
                                
                                @for($j = 1; $j <= 4; $j++)
                                    @php
                                        $isApproved = !empty($hopperCheck->{'approved_by_minggu'.$j}) && $hopperCheck->{'approved_by_minggu'.$j} != '-';
                                        
                                        // Get result value from hopperResults
                                        $result = $hopperResults->firstWhere('checked_items', $item);
                                        $resultValue = '';
                                        $keteranganValue = '';
                                        
                                        if ($result) {
                                            $mingguField = 'minggu'.$j;
                                            $keteranganField = 'keterangan_minggu'.$j;
                                            
                                            // Get the value directly
                                            $resultValue = $result->$mingguField ?? 'V';
                                            $keteranganValue = $result->$keteranganField ?? '';
                                        } else {
                                            // Default to V if no value exists
                                            $resultValue = 'V';
                                        }
                                    @endphp
                                
                                    <!-- Minggu {{ $j }} Check -->
                                    <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                        @if($isApproved)
                                            <!-- Read-only display if approved -->
                                            <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center justify-center">
                                                {!! $options[$resultValue] ?? '—' !!}
                                            </div>
                                            <input type="hidden" name="check_{{ $j }}[{{ $i }}]" value="{{ $resultValue }}">
                                        @else
                                            <!-- Editable dropdown if not approved -->
                                            <select name="check_{{ $j }}[{{ $i }}]" class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                                @foreach(['V', 'X', '-', 'OFF'] as $value)
                                                    <option value="{{ $value }}" {{ $resultValue == $value ? 'selected' : '' }}>
                                                        {{ $value == 'V' ? 'V' : ($value == 'X' ? 'X' : ($value == '-' ? '—' : $value)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </td>
                                    
                                    <!-- Minggu {{ $j }} Keterangan -->
                                    <td class="border border-gray-300 p-1 h-10 {{ $isApproved ? 'bg-green-50' : '' }}">
                                        @if($isApproved)
                                            <!-- Read-only display if approved -->
                                            <div class="w-full h-8 px-2 py-0 text-sm bg-green-100 border border-gray-300 rounded flex items-center">
                                                {{ $keteranganValue }}
                                            </div>
                                            <input type="hidden" name="keterangan_{{ $j }}[{{ $i }}]" value="{{ $keteranganValue }}">
                                        @else
                                            <!-- Editable input if not approved -->
                                            <input type="text" name="keterangan_{{ $j }}[{{ $i }}]" 
                                                value="{{ $keteranganValue }}"
                                                class="w-full h-8 px-2 py-0 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                                placeholder="Keterangan">
                                        @endif
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody class="bg-white">
                        <tr class="bg-sky-50">
                            <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                            <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10 w-24">Dibuat Oleh</td>
                            
                            @for($j = 1; $j <= 4; $j++)
                                @php
                                    $checkedBy = $hopperCheck->{'checked_by_minggu'.$j} ?? '';
                                    $isChecked = !empty($checkedBy);
                                    $isApproved = !empty($hopperCheck->{'approved_by_minggu'.$j}) && $hopperCheck->{'approved_by_minggu'.$j} != '-';
                                    $tanggal = $hopperCheck->{'tanggal_minggu'.$j} ?? '';
                                    
                                    // Format tanggal untuk tampilan jika ada
                                    $formattedDate = '';
                                    if (!empty($tanggal)) {
                                        $date = \Carbon\Carbon::parse($tanggal);
                                        $formattedDate = $date->format('d').' '.$date->locale('id')->monthName.' '.$date->format('Y');
                                    }
                                @endphp
                                <td colspan="2" class="border border-gray-300 p-1 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} w-32">
                                    <div x-data="{ 
                                        selected: {{ $isChecked ? 'true' : 'false' }}, 
                                        userName: '{{ $checkedBy }}',
                                        tanggal: '{{ $formattedDate }}',
                                        dbTanggal: '{{ $tanggal }}',
                                        isApproved: {{ $isApproved ? 'true' : 'false' }},
                                        hasExistingData: {{ (!empty($checkedBy) && !empty($tanggal)) ? 'true' : 'false' }}
                                    }">
                                        <div class="mt-1" x-show="selected || isApproved">
                                            <input type="text" name="checked_by_minggu{{ $j }}" x-ref="user{{ $j }}" value="{{ $checkedBy }}"
                                                class="w-full px-2 py-1 text-sm {{ $isApproved ? 'bg-green-100' : 'bg-gray-100' }} border border-gray-300 rounded mb-1 text-center"
                                                readonly>
                                            <input type="text" x-ref="displayDate{{ $j }}" value="{{ $formattedDate }}"
                                                class="w-full px-2 py-1 text-sm {{ $isApproved ? 'bg-green-100' : 'bg-gray-100' }} border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="tanggal_minggu{{ $j }}" x-ref="date{{ $j }}" value="{{ $tanggal }}">
                                            
                                            @if($isApproved)
                                                <div class="mt-1 text-xs text-green-600 text-center">
                                                    Disetujui oleh: {{ $hopperCheck->{'approved_by_minggu'.$j} }}
                                                </div>
                                                <input type="hidden" name="approved_by_minggu{{ $j }}" value="{{ $hopperCheck->{'approved_by_minggu'.$j} }}">
                                            @endif
                                        </div>
                                        
                                        <!-- Hanya tampilkan tombol jika belum diapprove -->
                                        @if(!$isApproved)
                                        <button type="button" 
                                            x-show="!hasExistingData || !selected"
                                            @click="selected = !selected; 
                                                if(selected) {
                                                    userName = '{{ Auth::user()->username }}'; 
                                                    $refs.user{{ $j }}.value = userName;
                                                    
                                                    // Format tanggal untuk tampilan: DD Bulan YYYY
                                                    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                                    const today = new Date();
                                                    const day = today.getDate();
                                                    const month = monthNames[today.getMonth()];
                                                    const year = today.getFullYear();
                                                    tanggal = day + ' ' + month + ' ' + year;
                                                    
                                                    // Format tanggal untuk database: YYYY-MM-DD
                                                    const dbMonth = String(today.getMonth() + 1).padStart(2, '0');
                                                    const dbDay = String(today.getDate()).padStart(2, '0');
                                                    const dbDate = `${year}-${dbMonth}-${dbDay}`;
                                                    
                                                    $refs.displayDate{{ $j }}.value = tanggal;
                                                    $refs.date{{ $j }}.value = dbDate;
                                                } else {
                                                    userName = '';
                                                    tanggal = '';
                                                    $refs.user{{ $j }}.value = '';
                                                    $refs.displayDate{{ $j }}.value = '';
                                                    $refs.date{{ $j }}.value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center mt-1 max-w-full"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            @endfor
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
        
        <!-- Tombol Submit dan Kembali -->
        @include('components.edit-form-buttons', ['backRoute' => route('hopper.index')])
    </form>
@endsection