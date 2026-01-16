<?php

namespace App\Http\Controllers;

use App\Models\Criteria;
use Illuminate\Http\Request;

class CriteriaController extends Controller
{
    public function index()
    {
        $criteria = Criteria::orderBy('code')->paginate(10);
        return view('criteria.index', compact('criteria'));
    }

    public function create()
    {
        return view('criteria.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:1',
        ]);

        Criteria::create($validated);

        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function edit(Criteria $criterion)
    {
        return view('criteria.edit', ['criteria' => $criterion]);
    }

    public function update(Request $request, Criteria $criterion)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:criteria,code,' . $criterion->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:benefit,cost',
            'weight' => 'required|numeric|min:0|max:1',
        ]);

        $criterion->update($validated);

        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Criteria $criterion)
    {
        $criterion->delete();

        return redirect()->route('criteria.index')->with('success', 'Kriteria berhasil dihapus.');
    }
}
