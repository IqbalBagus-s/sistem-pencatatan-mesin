@extends('layouts.edit-layout-2')

@section('title', 'Edit Pencatatan Mesin Hopper')
@section('page-title', 'Edit Pencatatan Mesin Hopper')
@section('show-checker')

@section('content')
<!-- Form Edit -->
<form action="{{ route('hopper.update', $hopperCheck->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="grid md:grid-cols-2 gap-4 mb-4">
        <!-- Dropdown Pilih No Hopper (Read-Only) -->
        <div class="relative w-full">
            <label class="block mb-2 text-sm font-medium text-gray-700">Pilih No Hopper:</label>
            
            <input type="text" 
                value="Hopper {{ $hopperCheck->nomer_hopper }}" 
                class="w-full h-10 px-3 py-2 bg-gray-200 border border-gray-300 rounded-md text-sm cursor-not-allowed" 
                readonly>
            
            <input type="hidden" name="nomer_hopper" value="{{ $hopperCheck->nomer_hopper }}">
        </div>                      
    
        <div>
            <label for="bulan" class="block mb-2 text-sm font-medium text-gray-700">Pilih Bulan:</label>
            <input type="text" 
                value="{{ \Carbon\Carbon::parse($hopperCheck->bulan)->format('F Y') }}" 
                class="w-full h-10 px-3 py-2 bg-gray-200 border border-gray-300 rounded-md cursor-not-allowed" 
                readonly>
            <input type="hidden" id="bulan" name="bulan" value="{{ $hopperCheck->bulan }}">
        </div>
    </div>                    

    <!-- Tabel Inspeksi -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 bg-sky-50 p-2 w-10" rowspan="2">No.</th>
                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-28" colspan="1">Minggu</th>
                    
                    <th class="border border-gray-300 bg-sky-50 p-2" colspan="1">01</th>
                    <th class="border border-gray-300 bg-sky-50 p-2 w-32" rowspan="2">Keterangan</th>
                    <th class="border border-gray-300 bg-sky-50 p-2" colspan="1">02</th>
                    <th class="border border-gray-300 bg-sky-50 p-2 w-32" rowspan="2">Keterangan</th>
                    <th class="border border-gray-300 bg-sky-50 p-2" colspan="1">03</th>
                    <th class="border border-gray-300 bg-sky-50 p-2 w-32" rowspan="2">Keterangan</th>
                    <th class="border border-gray-300 bg-sky-50 p-2" colspan="1">04</th>
                    <th class="border border-gray-300 bg-sky-50 p-2 w-32" rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th class="border border-gray-300 bg-sky-50 p-2 min-w-28">Checked Items</th>
                    <th class="border border-gray-300 bg-sky-50 p-2">Check</th>
                    <th class="border border-gray-300 bg-sky-50 p-2">Check</th>
                    <th class="border border-gray-300 bg-sky-50 p-2">Check</th>
                    <th class="border border-gray-300 bg-sky-50 p-2">Check</th>
                </tr>
            </thead>                          
            <tbody>
                @php
                    $items = [
                        1 => 'Filter',
                        2 => 'Selang',
                        3 => 'Kontraktor',
                        4 => 'Temperatur Kontrol',
                        5 => 'MCB'
                    ];
                    
                    $options = [
                        1 => ['Bersih', 'Kotor', 'OFF'],
                        2 => ['Tidak Bocor', 'Bocor', 'OFF'],
                        3 => ['Baik', 'Buruk', 'OFF'],
                        4 => ['Baik', 'Buruk', 'OFF'],
                        5 => ['Baik', 'Buruk', 'OFF']
                    ];
                @endphp
                
                @foreach($items as $i => $item)
                    @php
                        $result = $hopperResults->firstWhere('checked_items', $item);
                    @endphp
                    <tr>
                        <td class="border border-gray-300 text-center p-2 h-12">{{ $i }}</td>
                        <td class="border border-gray-300 p-2 h-12">
                            <input type="text" name="checked_items[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center" 
                                value="{{ $item }}" readonly>
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <select name="check_1[{{ $i }}]" class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                <option value="">Pilih</option>
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ $result && $result->minggu1 == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <input type="text" name="keterangan_1[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan" 
                                value="{{ $result ? $result->keterangan_minggu1 : '' }}">
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <select name="check_2[{{ $i }}]" class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                <option value="">Pilih</option>
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ $result && $result->minggu2 == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <input type="text" name="keterangan_2[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan"
                                value="{{ $result ? $result->keterangan_minggu2 : '' }}">
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <select name="check_3[{{ $i }}]" class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                <option value="">Pilih</option>
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ $result && $result->minggu3 == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <input type="text" name="keterangan_3[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan"
                                value="{{ $result ? $result->keterangan_minggu3 : '' }}">
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <select name="check_4[{{ $i }}]" class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white">
                                <option value="">Pilih</option>
                                @foreach($options[$i] as $option)
                                    <option value="{{ $option }}" {{ $result && $result->minggu4 == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border border-gray-300 p-2 h-12">
                            <input type="text" name="keterangan_4[{{ $i }}]" 
                                class="w-full h-10 px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white"
                                placeholder="Keterangan"
                                value="{{ $result ? $result->keterangan_minggu4 : '' }}">
                        </td>
                    </tr>
                    @if($i == 2)
                    <tr>
                        <td colspan="10" class="border border-gray-300 text-center p-2 h-12 bg-gray-100 font-medium">
                            Panel Kelistrikan
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
            <tbody class="bg-white">
                <tr class="bg-sky-50">
                    <td class="border border-gray-300 text-center p-2 bg-sky-50 h-12" rowspan="1">-</td>
                    <td class="border border-gray-300 p-2 font-medium bg-sky-50">Dibuat Oleh</td>
                    
                    <!-- Week 1 -->
                    <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                        <div x-data="{ selected: '{{ $hopperCheck->checked_by_minggu1 }}' ? true : false, userName: '{{ $hopperCheck->checked_by_minggu1 }}' }">
                            <button type="button" 
                                @click="selected = !selected; 
                                    if(selected) {
                                        userName = '{{ Auth::user()->username }}'; 
                                        $refs.user1.value = userName; 
                                        $refs.date1.value = '{{ date('Y-m-d') }}';
                                    } else {
                                        userName = '';
                                        $refs.user1.value = '';
                                        $refs.date1.value = '';
                                    }"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                            </button>
                            <div class="mt-2 space-y-1" x-show="selected">
                                <input type="text" name="created_by_1" x-ref="user1" x-bind:value="userName || '{{ $hopperCheck->checked_by_minggu1 }}'"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                                <input type="text" name="created_date_1" x-ref="date1" 
                                    value="{{ $hopperCheck->tanggal_minggu1 }}"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Week 2 -->
                    <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                        <div x-data="{ selected: '{{ $hopperCheck->checked_by_minggu2 }}' ? true : false, userName: '{{ $hopperCheck->checked_by_minggu2 }}' }">
                            <button type="button" 
                                @click="selected = !selected; 
                                    if(selected) {
                                        userName = '{{ Auth::user()->username }}'; 
                                        $refs.user2.value = userName; 
                                        $refs.date2.value = '{{ date('Y-m-d') }}';
                                    } else {
                                        userName = '';
                                        $refs.user2.value = '';
                                        $refs.date2.value = '';
                                    }"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                            </button>
                            <div class="mt-2 space-y-1" x-show="selected">
                                <input type="text" name="created_by_2" x-ref="user2" x-bind:value="userName || '{{ $hopperCheck->checked_by_minggu2 }}'"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                                <input type="text" name="created_date_2" x-ref="date2" 
                                    value="{{ $hopperCheck->tanggal_minggu2 }}"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Week 3 -->
                    <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                        <div x-data="{ selected: '{{ $hopperCheck->checked_by_minggu3 }}' ? true : false, userName: '{{ $hopperCheck->checked_by_minggu3 }}' }">
                            <button type="button" 
                                @click="selected = !selected; 
                                    if(selected) {
                                        userName = '{{ Auth::user()->username }}'; 
                                        $refs.user3.value = userName; 
                                        $refs.date3.value = '{{ date('Y-m-d') }}';
                                    } else {
                                        userName = '';
                                        $refs.user3.value = '';
                                        $refs.date3.value = '';
                                    }"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                            </button>
                            <div class="mt-2 space-y-1" x-show="selected">
                                <input type="text" name="created_by_3" x-ref="user3" x-bind:value="userName || '{{ $hopperCheck->checked_by_minggu3 }}'"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                                <input type="text" name="created_date_3" x-ref="date3" 
                                    value="{{ $hopperCheck->tanggal_minggu3 }}"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                            </div>
                        </div>
                    </td>
                    
                    <!-- Week 4 -->
                    <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                        <div x-data="{ selected: '{{ $hopperCheck->checked_by_minggu4 }}' ? true : false, userName: '{{ $hopperCheck->checked_by_minggu4 }}' }">
                            <button type="button" 
                                @click="selected = !selected; 
                                    if(selected) {
                                        userName = '{{ Auth::user()->username }}'; 
                                        $refs.user4.value = userName; 
                                        $refs.date4.value = '{{ date('Y-m-d') }}';
                                    } else {
                                        userName = '';
                                        $refs.user4.value = '';
                                        $refs.date4.value = '';
                                    }"
                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                            </button>
                            <div class="mt-2 space-y-1" x-show="selected">
                                <input type="text" name="created_by_4" x-ref="user4" x-bind:value="userName || '{{ $hopperCheck->checked_by_minggu4 }}'"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                                <input type="text" name="created_date_4" x-ref="date4" 
                                    value="{{ $hopperCheck->tanggal_minggu4 }}"
                                    class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded"
                                    readonly>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex justify-between mt-6">
        <a href="{{ route('hopper.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Kembali
        </a>
        <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    document.getElementById("tanggal")?.addEventListener("change", function() {
        let tanggal = new Date(this.value);
        let hari = new Intl.DateTimeFormat('id-ID', { weekday: 'long' }).format(tanggal);
        document.getElementById("hari").value = hari;
    });
</script>
@endsection