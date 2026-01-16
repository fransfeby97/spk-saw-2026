@extends('layouts.app')

@section('title', 'Hasil Perhitungan SAW')
@section('subtitle', 'Peringkat pegawai berdasarkan metode Simple Additive Weighting')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="p-6 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-800">Hasil Perhitungan SAW</h2>
            
            @if($periods->isNotEmpty())
            <form method="GET" action="{{ route('assessment.result') }}" class="flex items-center gap-2">
                <label class="text-sm text-slate-600">Periode:</label>
                <select name="period_id" onchange="this.form.submit()" class="px-4 py-2 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none text-sm">
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}" {{ $selectedPeriod && $selectedPeriod->id == $period->id ? 'selected' : '' }}>
                            {{ $period->name }} {{ $period->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </form>
            @endif
        </div>
    </div>

    @if(empty($results))
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center text-slate-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <p class="font-medium text-lg mb-2">Belum ada data penilaian</p>
            <p class="text-sm">Silakan input penilaian terlebih dahulu untuk melihat hasil perhitungan SAW.</p>
            <a href="{{ route('assessment.index') }}" class="inline-flex items-center gap-2 mt-6 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/20">
                Input Penilaian
            </a>
        </div>
    @else
        <!-- Step 1: Matriks Keputusan (Raw Values) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-slate-50">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center font-bold text-sm">1</span>
                    <div>
                        <h3 class="font-semibold text-slate-800">Matriks Keputusan (Data Awal)</h3>
                        <p class="text-sm text-slate-500">Nilai mentah dari setiap alternatif terhadap kriteria</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Alternatif</th>
                            @foreach($criteria as $c)
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                {{ $c->code }}
                                <span class="block text-xs font-normal text-slate-400 capitalize">({{ $c->type }})</span>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($results as $result)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $result['employee']->name }}</td>
                            @foreach($criteria as $c)
                            <td class="px-6 py-4 text-center text-sm text-slate-600">
                                {{ number_format($result['raw'][$c->id] ?? 0, 2) }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        <!-- Max/Min Row -->
                        <tr class="bg-emerald-50 font-semibold">
                            <td class="px-6 py-4 text-sm text-emerald-700">MAX</td>
                            @foreach($criteria as $c)
                            <td class="px-6 py-4 text-center text-sm text-emerald-700">
                                {{ number_format($maxMinValues['max'][$c->id] ?? 0, 2) }}
                            </td>
                            @endforeach
                        </tr>
                        <tr class="bg-red-50 font-semibold">
                            <td class="px-6 py-4 text-sm text-red-700">MIN</td>
                            @foreach($criteria as $c)
                            <td class="px-6 py-4 text-center text-sm text-red-700">
                                {{ number_format($maxMinValues['min'][$c->id] ?? 0, 2) }}
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 2: Normalisasi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-slate-50">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-bold text-sm">2</span>
                    <div>
                        <h3 class="font-semibold text-slate-800">Matriks Normalisasi (R)</h3>
                        <p class="text-sm text-slate-500">Benefit: R = Nilai/Max | Cost: R = Min/Nilai</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Alternatif</th>
                            @foreach($criteria as $c)
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                {{ $c->code }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($results as $result)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $result['employee']->name }}</td>
                            @foreach($criteria as $c)
                            <td class="px-6 py-4 text-center text-sm text-slate-600">
                                {{ number_format($result['normalized'][$c->id] ?? 0, 4) }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 3: Preferensi (Weighted) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-amber-50 to-slate-50">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 bg-amber-600 text-white rounded-lg flex items-center justify-center font-bold text-sm">3</span>
                    <div>
                        <h3 class="font-semibold text-slate-800">Nilai Preferensi (V = R × W)</h3>
                        <p class="text-sm text-slate-500">Hasil normalisasi dikalikan dengan bobot kriteria</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Alternatif</th>
                            @foreach($criteria as $c)
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                {{ $c->code }}
                                <span class="block text-xs font-normal text-slate-400">(W = {{ $c->weight }})</span>
                            </th>
                            @endforeach
                            <th class="px-6 py-4 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider bg-blue-50">Σ (Total)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($results as $result)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $result['employee']->name }}</td>
                            @foreach($criteria as $c)
                            <td class="px-6 py-4 text-center text-sm text-slate-600">
                                {{ number_format($result['weighted'][$c->id] ?? 0, 4) }}
                            </td>
                            @endforeach
                            <td class="px-6 py-4 text-center font-bold text-blue-700 bg-blue-50">
                                {{ number_format($result['score'], 4) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Step 4: Hasil Perangkingan -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-purple-50 to-slate-50">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 bg-purple-600 text-white rounded-lg flex items-center justify-center font-bold text-sm">4</span>
                    <div>
                        <h3 class="font-semibold text-slate-800">Hasil Perangkingan</h3>
                        <p class="text-sm text-slate-500">Urutan alternatif berdasarkan nilai preferensi tertinggi</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">Ranking</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jabatan</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Nilai Akhir</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($results as $result)
                        <tr class="hover:bg-slate-50 transition-colors {{ $result['rank'] <= 3 ? 'bg-gradient-to-r from-amber-50/50 to-transparent' : '' }}">
                            <td class="px-6 py-4 text-center">
                                @if($result['rank'] === 1)
                                    <span class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-500 text-white rounded-full font-bold shadow-lg shadow-amber-400/30">
                                        🥇
                                    </span>
                                @elseif($result['rank'] === 2)
                                    <span class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br from-slate-300 to-slate-400 text-white rounded-full font-bold shadow-lg shadow-slate-400/30">
                                        🥈
                                    </span>
                                @elseif($result['rank'] === 3)
                                    <span class="inline-flex items-center justify-center w-10 h-10 bg-gradient-to-br from-amber-600 to-amber-700 text-white rounded-full font-bold shadow-lg shadow-amber-600/30">
                                        🥉
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-10 h-10 bg-slate-100 text-slate-600 rounded-full font-bold">
                                        {{ $result['rank'] }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800 text-base">{{ $result['employee']->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $result['employee']->position ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-800 rounded-xl font-bold text-lg">
                                    {{ number_format($result['score'], 4) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('assessment.print', ['employee' => $result['employee']->id, 'period_id' => $selectedPeriod->id]) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg text-sm font-medium hover:from-red-600 hover:to-red-700 transition-all shadow-md shadow-red-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Cetak PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Keterangan Formula -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Keterangan Rumus SAW:</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-200">
                    <p class="font-medium text-emerald-700 mb-2">Normalisasi Benefit</p>
                    <p class="text-emerald-600 font-mono">R = Xij / Max(Xij)</p>
                    <p class="text-xs text-emerald-500 mt-1">Semakin tinggi nilai, semakin baik</p>
                </div>
                <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                    <p class="font-medium text-red-700 mb-2">Normalisasi Cost</p>
                    <p class="text-red-600 font-mono">R = Min(Xij) / Xij</p>
                    <p class="text-xs text-red-500 mt-1">Semakin rendah nilai, semakin baik</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                    <p class="font-medium text-blue-700 mb-2">Nilai Preferensi</p>
                    <p class="text-blue-600 font-mono">V = Σ (Rij × Wj)</p>
                    <p class="text-xs text-blue-500 mt-1">Jumlah dari (normalisasi × bobot)</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection