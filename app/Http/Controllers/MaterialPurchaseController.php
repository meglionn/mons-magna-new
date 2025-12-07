<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialPurchaseController extends Controller
{
    public function index()
    {
        $purchases = MaterialPurchase::with('material')->orderBy('Tanggal', 'desc')->get();
        return view('keuangan.material_purchases', compact('purchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MaterialID' => 'required|exists:materials,MaterialID',
            'Jumlah' => 'required|integer|min:1',
            'HargaSatuan' => 'required|numeric|min:0',
            'Supplier' => 'nullable|string|max:191',
            'Catatan' => 'nullable|string',
            'Tanggal' => 'nullable|date',
        ]);

        $material = Material::findOrFail($validated['MaterialID']);

        $jumlahBeli = (int) $validated['Jumlah'];
        $hargaBeli = (float) $validated['HargaSatuan'];
        $nilaiBeli = $jumlahBeli * $hargaBeli;

        // create purchase record
        $purchase = MaterialPurchase::create([
            'MaterialID' => $material->MaterialID,
            'Jumlah' => $jumlahBeli,
            'HargaSatuan' => $hargaBeli,
            'Total' => $nilaiBeli,
            'Supplier' => $validated['Supplier'] ?? null,
            'Tanggal' => $validated['Tanggal'] ?? now(),
            'CreatedBy' => Auth::id(),
            'Catatan' => $validated['Catatan'] ?? null,
        ]);

        // Update material using weighted average logic
        $oldStok = $material->StokBahan;
        $oldTotalValue = $material->TotalNilaiInventori ?? ($material->StokBahan * ($material->HargaSatuan ?? 0));

        $newStok = $oldStok + $jumlahBeli;
        $newTotalValue = $oldTotalValue + $nilaiBeli;
        $newHarga = $newStok > 0 ? round($newTotalValue / $newStok, 2) : 0;

        $material->update([
            'StokBahan' => $newStok,
            'HargaSatuan' => $newHarga,
            'TotalNilaiInventori' => $newTotalValue,
        ]);

        return redirect()->back()->with('success', 'Pembelian berhasil dicatat dan stok diperbarui');
    }
}
