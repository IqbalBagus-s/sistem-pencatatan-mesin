<!-- resources/views/vacuum-cleaner/edit.blade.php -->
@extends('layouts.edit-layout-2')

@section('title', 'Edit Form Pencatatan Mesin Vacuum Cleaner')

@section('page-title', 'Edit Pencatatan Mesin Vacuum Cleaner')

@section('content')

    <!-- Menampilkan Nama Checker -->
    <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">{{ Auth::user()->username }}</span>
    </div>

    <!-- Form Input -->
    <form action="{{ route('vacuum-cleaner.update', $check->id) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <!-- Info Vacuum Cleaner yang dipilih -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Vacuum Cleaner:</label>
                    <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-sm flex items-center">
                        <span class="font-medium">Vacuum Cleaner {{ $check->nomer_vacum_cleaner }}</span>
                    </div>
                    <input type="hidden" name="nomer_vacuum_cleaner" value="{{ $check->nomer_vacum_cleaner }}">
                </div>
            
                <div>
                    <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <input type="month" id="bulan" name="bulan" class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" value="{{ $check->bulan }}" readonly>
                </div>
            </div>                    
            @php
                // Items yang perlu di-check
                $items = $itemsMap;

                // Opsi check
                $options = [
                    'V' => '✓',
                    'X' => '✗',
                    '-' => '—',
                    'OFF' => 'OFF'
                ];
                
                // Format tanggal untuk tampilan DD-MM-YYYY
                $tanggal_minggu2 = $check->tanggal_dibuat_minggu2 ? date('d-m-Y', strtotime($check->tanggal_dibuat_minggu2)) : '';
                $tanggal_minggu4 = $check->tanggal_dibuat_minggu4 ? date('d-m-Y', strtotime($check->tanggal_dibuat_minggu4)) : '';
                
                // Check if approvers exist to set readonly status
                $isReadonlyMinggu2 = !empty($check->approver_minggu2) && $check->approver_minggu2 != '-';
                $isReadonlyMinggu4 = !empty($check->approver_minggu4) && $check->approver_minggu4 != '-';
            @endphp
            <!-- Tabel Inspeksi -->
            <div class="mb-6">
                <!-- Tabel untuk minggu kedua -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-12 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10" colspan="1">Minggu</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16" colspan="1">02</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-64" rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10">Item Terperiksa</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16">Cek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                @php
                                    $result = null;
                                    $keterangan = null;
                                    
                                    // Get data for this item if it exists in minggu 2
                                    if(isset($groupedResults[2])) {
                                        $minggu2Data = $groupedResults[2]->where('item_id', $i)->first();
                                        if($minggu2Data) {
                                            $result = $minggu2Data['result'];
                                            $keterangan = $minggu2Data['keterangan'];
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 bg-white z-10 w-12">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-12 bg-white z-10 w-32">
                                        <div class="w-full h-8 px-1 py-0 text-sm flex items-center">{{ $item }}</div>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-20">
                                        <select name="check_1[{{ $i }}]" class="w-full h-8 px-2 py-0 text-center text-sm {{ $isReadonlyMinggu2 ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" {{ $isReadonlyMinggu2 ? 'disabled' : '' }}>
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}" {{ $result == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                        @if($isReadonlyMinggu2)
                                            <input type="hidden" name="check_1[{{ $i }}]" value="{{ $result }}">
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-64">
                                        <input type="text" name="keterangan_1[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm {{ $isReadonlyMinggu2 ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan" value="{{ $keterangan }}" {{ $isReadonlyMinggu2 ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris checker --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-sm sticky left-0 z-10 w-12" rowspan="3">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center bg-sky-50 text-sm sticky left-12 z-10 w-32">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <input type="text" name="checked_by_1" id="user1"
                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                        readonly value="{{ $check->checker_minggu2 }}">
                                    <input type="hidden" name="check_num_1" id="checkNum1" value="{{ $check_num_1 }}">
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm text-center sticky left-12 z-10 w-32">Tanggal</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <input type="text" name="check_date_1" id="date1"
                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                        readonly value="{{ $tanggal_minggu2 }}">
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm sticky left-12 z-10 w-32"></td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    @if(empty($check->checker_minggu2) && !$isReadonlyMinggu2)
                                    <div x-data="{ selected: false }">
                                        <button type="button" 
                                            @click="
                                                selected = !selected; 
                                                if(selected) {
                                                    document.getElementById('user1').value = '{{ Auth::user()->username }}';
                                                    document.getElementById('checkNum1').value = '1';
                                                    
                                                    // Format date properly
                                                    const now = new Date();
                                                    const day = String(now.getDate()).padStart(2, '0');
                                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                                    const year = now.getFullYear();
                                                    const formattedDate = `${day}-${month}-${year}`;
                                                    
                                                    document.getElementById('date1').value = formattedDate;
                                                } else {
                                                    document.getElementById('user1').value = '';
                                                    document.getElementById('checkNum1').value = '';
                                                    document.getElementById('date1').value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                    @else
                                    <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                        Data telah tercatat
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                        {{-- baris approver --}}
                        <tbody class="bg-white">
                            <tr class="bg-gray-50">
                                <td class="border border-gray-300 text-center p-1 bg-gray-50 h-10 text-sm sticky left-0 z-10 w-12">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center bg-gray-50 text-sm sticky left-12 z-10 w-32">Penanggung Jawab</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-gray-50">
                                    <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                        {{ !empty($check->approver_minggu2) ? $check->approver_minggu2 : '-' }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <!-- Tabel untuk minggu keempat -->
                <div class="overflow-x-auto mb-6 border border-gray-300">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-12 sticky left-0 z-10" rowspan="2">No.</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10" colspan="1">Minggu</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16" colspan="1">04</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-64" rowspan="2">Keterangan</th>
                            </tr>
                            <tr>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-32 sticky left-12 z-10">Item Terperiksa</th>
                                <th class="border border-gray-300 bg-sky-50 p-2 w-16">Cek</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $i => $item)
                                @php
                                    $result = null;
                                    $keterangan = null;
                                    
                                    // Get data for this item if it exists in minggu 4
                                    if(isset($groupedResults[4])) {
                                        $minggu4Data = $groupedResults[4]->where('item_id', $i)->first();
                                        if($minggu4Data) {
                                            $result = $minggu4Data['result'];
                                            $keterangan = $minggu4Data['keterangan'];
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="border border-gray-300 text-center p-1 h-10 text-sm sticky left-0 bg-white z-10 w-12">{{ $i }}</td>
                                    <td class="border border-gray-300 p-1 h-10 sticky left-12 bg-white z-10 w-32">
                                        <div class="w-full h-8 px-1 py-0 text-sm flex items-center">{{ $item }}</div>
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-20">
                                        <select name="check_2[{{ $i }}]" class="w-full h-8 px-2 py-0 text-center text-sm {{ $isReadonlyMinggu4 ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white" {{ $isReadonlyMinggu4 ? 'disabled' : '' }}>
                                            @foreach($options as $value => $symbol)
                                                <option value="{{ $value }}" {{ $result == $value ? 'selected' : '' }}>{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                        @if($isReadonlyMinggu4)
                                            <input type="hidden" name="check_2[{{ $i }}]" value="{{ $result }}">
                                        @endif
                                    </td>
                                    <td class="border border-gray-300 p-1 h-10 w-64">
                                        <input type="text" name="keterangan_2[{{ $i }}]" 
                                            class="w-full h-8 px-2 py-0 text-sm {{ $isReadonlyMinggu4 ? 'bg-gray-100' : 'bg-white' }} border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                            placeholder="Keterangan" value="{{ $keterangan }}" {{ $isReadonlyMinggu4 ? 'readonly' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- baris checker --}}
                        <tbody class="bg-white">
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 text-center p-1 bg-sky-50 h-10 text-sm sticky left-0 z-10 w-12" rowspan="3">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center bg-sky-50 text-sm sticky left-12 z-10 w-32">Dibuat Oleh</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    <input type="text" name="checked_by_2" id="user2"
                                        class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                        readonly value="{{ $check->checker_minggu4 }}">
                                    <input type="hidden" name="check_num_2" id="checkNum2" value="{{ $check_num_2 }}">
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm text-center sticky left-12 z-10 w-32">Tanggal</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                <input type="text" name="check_date_2" id="date2"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center"
                                    readonly value="{{ $tanggal_minggu4 }}">
                                </td>
                            </tr>
                            <tr class="bg-sky-50">
                                <td class="border border-gray-300 p-1 font-medium bg-sky-50 text-sm sticky left-12 z-10 w-32"></td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-sky-50">
                                    @if(empty($check->checker_minggu4) && !$isReadonlyMinggu4)
                                    <div x-data="{ selected: false }">
                                        <button type="button" 
                                            @click="
                                                selected = !selected; 
                                                if(selected) {
                                                    document.getElementById('user2').value = '{{ Auth::user()->username }}';
                                                    document.getElementById('checkNum2').value = '2';
                                                    
                                                    // Format date properly
                                                    const now = new Date();
                                                    const day = String(now.getDate()).padStart(2, '0');
                                                    const month = String(now.getMonth() + 1).padStart(2, '0');
                                                    const year = now.getFullYear();
                                                    const formattedDate = `${day}-${month}-${year}`;
                                                    
                                                    document.getElementById('date2').value = formattedDate;
                                                } else {
                                                    document.getElementById('user2').value = '';
                                                    document.getElementById('checkNum2').value = '';
                                                    document.getElementById('date2').value = '';
                                                }"
                                            class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                            :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                            <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                        </button>
                                    </div>
                                    @else
                                    <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                        Data telah tercatat
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                        {{-- baris approver --}}
                        <tbody class="bg-white">
                            <tr class="bg-gray-50">
                                <td class="border border-gray-300 text-center p-1 bg-gray-50 h-10 text-sm sticky left-0 z-10 w-12">-</td>
                                <td class="border border-gray-300 p-1 font-medium text-center bg-gray-50 text-sm sticky left-12 z-10 w-32">Penanggung Jawab</td>
                                <td colspan="2" class="border border-gray-300 p-1 bg-gray-50">
                                    <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                        {{ !empty($check->approver_minggu4) ? $check->approver_minggu4 : '-' }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

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
                        <span>Vacuum cleaner dibersihkan per 2 minggu sekali.</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span>Setiap pengecekan vacuum cleaner bergantian antara Maintenance.</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span>Setelah membersihkan, dimohon untuk mengisi FORM CHECKLIST VACUUM CLEANER yang sudah disediakan.</span>
                    </li>
                </ul>
                
                <div class="mt-4 pt-3 border-t border-blue-100">
                    <h6 class="font-medium text-blue-600 mb-2">Daftar Vacuum Cleaner:</h6>
                    <div class="grid md:grid-cols-3 gap-2">
                        <div class="bg-white p-2 rounded shadow-sm border border-gray-100">
                            <span class="font-semibold">Vacuum Cleaner No 1:</span>
                            <span class="ml-1">Nilvis</span>
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm border border-gray-100">
                            <span class="font-semibold">Vacuum Cleaner No 2:</span>
                            <span class="ml-1">Modif</span>
                        </div>
                        <div class="bg-white p-2 rounded shadow-sm border border-gray-100">
                            <span class="font-semibold">Vacuum Cleaner No 3:</span>
                            <span class="ml-1">Ransel</span>
                        </div>
                    </div>
                </div>
            </div>

            @include('components.edit-form-buttons', ['backRoute' => route('vacuum-cleaner.index')])
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Additional JavaScript if needed
    });
</script>
@endsection