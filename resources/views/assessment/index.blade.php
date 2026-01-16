@extends('layouts.app')

@section('title', 'Input Penilaian')
@section('subtitle', 'Input nilai pegawai berdasarkan kriteria')

@section('content')
    @if($periods->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-6 py-8 rounded-2xl text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-amber-400" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="font-medium">Belum ada periode penilaian.</p>
            <a href="{{ route('periods.create') }}" class="inline-flex items-center gap-2 mt-4 text-amber-800 underline">Tambah
                periode sekarang</a>
        </div>
    @elseif($employees->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-6 py-8 rounded-2xl text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-amber-400" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="font-medium">Belum ada data pegawai.</p>
            <a href="{{ route('employees.create') }}"
                class="inline-flex items-center gap-2 mt-4 text-amber-800 underline">Tambah pegawai sekarang</a>
        </div>
    @elseif($criteria->isEmpty())
        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-6 py-8 rounded-2xl text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-amber-400" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="font-medium">Belum ada kriteria penilaian.</p>
            <a href="{{ route('criteria.create') }}" class="inline-flex items-center gap-2 mt-4 text-amber-800 underline">Tambah
                kriteria sekarang</a>
        </div>
    @else
        <div class="space-y-4">
            <!-- Add Employee Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
                <div class="flex items-center gap-4 flex-wrap">
                    <label class="text-sm font-medium text-slate-700">Tambah Pegawai:</label>
                    <select id="employeeSelector" class="flex-1 min-w-[200px] px-4 py-2.5 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" data-name="{{ $emp->name }}" data-position="{{ $emp->position ?? '' }}">
                                {{ $emp->name }} {{ $emp->position ? '('.$emp->position.')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" onclick="addEmployee()" class="inline-flex items-center gap-2 bg-emerald-600 text-white px-4 py-2.5 rounded-xl font-medium text-sm hover:bg-emerald-700 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Tambah
                    </button>
                </div>
            </div>

            <!-- Main Assessment Form -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">Input Nilai Pegawai</h2>

                    <!-- Period Selector -->
                    <form method="GET" action="{{ route('assessment.index') }}" class="flex items-center gap-2">
                        <label class="text-sm text-slate-600">Periode:</label>
                        <select name="period_id" onchange="this.form.submit()"
                            class="px-4 py-2 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm">
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}" {{ $selectedPeriod && $selectedPeriod->id == $period->id ? 'selected' : '' }}>
                                    {{ $period->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <form action="{{ route('assessment.store') }}" method="POST" id="assessmentForm">
                    @csrf
                    <input type="hidden" name="period_id" value="{{ $selectedPeriod->id }}">

                    <div class="overflow-x-auto">
                        <table class="w-full" id="assessmentTable">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider sticky left-0 bg-slate-50 min-w-[200px]">
                                        Pegawai</th>
                                    @foreach($criteria as $c)
                                        <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            <div>{{ $c->code }}</div>
                                            <div class="font-normal text-slate-400 normal-case">{{ $c->name }}</div>
                                        </th>
                                    @endforeach
                                    <th class="px-4 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100" id="employeeTableBody">
                                @php
                                    // Only show employees that have ratings for this period, or all if no ratings yet
                                    $employeesWithRatings = !empty($ratings) ? $employees->filter(fn($e) => isset($ratings[$e->id])) : $employees;
                                    if($employeesWithRatings->isEmpty()) $employeesWithRatings = $employees;
                                @endphp
                                @foreach($employeesWithRatings as $employee)
                                    <tr class="hover:bg-slate-50 transition-colors employee-row" data-employee-id="{{ $employee->id }}">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-800 sticky left-0 bg-white">
                                            {{ $employee->name }}
                                            @if($employee->position)
                                                <span class="block text-xs text-slate-400">{{ $employee->position }}</span>
                                            @endif
                                        </td>
                                        @foreach($criteria as $c)
                                            <td class="px-6 py-4">
                                                <input type="number" name="ratings[{{ $employee->id }}][{{ $c->id }}]"
                                                    value="{{ $ratings[$employee->id][$c->id] ?? '' }}" step="0.01" min="0" required
                                                    class="w-24 px-3 py-2 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-center text-sm">
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-4 text-center">
                                            <button type="button" onclick="removeEmployee(this, {{ $employee->id }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus dari penilaian">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div id="emptyState" class="hidden p-12 text-center text-slate-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="font-medium">Belum ada pegawai yang dipilih</p>
                        <p class="text-sm mt-1">Gunakan dropdown di atas untuk menambahkan pegawai yang akan dinilai</p>
                    </div>

                    <div class="p-6 border-t border-slate-100 flex items-center gap-3">
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            Simpan & Hitung SAW
                        </button>
                        <a href="{{ route('assessment.result', ['period_id' => $selectedPeriod->id]) }}"
                            class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 px-6 py-3 rounded-xl font-medium hover:bg-emerald-100 transition-all border border-emerald-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm2 10a1 1 0 10-2 0v3a1 1 0 102 0v-3zm2-3a1 1 0 011 1v5a1 1 0 11-2 0v-5a1 1 0 011-1zm4-1a1 1 0 10-2 0v7a1 1 0 102 0V8z"
                                    clip-rule="evenodd" />
                            </svg>
                            Lihat Hasil
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <script>
            const criteriaData = @json($criteria);
            const ratingsData = @json($ratings);
            
            function updateEmptyState() {
                const tbody = document.getElementById('employeeTableBody');
                const emptyState = document.getElementById('emptyState');
                const table = document.getElementById('assessmentTable');
                
                if (tbody.children.length === 0) {
                    emptyState.classList.remove('hidden');
                    table.classList.add('hidden');
                } else {
                    emptyState.classList.add('hidden');
                    table.classList.remove('hidden');
                }
            }

            function removeEmployee(btn, employeeId) {
                const row = btn.closest('tr');
                row.remove();
                
                // Show the employee back in dropdown
                const selector = document.getElementById('employeeSelector');
                const option = selector.querySelector(`option[value="${employeeId}"]`);
                if (option) {
                    option.disabled = false;
                    option.classList.remove('hidden');
                }
                
                updateEmptyState();
            }

            function addEmployee() {
                const selector = document.getElementById('employeeSelector');
                const selectedOption = selector.options[selector.selectedIndex];
                
                if (!selectedOption.value) {
                    alert('Pilih pegawai terlebih dahulu');
                    return;
                }

                const employeeId = selectedOption.value;
                const employeeName = selectedOption.dataset.name;
                const employeePosition = selectedOption.dataset.position;

                // Check if already exists
                if (document.querySelector(`tr[data-employee-id="${employeeId}"]`)) {
                    alert('Pegawai sudah ada dalam daftar');
                    return;
                }

                // Create new row
                const tbody = document.getElementById('employeeTableBody');
                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50 transition-colors employee-row';
                row.dataset.employeeId = employeeId;

                let cellsHtml = `
                    <td class="px-6 py-4 text-sm font-medium text-slate-800 sticky left-0 bg-white">
                        ${employeeName}
                        ${employeePosition ? `<span class="block text-xs text-slate-400">${employeePosition}</span>` : ''}
                    </td>
                `;

                criteriaData.forEach(c => {
                    const existingValue = ratingsData[employeeId]?.[c.id] ?? '';
                    cellsHtml += `
                        <td class="px-6 py-4">
                            <input type="number" name="ratings[${employeeId}][${c.id}]"
                                value="${existingValue}" step="0.01" min="0" required
                                class="w-24 px-3 py-2 rounded-lg border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-center text-sm">
                        </td>
                    `;
                });

                cellsHtml += `
                    <td class="px-4 py-4 text-center">
                        <button type="button" onclick="removeEmployee(this, ${employeeId})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus dari penilaian">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </td>
                `;

                row.innerHTML = cellsHtml;
                tbody.appendChild(row);

                // Disable option in dropdown
                selectedOption.disabled = true;
                selectedOption.classList.add('hidden');
                selector.value = '';

                updateEmptyState();
            }

            // Mark existing employees as disabled in dropdown on load
            document.addEventListener('DOMContentLoaded', function() {
                const existingRows = document.querySelectorAll('.employee-row');
                const selector = document.getElementById('employeeSelector');
                
                existingRows.forEach(row => {
                    const id = row.dataset.employeeId;
                    const option = selector.querySelector(`option[value="${id}"]`);
                    if (option) {
                        option.disabled = true;
                        option.classList.add('hidden');
                    }
                });
                
                updateEmptyState();
            });
        </script>
    @endif
@endsection