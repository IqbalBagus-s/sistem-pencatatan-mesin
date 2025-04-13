@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Dehum Bahan')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Dehum Bahan</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <!-- Menampilkan Nama Checker -->
        <div class="bg-sky-50 p-4 rounded-md mb-5">
            <span class="text-gray-600 font-bold">Checker: </span>
            <span class="font-bold text-blue-700">
                @php
                    $checkers = collect([
                        $dehumBahanRecord->created_by_1, 
                        $dehumBahanRecord->created_by_2, 
                        $dehumBahanRecord->created_by_3, 
                        $dehumBahanRecord->created_by_4
                    ])->filter()->unique()->values()->implode(', ') ?? 'Tidak Diketahui'
                @endphp
                {{ $checkers }}
            </span>
        </div>
    
        <!-- Detail Informasi -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">No Dehum Bahan:</label>
                <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md flex items-center">
                    {{ $dehumBahanRecord->nomer_dehum_bahan }}
                </div>
            </div>
            
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                <div class="w-full h-10 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md flex items-center">
                    {{ \Carbon\Carbon::parse($dehumBahanRecord->bulan)->translatedFormat('F Y') }}
                </div>
            </div>
        </div>
    
        <!-- Tabel Inspeksi -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 text-sm">
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
                            4 => 'Temperatur kontrol',
                            5 => 'MCB',
                            6 => 'Dew Point'
                        ];
                    @endphp
                    
                    @foreach($items as $i => $item)
                        <tr class="bg-white">
                            <td class="border border-gray-300 p-1 text-center w-10">{{ $i }}</td>
                            <td class="border border-gray-300 p-1 text-center w-24">{{ $item }}</td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'check_1'}[$i] ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'keterangan_1'}[$i] ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'check_2'}[$i] ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'keterangan_2'}[$i] ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'check_3'}[$i] ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'keterangan_3'}[$i] ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'check_4'}[$i] ?? '' }}
                            </td>
                            <td class="border border-gray-300 p-1 text-center w-20">
                                {{ $dehumBahanRecord->{'keterangan_4'}[$i] ?? '' }}
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
                        
                        <!-- Week 1 Creator -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center mb-1">
                                {{ $dehumBahanRecord->created_by_1 ?? '-' }}
                            </div>
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                {{ $dehumBahanRecord->created_date_1 ? \Carbon\Carbon::parse($dehumBahanRecord->created_date_1)->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        
                        <!-- Week 2 Creator -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center mb-1">
                                {{ $dehumBahanRecord->created_by_2 ?? '-' }}
                            </div>
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                {{ $dehumBahanRecord->created_date_2 ? \Carbon\Carbon::parse($dehumBahanRecord->created_date_2)->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        
                        <!-- Week 3 Creator -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center mb-1">
                                {{ $dehumBahanRecord->created_by_3 ?? '-' }}
                            </div>
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                {{ $dehumBahanRecord->created_date_3 ? \Carbon\Carbon::parse($dehumBahanRecord->created_date_3)->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        
                        <!-- Week 4 Creator -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center mb-1">
                                {{ $dehumBahanRecord->created_by_4 ?? '-' }}
                            </div>
                            <div class="w-full px-2 py-1 text-sm bg-gray-100 border border-gray-300 rounded text-center">
                                {{ $dehumBahanRecord->created_date_4 ? \Carbon\Carbon::parse($dehumBahanRecord->created_date_4)->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                    </tr>
                </tbody>
                <!-- Tbody for Approval -->
                <tbody class="bg-white">
                    <tr class="bg-sky-50">
                        <td class="border border-gray-300 text-center p-2 bg-sky-50 h-12" rowspan="1">-</td>
                        <td class="border border-gray-300 p-2 font-medium bg-sky-50">Disetujui Oleh</td>
                        
                        <!-- Week 1 Approval -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div x-data="{ selected: {{ $dehumBahanRecord->approved_by_minggu1 ? 'true' : 'false' }}, userName: '{{ $dehumBahanRecord->approved_by_minggu1 ?? '' }}' }">
                                <button type="button" 
                                    @click="selected = !selected; 
                                        if(selected) {
                                            userName = '{{ Auth::user()->username }}'; 
                                            $refs.user1.value = userName;
                                            document.getElementById('hidden_approved_by_minggu1').value = userName;
                                        } else {
                                            userName = '';
                                            $refs.user1.value = '';
                                            document.getElementById('hidden_approved_by_minggu1').value = '';
                                        }"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                    :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                    <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                </button>
                                <div class="mt-2 space-y-1" x-show="selected">
                                    <input type="text" x-ref="user1" x-bind:value="userName"
                                        class="w-full px-2 py-1 text-sm bg-gray-100 border text-center border-gray-300 rounded"
                                        readonly>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Week 2 Approval -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div x-data="{ selected: {{ $dehumBahanRecord->approved_by_minggu2 ? 'true' : 'false' }}, userName: '{{ $dehumBahanRecord->approved_by_minggu2 ?? '' }}' }">
                                <button type="button" 
                                    @click="selected = !selected; 
                                        if(selected) {
                                            userName = '{{ Auth::user()->username }}'; 
                                            $refs.user2.value = userName;
                                            document.getElementById('hidden_approved_by_minggu2').value = userName;
                                        } else {
                                            userName = '';
                                            $refs.user2.value = '';
                                            document.getElementById('hidden_approved_by_minggu2').value = '';
                                        }"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                    :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                    <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                </button>
                                <div class="mt-2 space-y-1" x-show="selected">
                                    <input type="text" x-ref="user2" x-bind:value="userName"
                                        class="w-full px-2 py-1 text-sm bg-gray-100 border text-center border-gray-300 rounded"
                                        readonly>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Week 3 Approval -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div x-data="{ selected: {{ $dehumBahanRecord->approved_by_minggu3 ? 'true' : 'false' }}, userName: '{{ $dehumBahanRecord->approved_by_minggu3 ?? '' }}' }">
                                <button type="button" 
                                    @click="selected = !selected; 
                                        if(selected) {
                                            userName = '{{ Auth::user()->username }}'; 
                                            $refs.user3.value = userName;
                                            document.getElementById('hidden_approved_by_minggu3').value = userName;
                                        } else {
                                            userName = '';
                                            $refs.user3.value = '';
                                            document.getElementById('hidden_approved_by_minggu3').value = '';
                                        }"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                    :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                    <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                </button>
                                <div class="mt-2 space-y-1" x-show="selected">
                                    <input type="text" x-ref="user3" x-bind:value="userName"
                                        class="w-full px-2 py-1 text-sm bg-gray-100 border text-center border-gray-300 rounded"
                                        readonly>
                                </div>
                            </div>
                        </td>
                        
                        <!-- Week 4 Approval -->
                        <td colspan="2" class="border border-gray-300 p-2 bg-sky-50">
                            <div x-data="{ selected: {{ $dehumBahanRecord->approved_by_minggu4 ? 'true' : 'false' }}, userName: '{{ $dehumBahanRecord->approved_by_minggu4 ?? '' }}' }">
                                <button type="button" 
                                    @click="selected = !selected; 
                                        if(selected) {
                                            userName = '{{ Auth::user()->username }}'; 
                                            $refs.user4.value = userName;
                                            document.getElementById('hidden_approved_by_minggu4').value = userName;
                                        } else {
                                            userName = '';
                                            $refs.user4.value = '';
                                            document.getElementById('hidden_approved_by_minggu4').value = '';
                                        }"
                                    class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center"
                                    :class="selected ? 'bg-red-100 hover:bg-red-200' : 'bg-blue-100 hover:bg-blue-200'">
                                    <span x-text="selected ? 'Batal Pilih' : 'Pilih'"></span>
                                </button>
                                <div class="mt-2 space-y-1" x-show="selected">
                                    <input type="text" x-ref="user4" x-bind:value="userName"
                                        class="w-full px-2 py-1 text-sm bg-gray-100 border text-center border-gray-300 rounded"
                                        readonly>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    
        <div class="flex justify-between mt-6">
            <a href="{{ route('dehum-bahan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Kembali
            </a>
        
            <form id="approvalForm" method="POST" action="{{ route('dehum-bahan.approve', $dehumBahanRecord->id) }}" class="flex space-x-4">
                @csrf
                <!-- Hidden inputs for approval tracking -->
                <input type="hidden" id="hidden_approved_by_minggu1" name="approved_by_minggu1" value="{{ $dehumBahanRecord->approved_by_minggu1 }}">
                <input type="hidden" id="hidden_approved_by_minggu2" name="approved_by_minggu2" value="{{ $dehumBahanRecord->approved_by_minggu2 }}">
                <input type="hidden" id="hidden_approved_by_minggu3" name="approved_by_minggu3" value="{{ $dehumBahanRecord->approved_by_minggu3 }}">
                <input type="hidden" id="hidden_approved_by_minggu4" name="approved_by_minggu4" value="{{ $dehumBahanRecord->approved_by_minggu4 }}">
        
                @php
                    $isFullyApproved = $dehumBahanRecord->approved_by_minggu1 && 
                                      $dehumBahanRecord->approved_by_minggu2 && 
                                      $dehumBahanRecord->approved_by_minggu3 && 
                                      $dehumBahanRecord->approved_by_minggu4;
                @endphp
        
                @if(!$isFullyApproved)
                    <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded hover:bg-blue-800">
                        Setujui
                    </button>
                @else
                    <button type="button" class="bg-gray-400 text-white py-2 px-4 rounded cursor-not-allowed" disabled>
                        Telah Disetujui
                    </button>
        
                    <a href="{{ route('dehum-bahan.downloadPdf', $dehumBahanRecord->id) }}" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                        Download PDF
                    </a>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection