@extends('layouts.app')

@section('title', 'Edit Kriteria')
@section('subtitle', 'Perbarui data kriteria')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">Form Edit Kriteria</h2>
        </div>
        
        <form action="{{ route('criteria.update', $criteria) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-sm font-medium text-slate-700 mb-2">Kode Kriteria</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $criteria->code) }}" required
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                        placeholder="C1, C2, dst...">
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="weight" class="block text-sm font-medium text-slate-700 mb-2">Bobot (0-1)</label>
                    <input type="number" name="weight" id="weight" value="{{ old('weight', $criteria->weight) }}" required step="0.01" min="0" max="1"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                        placeholder="0.25">
                    @error('weight')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama Kriteria</label>
                <input type="text" name="name" id="name" value="{{ old('name', $criteria->name) }}" required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all outline-none"
                    placeholder="Masukkan nama kriteria">
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Kriteria</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="benefit" {{ old('type', $criteria->type) === 'benefit' ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="text-sm text-slate-700">Benefit (semakin tinggi semakin baik)</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="type" value="cost" {{ old('type', $criteria->type) === 'cost' ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500">
                        <span class="text-sm text-slate-700">Cost (semakin rendah semakin baik)</span>
                    </label>
                </div>
                @error('type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Simpan Perubahan
                </button>
                <a href="{{ route('criteria.index') }}" class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 px-6 py-3 rounded-xl font-medium hover:bg-slate-200 transition-all">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
