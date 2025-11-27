<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::all();
        $stats = [
            'totalMaterials' => Material::count(),
            'totalInventory' => Material::sum('StokBahan'),
            'totalValue' => Material::selectRaw('SUM(StokBahan * HargaSatuan) as total')->first()->total ?? 0,
            'lowStock' => Material::whereColumn('StokBahan', '<=', 'MinimumStok')->count(),
        ];
        
        return view('inventorymaterial', compact('materials', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'NamaBahan' => 'required|string|max:100',
            'Kategori' => 'nullable|string|max:50',
            'StokBahan' => 'required|integer',
            'MinimumStok' => 'required|integer',
            'HargaSatuan' => 'nullable|numeric',
            'JenisBahan' => 'nullable|string|max:100',
        ]);

        Material::create($validated);

        return redirect()->route('inventorymaterial')
            ->with('success', 'Material berhasil ditambahkan');
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'NamaBahan' => 'required|string|max:100',
            'Kategori' => 'nullable|string|max:50',
            'StokBahan' => 'required|integer',
            'MinimumStok' => 'required|integer',
            'HargaSatuan' => 'nullable|numeric',
            'JenisBahan' => 'nullable|string|max:100',
        ]);

        $material->update($validated);

        return redirect()->route('inventorymaterial')
            ->with('success', 'Material berhasil diupdate');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('inventorymaterial')
            ->with('success', 'Material berhasil dihapus');
    }
}
