<!-- resources/views/autoloader/show.blade.php -->
@extends('layouts.show-layout-2')

@section('title', 'Detail Pencatatan Mesin Autoloader')

@section('content')
<h2 class="mb-4 text-xl font-bold">Detail Pencatatan Mesin Autoloader</h2>

<div class="bg-white rounded-lg shadow-md mb-5">
    <div class="p-4">
        <form method="POST" action="{{ route('autoloader.approve', $check->id) }}" id="approveForm">
            @csrf
            <!-- Menampilkan Nama Checker -->
            <div class="bg-sky-50 p-4 rounded-md mb-5">
                <span class="text-gray-600 font-bold">Checker: </span>
                <span class="font-bold text-blue-700">
                    @php
                        // Extract all unique checker names from the results collection
                        $checkers = $results->pluck('checked_by')->filter()->unique()->implode(', ');
                    @endphp
                    {{ $checkers ?: 'Belum ada checker' }}
                </span>
            </div>

            <!-- Info Display -->
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <!-- No Autoloader Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">No Autoloader:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Autoloader {{ $check->nomer_autoloader }}
                    </div>
                </div>
                
                <!-- Shift Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Shift:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        Shift {{ $check->shift }}
                    </div>
                </div>

                <!-- Bulan Display -->
                <div class="w-full">
                    <label class="block mb-2 text-sm font-medium text-gray-700">Bulan:</label>
                    <div class="w-full h-10 px-3 py-2 bg-white border border-blue-400 rounded-md text-sm flex items-center">
                        {{ \Carbon\Carbon::parse($check->bulan)->translatedFormat('F Y') }}
                    </div>
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

                // Opsi check dengan warna
                $options = [
                    'V' => '<span class="text-green-600 font-bold">V</span>',
                    'X' => '<span class="text-red-600 font-bold">X</span>',
                    '-' => '<span class="text-gray-600">—</span>',
                    'OFF' => '<span class="text-gray-600">OFF</span>'
                ];
                
                // Helper function untuk mendapatkan hasil check berdasarkan tanggal dan item
                function getCheckResult($results, $date, $itemId) {
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    return $result && isset($result['result']) ? $result['result'] : null;
                }

                // Helper function untuk mendapatkan keterangan berdasarkan tanggal dan item
                function getKeterangan($results, $date, $itemId) {
                    $result = $results->where('tanggal', $date)->where('item_id', $itemId)->first();
                    return $result && isset($result['keterangan']) ? $result['keterangan'] : '';
                }

                // Helper function untuk mendapatkan checker username berdasarkan tanggal
                function getCheckerUsername($results, $date) {
                    $result = $results->where('tanggal', $date)->first();
                    return $result && isset($result['checker_username']) ? $result['checker_username'] : '';
                }
                
                // Helper function untuk mendapatkan approver username berdasarkan tanggal
                function getApproverUsername($results, $date) {
                    $result = $results->where('tanggal', $date)->first();
                    return $result && isset($result['approver_username']) ? $result['approver_username'] : '';
                }

                // Helper function untuk mendapatkan checker_id berdasarkan tanggal (untuk input hidden)
                function getCheckerId($results, $date) {
                    $result = $results->where('tanggal', $date)->first();
                    return $result && isset($result['checker_id']) ? $result['checker_id'] : '';
                }
                
                // Helper function untuk mendapatkan approver_id berdasarkan tanggal (untuk input hidden)
                function getApproverId($results, $date) {
                    $result = $results->where('tanggal', $date)->first();
                    return $result && isset($result['approver_id']) ? $result['approver_id'] : '';
                }

                // Helper function untuk memeriksa apakah ada data checker pada tanggal tertentu
                function hasCheckerData($results, $date) {
                    $result = $results->where('tanggal', $date)->first();
                    return $result && !empty($result['checker_id']);
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
                                        @php
                                            $hasChecker = hasCheckerData($results, $j);
                                        @endphp
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @if ($hasChecker)
                                                @php
                                                    $result = getCheckResult($results, $j, $i);
                                                @endphp
                                                {!! isset($options[$result]) ? $options[$result] : '<span class="text-gray-600">—</span>' !!}
                                            @else
                                                <span class="text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            @if ($hasChecker)
                                                {{ getKeterangan($results, $j, $i) ?: '-' }}
                                            @else
                                                <span class="text-gray-600 italic text-xs">Belum diisi</span>
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
                                
                                @for($j = 1; $j <= 11; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        @php
                                            $checkerUsername = getCheckerUsername($results, $j);
                                        @endphp
                                        {{ $checkerUsername ?: '-' }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 1--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 1; $j <= 11; $j++)
                                    @php
                                        $approverUsername = getApproverUsername($results, $j);
                                        $approverId = getApproverId($results, $j);
                                    @endphp
                                    <td colspan="2" class="border border-gray-300 p-1 bg-green-50 text-center text-sm">
                                        @if($approverId)
                                            <div class="w-full px-2 py-1 text-sm text-center">
                                                {{ $approverUsername }}
                                            </div>
                                        @else
                                            <div x-data="{ selected: false, userId: '', userUsername: '' }">
                                                <div x-show="!selected">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userId = '{{ $user->id }}'; 
                                                            userUsername = '{{ $user->username }}';
                                                            $refs.approverDisplay{{ $j }}.value = userUsername;
                                                            $refs.approver{{ $j }}.value = userId;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div class="mt-1" x-show="selected">
                                                    <input type="text" name="approver_display_{{ $j }}" x-ref="approverDisplay{{ $j }}" x-bind:value="userUsername"
                                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                        readonly>
                                                    <input type="hidden" name="approver_id_{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userId">
                                                    <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="{{ $j }}">
                                                    <button type="button" 
                                                        @click="selected = false; 
                                                            userId = ''; 
                                                            userUsername = '';
                                                            $refs.approverDisplay{{ $j }}.value = '';
                                                            $refs.approver{{ $j }}.value = '';
                                                            $refs.approveNum{{ $j }}.value = '';"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                        Batal Pilih
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
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
                                        @php
                                            $hasChecker = hasCheckerData($results, $j);
                                        @endphp
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @if ($hasChecker)
                                                @php
                                                    $result = getCheckResult($results, $j, $i);
                                                @endphp
                                                {!! isset($options[$result]) ? $options[$result] : '<span class="text-gray-600">—</span>' !!}
                                            @else
                                                <span class="text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            @if ($hasChecker)
                                                {{ getKeterangan($results, $j, $i) ?: '-' }}
                                            @else
                                                <span class="text-gray-600 italic text-xs">Belum diisi</span>
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
                                
                                @for($j = 12; $j <= 22; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        @php
                                            $checkerUsername = getCheckerUsername($results, $j);
                                        @endphp
                                        {{ $checkerUsername ?: '-' }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 2--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 12; $j <= 22; $j++)
                                    @php
                                        $approverUsername = getApproverUsername($results, $j);
                                        $approverId = getApproverId($results, $j);
                                    @endphp
                                    <td colspan="2" class="border border-gray-300 p-1 bg-green-50 text-center text-sm">
                                        @if($approverId)
                                            <div class="w-full px-2 py-1 text-sm text-center">
                                                {{ $approverUsername }}
                                            </div>
                                        @else
                                            <div x-data="{ selected: false, userId: '', userUsername: '' }">
                                                <div x-show="!selected">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userId = '{{ $user->id }}'; 
                                                            userUsername = '{{ $user->username }}';
                                                            $refs.approverDisplay{{ $j }}.value = userUsername;
                                                            $refs.approver{{ $j }}.value = userId;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div class="mt-1" x-show="selected">
                                                    <input type="text" name="approver_display_{{ $j }}" x-ref="approverDisplay{{ $j }}" x-bind:value="userUsername"
                                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                        readonly>
                                                    <input type="hidden" name="approver_id_{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userId">
                                                    <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="{{ $j }}">
                                                    <button type="button" 
                                                        @click="selected = false; 
                                                            userId = ''; 
                                                            userUsername = '';
                                                            $refs.approverDisplay{{ $j }}.value = '';
                                                            $refs.approver{{ $j }}.value = '';
                                                            $refs.approveNum{{ $j }}.value = '';"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                        Batal Pilih
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
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
                                        @php
                                            $hasChecker = hasCheckerData($results, $j);
                                        @endphp
                                        <td class="border border-gray-300 p-1 h-10 text-center">
                                            @if ($hasChecker)
                                                @php
                                                    $result = getCheckResult($results, $j, $i);
                                                @endphp
                                                {!! isset($options[$result]) ? $options[$result] : '<span class="text-gray-600">—</span>' !!}
                                            @else
                                                <span class="text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 p-1 h-10 text-sm">
                                            @if ($hasChecker)
                                                {{ getKeterangan($results, $j, $i) ?: '-' }}
                                            @else
                                                <span class="text-gray-600 italic text-xs">Belum diisi</span>
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
                                
                                @for($j = 23; $j <= 31; $j++)
                                    <td colspan="2" class="border border-gray-300 p-1 bg-sky-50 text-center text-sm">
                                        @php
                                            $checkerUsername = getCheckerUsername($results, $j);
                                        @endphp
                                        {{ $checkerUsername ?: '-' }}
                                    </td>
                                @endfor
                            </tr>
                        </tbody>
                        {{-- baris penanggung jawab tabel 3--}}
                        <tbody class="bg-white">
                            <tr class="bg-green-50">
                                <td class="border border-gray-300 text-center p-1 bg-green-50 h-10 text-xs sticky left-0 z-10" rowspan="1">-</td>
                                <td class="border border-gray-300 p-1 font-medium bg-green-50 text-xs sticky left-10 z-10">Penanggung Jawab</td>
                                
                                @for($j = 23; $j <= 31; $j++)
                                    @php
                                        $approverUsername = getApproverUsername($results, $j);
                                        $approverId = getApproverId($results, $j);
                                    @endphp
                                    <td colspan="2" class="border border-gray-300 p-1 bg-green-50 text-center text-sm">
                                        @if($approverId)
                                            <div class="w-full px-2 py-1 text-sm text-center">
                                                {{ $approverUsername }}
                                            </div>
                                        @else
                                            <div x-data="{ selected: false, userId: '', userUsername: '' }">
                                                <div x-show="!selected">
                                                    <button type="button" 
                                                        @click="selected = true; 
                                                            userId = '{{ $user->id }}'; 
                                                            userUsername = '{{ $user->username }}';
                                                            $refs.approverDisplay{{ $j }}.value = userUsername;
                                                            $refs.approver{{ $j }}.value = userId;
                                                            $refs.approveNum{{ $j }}.value = '{{ $j }}';"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded text-center bg-green-100 hover:bg-green-200">
                                                        Pilih
                                                    </button>
                                                </div>
                                                <div class="mt-1" x-show="selected">
                                                    <input type="text" name="approver_display_{{ $j }}" x-ref="approverDisplay{{ $j }}" x-bind:value="userUsername"
                                                        class="w-full px-2 py-1 text-sm bg-white border border-gray-300 rounded text-center mb-1"
                                                        readonly>
                                                    <input type="hidden" name="approver_id_{{ $j }}" x-ref="approver{{ $j }}" x-bind:value="userId">
                                                    <input type="hidden" name="approve_num_{{ $j }}" x-ref="approveNum{{ $j }}" value="{{ $j }}">
                                                    <button type="button" 
                                                        @click="selected = false; 
                                                            userId = ''; 
                                                            userUsername = '';
                                                            $refs.approverDisplay{{ $j }}.value = '';
                                                            $refs.approver{{ $j }}.value = '';
                                                            $refs.approveNum{{ $j }}.value = '';"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded text-center bg-red-100 hover:bg-red-200">
                                                        Batal Pilih
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
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
            
            <!-- Button Controls -->
            <div class="flex justify-between mt-6">
                <a href="{{ route('autoloader.index') }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
                
                <!-- Action Buttons - Right Side -->
                <div class="flex flex-row flex-wrap gap-2 justify-end">
                    <!-- Hitung jumlah hari dalam bulan -->
                    @php
                        $year = substr($check->bulan, 0, 4);
                        $month = substr($check->bulan, 5, 2);
                        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$month, (int)$year);
                        // Hitung jumlah tanggal yang sudah di-approve berdasarkan approver_id
                        $approvedDatesCount = $results->where('approver_id', '!=', null)
                                                     ->where('approver_id', '!=', '')
                                                     ->unique('tanggal')
                                                     ->count();
                    @endphp
                    
                    <!-- Conditional rendering based on approval status -->
                    @if($approvedDatesCount < $daysInMonth)
                        <!-- Tombol Setujui untuk yang belum disetujui atau disetujui sebagian -->
                        <button type="submit" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui
                        </button>
                    @else
                        <!-- PDF Preview Button -->
                        <a href="{{ route('autoloader.pdf', $check->id) }}" target="_blank" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview PDF
                        </a>
                        
                        <!-- Download PDF Button -->
                        <a href="{{ route('autoloader.downloadPdf', $check->id) }}" class="flex items-center justify-center text-xs sm:text-sm md:text-base px-3 sm:px-4 md:px-5 py-1.5 sm:py-2 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-300 ease-in-out">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5 mr-1 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download PDF
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection