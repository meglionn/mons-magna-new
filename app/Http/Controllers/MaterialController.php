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
               'totalValue' => Material::sum('TotalNilaiInventori'), // Use the new column
            'lowStock' => Material::whereColumn('StokBahan', '<=', 'MinimumStok')->count(),
        ];
        
        return view('inventorymaterial', compact('materials', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'NamaBahan' => 'required|string|max:100',
            'Kategori' => 'nullable|string|max:50',
            'StokBahan' => 'required|integer|min:0',
            'MinimumStok' => 'required|integer|min:0',
            'HargaSatuan' => 'nullable|numeric|min:0',
            'JenisBahan' => 'nullable|string|max:100',
        ]);

        // Calculate total inventory value for new material
        $totalValue = ($validated['StokBahan'] ?? 0) * ($validated['HargaSatuan'] ?? 0);
        $validated['TotalNilaiInventori'] = $totalValue;

        Material::create($validated);

        return redirect()->route('inventorymaterial')
            ->with('success', 'Material berhasil ditambahkan');
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'NamaBahan' => 'required|string|max:100',
            'Kategori' => 'nullable|string|max:50',
            'StokBahan' => 'required|integer|min:0',
            'MinimumStok' => 'required|integer|min:0',
            'HargaSatuan' => 'nullable|numeric|min:0',
            'JenisBahan' => 'nullable|string|max:100',
            'jumlahBeli' => 'nullable|integer|min:0',
            'hargaBeliTerbaru' => 'nullable|numeric|min:0',
            'editMode' => 'nullable|string',
        ]);

        // EDIT MODE: Weighted average price calculation
        if (($validated['editMode'] ?? '') === 'update_stock' && !empty($validated['jumlahBeli'])) {
            $oldStok = $material->StokBahan;
            $oldHarga = $material->HargaSatuan ?? 0;
            $oldTotalValue = $material->TotalNilaiInventori ?? 0;
            
            $jumlahBeliTerbaru = $validated['jumlahBeli'];
            $hargaBeliTerbaru = $validated['hargaBeliTerbaru'] ?? $oldHarga;
            
            // Calculate new total stock
            $newStok = $oldStok + $jumlahBeliTerbaru;
            
            // Calculate new total value (weighted average)
            $nilaiBeliTerbaru = $jumlahBeliTerbaru * $hargaBeliTerbaru;
            $newTotalValue = $oldTotalValue + $nilaiBeliTerbaru;
            
            // Calculate new unit price (weighted average)
            $newHargaSatuan = $newStok > 0 ? $newTotalValue / $newStok : 0;
            
            $validated['StokBahan'] = $newStok;
            $validated['HargaSatuan'] = round($newHargaSatuan, 2);
            $validated['TotalNilaiInventori'] = round($newTotalValue, 2);
            
            // Create a purchase record for audit/logging
            try {
                \App\Models\MaterialPurchase::create([
                    'MaterialID' => $material->MaterialID,
                    'Jumlah' => $jumlahBeliTerbaru,
                    'HargaSatuan' => $hargaBeliTerbaru,
                    'Total' => $nilaiBeliTerbaru,
                    'Tanggal' => now(),
                    'CreatedBy' => auth()->id(),
                ]);
            } catch (\Exception $ex) {
                // If purchase table doesn't exist or insert fails, ignore and continue update
            }
        } else {
            // DIRECT EDIT MODE: Just update the values
            $totalValue = ($validated['StokBahan'] ?? 0) * ($validated['HargaSatuan'] ?? 0);
            $validated['TotalNilaiInventori'] = $totalValue;
        }

        // Remove temporary fields
        unset($validated['jumlahBeli']);
        unset($validated['hargaBeliTerbaru']);
        unset($validated['editMode']);

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
