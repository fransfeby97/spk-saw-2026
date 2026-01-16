@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan data sistem penilaian kinerja')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Stat Card: Pegawai -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Jumlah Pegawai</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $employeeCount }}</p>
                </div>
            </div>
        </div>

        <!-- Stat Card: Kriteria -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Jumlah Kriteria</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $criteriaCount }}</p>
                </div>
            </div>
        </div>

        <!-- Stat Card: Periode -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 font-medium">Jumlah Periode</p>
                    <p class="text-3xl font-bold text-slate-800">{{ $periodCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
        <h2 class="text-xl font-bold text-blue-800 mb-4">SPK Penilaian Kinerja Pegawai</h2>
        <p class="text-slate-600 leading-relaxed">
            Sistem Pendukung Keputusan (SPK) Penilaian Kinerja Pegawai menggunakan metode <strong>Simple Additive Weighting
                (SAW)</strong>
            adalah suatu sistem yang membantu dalam proses pengambilan keputusan terkait penilaian kinerja pegawai.
            Metode SAW adalah metode penilaian kinerja dengan memberikan bobot pada setiap kriteria,
            kemudian menjumlahkan nilai kriteria yang telah dinormalisasi untuk mendapatkan nilai akhir.
        </p>

        @if($activePeriod)
            <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-sm text-blue-700">
                    <span class="font-semibold">Periode Aktif:</span> {{ $activePeriod->name }}
                </p>
            </div>
        @else
            <div class="mt-6 p-4 bg-amber-50 rounded-xl border border-amber-100">
                <p class="text-sm text-amber-700">
                    <span class="font-semibold">Perhatian:</span> Belum ada periode aktif. Silakan tambahkan periode terlebih
                    dahulu.
                </p>
            </div>
        @endif
    </div>
@endsection