<!-- resources/views/caplining/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Pencatatan Mesin Caplining')

@section('page-title', 'Edit Pencatatan Mesin Caplining')

@section('show-checker', true)

@section('content')
    <!-- Form Input -->
    <form action="{{ route('caplining.update', $check->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <!-- Display-only No Caplining (replaced dropdown with simple display) -->
            <div class="relative w-full">
                <!-- Label -->
                <label class="block mb-2 text-sm font-medium text-gray-700">No Caplining:</label>
                
                <!-- Display field (not editable) -->
                <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                    <span>Caplining {{ $check->nomer_caplining }}</span>
                </div>
                
                <!-- Hidden Input to keep the value for submission -->
                <input type="hidden" name="nomer_caplining" value="{{ $check->nomer_caplining }}">
            </div>
        </div>                    
        @php
            // Items yang perlu di-check (updated list sesuai permintaan)
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
                'V' => 'V',
                'X' => 'X',
                '-' => '-',
                'OFF' => 'OFF'
            ];

            // Nama bulan dalam bahasa Indonesia (singkatan)
            $bulanSingkat = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 
                6 => 'Jun', 7 => 'Jul', 8 => 'Ags', 9 => 'Sep', 10 => 'Okt', 
                11 => 'Nov', 12 => 'Des'
            ];
        @endphp

        <!-- Tabel Inspeksi -->
        <div class="mb-6">
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
                            
                            @for ($i = 1; $i <= 5; $i++)
                                @php
                                    // Check if this column is approved
                                    $isApproved = !empty($check->{'approved_by'.$i});
                                @endphp
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2" colspan="1">
                                    <!-- Conditionally editable date field based on whether date exists -->
                                    <div class="relative">
                                        @php
                                            $hasDate = !empty($check->{'tanggal_check'.$i});
                                            $formattedDate = $hasDate ? date('d', strtotime($check->{'tanggal_check'.$i})) . ' ' . $bulanSingkat[date('n', strtotime($check->{'tanggal_check'.$i}))] . ' ' . date('Y', strtotime($check->{'tanggal_check'.$i})) : '';
                                            $rawDate = $hasDate ? date('Y-m-d', strtotime($check->{'tanggal_check'.$i})) : '';
                                        @endphp

                                        <!-- Read-only display if date exists or is approved -->
                                        @if($hasDate || $isApproved)
                                            <div class="flex items-center justify-center">
                                                <div class="px-2 py-1 border border-gray-300 rounded {{ $isApproved ? 'bg-green-100' : 'bg-white' }} text-center">
                                                    <span class="text-sm font-medium">{{ $formattedDate }}</span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="tanggal_{{ $i }}" value="{{ $formattedDate }}">
                                            <input type="hidden" name="tanggal_raw_{{ $i }}" value="{{ $rawDate }}">
                                        @else
                                            <!-- Editable date picker if no date exists and not approved -->
                                            <div x-data="{ 
                                                formattedDate: '', 
                                                showPicker: false,
                                                formatDate: function(value) {
                                                    if (!value) return '';
                                                    const date = new Date(value);
                                                    const day = date.getDate();
                                                    const month = date.getMonth();
                                                    const year = date.getFullYear();
                                                    
                                                    // Mengambil nama bulan dari array PHP
                                                    const monthNames = {{ json_encode($bulanSingkat) }};
                                                    this.formattedDate = `${day} ${monthNames[month+1]} ${year}`;
                                                    
                                                    // Update hidden input untuk tanggal_i dan tanggal_checki
                                                    document.querySelector('input[name=\'tanggal_' + {{ $i }} + '\']').value = this.formattedDate;
                                                    document.querySelector('input[name=\'tanggal_check' + {{ $i }} + '\']').value = this.formattedDate;
                                                    
                                                    this.showPicker = false;
                                                    return this.formattedDate;
                                                },
                                                clearDate: function() {
                                                    if (this.formattedDate) {
                                                        this.formattedDate = '';
                                                        document.querySelector('input[name=\'tanggal_' + {{ $i }} + '\']').value = '';
                                                        document.querySelector('input[name=\'tanggal_raw_' + {{ $i }} + '\']').value = '';
                                                        document.querySelector('input[name=\'tanggal_check' + {{ $i }} + '\']').value = '';
                                                        return true;
                                                    }
                                                    return false;
                                                }
                                            }" class="relative">
                                                <!-- Tombol kalender dengan ikon dan tampilan tanggal -->
                                                <div class="flex items-center justify-center">
                                                    <button type="button" 
                                                        @click="formattedDate ? clearDate() : showPicker = !showPicker"
                                                        class="flex items-center justify-center px-2 py-1 border border-gray-300 rounded bg-white hover:bg-gray-50">
                                                        <span x-show="!formattedDate" class="flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            <span class="text-xs">Pilih</span>
                                                        </span>
                                                        <span x-show="formattedDate" class="text-sm font-medium" x-text="formattedDate"></span>
                                                    </button>
                                                </div>
                                                
                                                <!-- Date picker popup -->
                                                <div x-show="showPicker" 
                                                    @click.outside="showPicker = false"
                                                    class="absolute z-20 top-full left-0 mt-1 bg-white shadow-lg rounded border border-gray-200 p-1">
                                                    <input type="date" 
                                                        name="tanggal_raw_{{ $i }}" 
                                                        @change="formatDate($event.target.value); showPicker = false;"
                                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </div>
                                                
                                                <input type="hidden" name="tanggal_{{ $i }}" :value="formattedDate">
                                                <input type="hidden" name="tanggal_check{{ $i }}" :value="formattedDate">
                                            </div>
                                        @endif
                                    </div>
                                </th>
                                <th class="border border-gray-300 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }} p-2 w-64" rowspan="2">Keterangan</th>
                            @endfor
                        </tr>
                        <tr>
                            <th class="border border-gray-300 bg-sky-50 p-2 min-w-40 sticky left-10 z-10">Item Terperiksa</th>
                            @for ($i = 1; $i <= 5; $i++)
                                @php
                                    $isApproved = !empty($check->{'approved_by'.$i});
                                @endphp
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
                                
                                @for($j = 1; $j <= 5; $j++)
                                    @php
                                        // Cari data item ini pada kolom j
                                        $itemData = $groupedData[$j] ?? collect();
                                        $currentItem = $itemData->where('item_id', $i)->first();
                                        $resultValue = $currentItem['result'] ?? '';
                                        $keteranganValue = $currentItem['keterangan'] ?? '';
                                        
                                        // Check if this column is approved
                                        $isApproved = !empty($check->{'approved_by'.$j});
                                    @endphp
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
                                                placeholder="Keterangan"
                                                style="min-width: 200px;">
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
                            
                            @for($j = 1; $j <= 5; $j++)
                                @php
                                    $checkedBy = $check->{"checked_by$j"} ?? '';
                                    $isChecked = !empty($checkedBy);
                                    $isApproved = !empty($check->{"approved_by$j"});
                                @endphp
                                <td colspan="2" class="border border-gray-300 p-1 {{ $isApproved ? 'bg-green-50' : 'bg-sky-50' }}">
                                    <div x-data="{ 
                                        selected: {{ $isChecked ? 'true' : 'false' }},
                                        username: '{{ $isChecked ? $checkedBy : $user->username }}',
                                        originalUsername: '{{ $checkedBy }}',
                                        currentUsername: '{{ $user->username }}',
                                        isPreexisting: {{ $isChecked ? 'true' : 'false' }},
                                        isApproved: {{ $isApproved ? 'true' : 'false' }}
                                    }">
                                        <div class="mt-1 mb-1" x-show="selected || isApproved">
                                            <span class="block w-full px-2 py-1 text-sm {{ $isApproved ? 'bg-green-100' : 'bg-white' }} border border-gray-300 rounded text-center" x-text="username">
                                            </span>
                                            @if($isApproved)
                                                <div class="mt-1 text-xs text-green-600 text-center">
                                                    Disetujui oleh: {{ $check->{"approved_by$j"} }}
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Only show button when not preexisting and not approved -->
                                        <button type="button" 
                                            x-show="!isPreexisting && !isApproved"
                                            @click="
                                                selected = !selected;
                                                if(selected) {
                                                    username = currentUsername;
                                                    document.querySelector('input[name=\'checked_by{{ $j }}\']').value = currentUsername;
                                                    document.querySelector('input[name=\'tanggal_check{{ $j }}\']').value = 
                                                    document.querySelector('input[name=\'tanggal_{{ $j }}\']').value;
                                                } else {
                                                    username = '';
                                                    document.querySelector('input[name=\'checked_by{{ $j }}\']').value = '';
                                                    document.querySelector('input[name=\'tanggal_check{{ $j }}\']').value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                        <!-- Hidden input to store who checked this column -->
                                        <input type="hidden" name="checked_by{{ $j }}" value="{{ $checkedBy }}">
                                        <!-- Hidden input to store the check date for the controller -->
                                        <input type="hidden" name="tanggal_check{{ $j }}" value="{{ $check->{'tanggal_check'.$j} ? date('d', strtotime($check->{'tanggal_check'.$j})) . ' ' . $bulanSingkat[date('n', strtotime($check->{'tanggal_check'.$j}))] . ' ' . date('Y', strtotime($check->{'tanggal_check'.$j})) : '' }}">
                                        <!-- Hidden input to maintain approved by value -->
                                        <input type="hidden" name="approved_by{{ $j }}" value="{{ $check->{'approved_by'.$j} ?? '' }}">
                                    </div>
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
                <li class="flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span> Isi bagian tanggal agar data dapat tersimpan dengan benar.</span>
                </li>
                <li class="flex items-start mt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span>Kolom dengan warna hijau telah disetujui dan tidak dapat diubah.</span>
                </li>
            </ul>

            <div class="mt-4 p-3 bg-white rounded-lg col-span-1 md:col-span-2 lg:col-span-3 mb-4">
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

        @include('components.edit-form-buttons', ['backRoute' => route('caplining.index')])
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any date fields that already have values
        for (let i = 1; i <= 5; i++) {
            const dateInput = document.querySelector(`input[name='tanggal_raw_${i}']`);
            if (dateInput && dateInput.value) {
                // This will trigger the formatDate function in Alpine.js
                const event = new Event('change');
                dateInput.dispatchEvent(event);
            }
        }
    });
</script>
@endsection