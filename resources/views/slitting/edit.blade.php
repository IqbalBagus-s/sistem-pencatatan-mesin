<!-- resources/views/slitting/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Form Pencatatan Mesin Slitting')

@section('page-title', 'Edit Pencatatan Mesin Slitting')

@section('show-checker', true)

@section('content')
    <!-- Form Input -->
    <form action="{{ route('slitting.update', $check->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <!-- Dropdown Pilih No Slitting - Already selected -->
            <div class="relative w-full">
                <label for="bulan" class="block mb-2">No Slitting:</label>
                <div class="px-3 py-2 bg-white border border-blue-400 rounded-md">
                    Slitting Nomor {{ $check->nomer_slitting }}
                </div>
                <input type="hidden" name="nomer_slitting" value="{{ $check->nomer_slitting }}">
            </div>
        
            <div>
                <label for="bulan" class="block mb-2">Bulan:</label>
                <div class="px-3 py-2 bg-white border border-blue-400 rounded-md">
                    {{ \Carbon\Carbon::parse($check->bulan)->translatedFormat('F Y') }}
                </div>
                <input type="hidden" name="bulan" value="{{ $check->bulan }}">
            </div>
        </div>                  
        @php
            // Items yang perlu di-check (sama seperti di create)
            $items = [
                1 => 'Conveyor',
                2 => 'Motor Conveyor',
                3 => 'Kelistrikan',
                4 => 'Kontaktor',
                5 => 'Inverter',
                6 => 'Vibrator',
                7 => 'Motor Vibrator',
                8 => 'Motor Blower',
                9 => 'Selang angin',
                10 => 'Flow Control',
                11 => 'Sensor',
                12 => 'Limit Switch',
                13 => 'Pisau Cutting',
                14 => 'Motor Cutting',
                15 => 'Elemen ',
                16 => 'Regulator',
                17 => 'Air Filter',
            ];

            // Opsi check
            $options = [
                'V' => 'V',
                'X' => 'X',
                '-' => '-',
                'OFF' => 'OFF'
            ];
        @endphp
        
        <!-- Input untuk menyimpan semua checked items -->
        @foreach($items as $i => $item)
            <input type="hidden" name="checked_items[{{ $i }}]" value="{{ $item }}">
        @endforeach

        <!-- Tabel Inspeksi Mingguan -->
        <div class="mb-6">
            <div class="md:hidden text-sm text-gray-500 italic mb-2">
                ← Geser ke kanan untuk melihat semua kolom →
            </div>
            <div class="overflow-x-auto mb-6 border border-gray-300">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 w-10 text-sm sticky left-0 z-10" rowspan="2">No.</th>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10" colspan="1">Minggu</th>
                            
                            @for ($i = 1; $i <= 4; $i++)
                                @php
                                    // Menggunakan format nama field yang benar
                                    $isApproved = !empty($check->{'approved_by_minggu'.$i});
                                @endphp
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 text-sm" colspan="1">0{{ $i }}</th>
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-32 text-sm" rowspan="2">Keterangan</th>
                            @endfor
                        </tr>
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-28 text-sm sticky left-10 z-10">Item Terperiksa</th>
                            @for ($i = 1; $i <= 4; $i++)
                                @php
                                    // Menggunakan format nama field yang benar
                                    $isApproved = !empty($check->{'approved_by_minggu'.$i});
                                @endphp
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 text-sm">Check</th>
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
                                
                                @for($j = 1; $j <= 4; $j++)
                                    @php
                                        // Menggunakan format nama field yang benar
                                        $isApproved = !empty($check->{'approved_by_minggu'.$j});
                                        $resultValue = isset($formattedResults[$i]['minggu'.$j]) ? $formattedResults[$i]['minggu'.$j] : '';
                                        $keteranganValue = isset($formattedResults[$i]['keterangan_minggu'.$j]) ? $formattedResults[$i]['keterangan_minggu'.$j] : '';
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
                                                @foreach($options as $value => $symbol)
                                                    <option value="{{ $value }}" {{ $resultValue == $value ? 'selected' : '' }}>{!! $symbol !!}</option>
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
                            <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-xs sticky left-10 z-10">Dibuat Oleh</td>
                            
                            @for($j = 1; $j <= 4; $j++)
                                @php
                                    $checkedBy = $checkerData['checked_by_'.$j] ?? '';
                                    $isChecked = !empty($checkedBy);
                                    // Menggunakan format nama field yang benar
                                    $isApproved = !empty($check->{'approved_by_minggu'.$j});
                                @endphp
                                <td colspan="2" class="border border-gray-300 p-1 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }}">
                                    <div x-data="{ 
                                        selected: {{ $isChecked ? 'true' : 'false' }}, 
                                        userName: '{{ $checkedBy }}',
                                        isApproved: {{ $isApproved ? 'true' : 'false' }}
                                    }">
                                        <div class="mt-1 mb-1" x-show="selected || isApproved">
                                            <input type="text" name="checked_by_{{ $j }}" x-ref="user{{ $j }}" value="{{ $checkedBy }}"
                                                class="w-full px-2 py-1 text-sm {{ $isApproved ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center"
                                                readonly>
                                            <input type="hidden" name="check_num_{{ $j }}" x-ref="checkNum{{ $j }}" value="{{ $isChecked ? $j : '' }}">
                                            
                                            @if($isApproved)
                                                <div class="mt-1 text-xs text-green-600 text-center">
                                                    Disetujui oleh: {{ $check->{'approved_by_minggu'.$j} }}
                                                </div>
                                                <input type="hidden" name="approved_by_minggu{{ $j }}" value="{{ $check->{'approved_by_minggu'.$j} }}">
                                            @endif
                                        </div>
                                        
                                        <!-- Hanya tampilkan tombol jika belum dicheck dan belum diapprove -->
                                        @if(!$isChecked && !$isApproved)
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
        @include('components.edit-form-buttons', ['backRoute' => route('slitting.index')])
    </form>
@endsection