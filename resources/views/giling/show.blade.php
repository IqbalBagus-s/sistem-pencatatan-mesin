@extends('layouts.show-layout-1')

@section('title', 'Detail Pemeriksaan Mesin Giling')

@section('page-title', 'Detail Pemeriksaan Mesin Giling')

@section('back-route', route('giling.index'))

@section('approval-route', route('giling.approve', $check->id))

@section('content')
    <div class="bg-white p-4 rounded-lg shadow-md">
        <!-- Header Information -->
        @section('month-time')
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2 font-medium">Minggu ke- :</label>
                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    {{ $check->minggu }}
                </div>
            </div>
            <div>
                <label class="block mb-2 font-medium">Bulan:</label>
                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    @php
                        $date = new DateTime($check->bulan);
                        $formattedDate = $date->format('F Y');
                    @endphp
                    {{ $formattedDate }}
                </div>
            </div>
        </div>
        @endsection

        <!-- Check Information -->
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block mb-2 font-medium">Checker:</label>
                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    {{ $check->checked_by }}
                </div>
            </div>
            <div>
                <label class="block mb-2 font-medium">Approver:</label>
                <div class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md">
                    {{ $check->approved_by ?? 'Belum disetujui' }}
                </div>
            </div>
        </div>


        <!-- Table Data -->
        @section('table-content')
        <div class="overflow-x-auto max-h-[600px] mt-6">
            <table class="w-full border-collapse border border-gray-300">
                <thead class="sticky-header">
                    <tr>
                        <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-5 align-middle">No.</th>
                        <th rowspan="2" class="border border-gray-300 bg-sky-50 p-2 w-40 align-middle">Checked Items</th>
                        <th colspan="10" class="border border-gray-300 bg-sky-50 p-2 text-center">HASIL PEMERIKSAAN GILINGAN</th>
                    </tr>
                    <tr>
                        @for ($i = 1; $i <= 10; $i++)
                            <th class="border border-gray-300 bg-sky-50 p-2 text-center w-24">G{{ $i }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @php
                        $items = [
                            1 => 'Cek Motor Mesin Giling',
                            2 => 'Cek Vanbelt',
                            3 => 'Cek Dustcollector',
                            4 => 'Cek Safety Switch',
                            5 => 'Cek Ketajaman Pisau Putar dan Pisau Duduk'
                        ];
                    @endphp
                    
                    @foreach($items as $i => $item)
                        <tr>
                            <td class="border border-gray-300 text-center p-2">{{ $i }}</td>
                            <td class="border border-gray-300 p-2">{{ $item }}</td>
                            
                            @for ($g = 1; $g <= 10; $g++)
                                <td class="border border-gray-300 p-2 text-center">
                                    @php
                                        $result = isset($results[$item]) ? $results[$item] : null;
                                        $value = $result ? $result->{"g$g"} : '-';
                                    @endphp
                                    {{ $value }}
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endsection

        @section('machine-detail')
        <!-- Catatan Pemeriksaan -->
        <div class="bg-sky-50 p-4 rounded-md mt-6">
            <h5 class="mb-3 font-medium">Catatan Pemeriksaan:</h5>
            <p class="mb-1">- Pengecekan Ketajaman Pisau Putar dan Pisau Duduk DIlakukan Pada minggu ke-4 di setiap bulannya</p>
        </div>
        @endsection
        @section('approval-menu')
        <div class="mb-4 p-4 bg-white rounded shadow" 
                x-data="{
                    // Initial values from database 
                    dbApprover1: '{{ $check->approved_by1 }}',
                    dbApprover2: '{{ $check->approved_by2 }}',
                    dbApprovalDate1: '{{ $check->approval_date1 }}',
                    // Current form values (initially set to match database)
                    approver1: '{{ $check->approved_by1 }}',
                    approver2: '{{ $check->approved_by2 }}',
                    approvalDate1: '{{ $check->approval_date1 }}',
                    // Store the original database values for comparison
                    formChanged: false,
                    pilihApprover(position) {
                        const user = '{{ Auth::user()->username }}';
                        const currentDate = new Date().toISOString().split('T')[0]; // YYYY-MM-DD format
                        
                        if (position === 1) {
                            this.approver1 = user;
                            this.approvalDate1 = currentDate;
                        } else if (position === 2) {
                            this.approver2 = user;
                        }
                        this.updateFormChanged();
                    },
                    batalPilih(position) {
                        // Clear the fields completely
                        if (position === 1) {
                            this.approver1 = '';
                            this.approvalDate1 = '';
                        } else if (position === 2) {
                            this.approver2 = '';
                        }
                        this.updateFormChanged();
                    },
                    // Update the form changed status
                    updateFormChanged() {
                        this.formChanged = (this.approver1 !== this.dbApprover1) || 
                                          (this.approver2 !== this.dbApprover2) ||
                                          (this.approvalDate1 !== this.dbApprovalDate1);
                    },
                    // Check if form can be submitted
                    canSubmit() {
                        return this.formChanged && (this.approver1 !== '' || this.approver2 !== '');
                    }
                }">
            <p class="text-lg font-semibold text-gray-700 mb-3">Setujui Laporan</p>
            
            <div class="grid grid-cols-2 gap-4 mt-2">
                <!-- Approver 1 with date -->
                <div class="p-4 bg-white shadow rounded border border-gray-300">
                    <label class="block text-gray-700 font-semibold mb-3">Pelaksana Utility</label>
                    
                    <!-- Horizontal layout with two columns -->
                    <div class="grid grid-cols-2 gap-2 mb-3">
                        <!-- Username field -->
                        <div>
                            <input type="text" id="approved_by1" name="approved_by1" 
                                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                x-model="approver1" readonly placeholder="Nama">
                        </div>
                        
                        <!-- Date field -->
                        <div>
                            <input type="date" id="approval_date1" name="approval_date1" 
                                class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" 
                                x-model="approvalDate1" readonly>
                        </div>
                    </div>
                    
                    <!-- Conditional buttons -->
                    <button type="button" 
                        x-show="!approver1" 
                        class="w-full bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-700 cursor-pointer" 
                        @click="pilihApprover(1)">
                        Setujui Sebagai Pelaksana Utility
                    </button>
                    
                    <button type="button" 
                        x-show="approver1" 
                        class="w-full bg-red-500 text-white py-2 px-3 rounded hover:bg-red-600 cursor-pointer" 
                        @click="batalPilih(1)">
                        Batal Setujui
                    </button>
                </div>

                <!-- Approver 2 with full-width name field -->
                <div class="p-4 bg-white shadow rounded border border-gray-300">
                    <label class="block text-gray-700 font-semibold mb-3">Koordinator Staff Utility</label>
                    
                    <!-- Full-width name field -->
                    <div class="mb-3">
                        <input type="text" id="approved_by2" name="approved_by2" 
                            class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" 
                            x-model="approver2" readonly placeholder="Nama">
                    </div>
                    
                    <!-- Conditional buttons with same height as first section -->
                    <button type="button" 
                        x-show="!approver2" 
                        class="w-full bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-700 cursor-pointer" 
                        @click="pilihApprover(2)">
                        Setujui Sebagai Koordinator Staff Utility
                    </button>
                    
                    <button type="button" 
                        x-show="approver2" 
                        class="w-full bg-red-500 text-white py-2 px-3 rounded hover:bg-red-600 cursor-pointer" 
                        @click="batalPilih(2)">
                        Batal Setujui
                    </button>
                </div>
            </div>
            
            <!-- Hidden input for form submission -->
            <input type="hidden" name="approval_date1" x-model="approvalDate1">
</div>
        @endsection
    </div>
@endsection

@section('additional-scripts')
<style>
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endsection