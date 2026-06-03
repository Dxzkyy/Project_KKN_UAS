<?php

namespace App\Http\Controllers;

use App\Models\BahanJadi;
use Illuminate\Http\Request;

class BahanJadiController extends Controller
{
    public function index()
    {
        $bahans = BahanJadi::latest()->get();
        return view('owner.bahan_jadi.index', compact('bahans'));
    }

    public function create()
    {
        return view('owner.bahan_jadi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:bahan_jadis,nama_bahan',
            'stok'       => 'required|numeric|min:0',
            'satuan'     => 'required|in:gram,mililiter',
        ]);

        BahanJadi::create($request->only('nama_bahan', 'stok', 'satuan'));

        return redirect()->route('owner.bahan_jadi.index')
                         ->with('success', 'Bahan berhasil ditambahkan!');
    }

    public function edit(BahanJadi $bahanJadi)
    {
        return view('owner.bahan_jadi.edit', compact('bahanJadi'));
    }

    public function update(Request $request, BahanJadi $bahanJadi)
    {
        $request->validate([
            'nama_bahan' => 'required|string|max:255|unique:bahan_jadis,nama_bahan,' . $bahanJadi->id,
            'stok'       => 'required|numeric|min:0',
            'satuan'     => 'required|in:gram,mililiter',
        ]);

        $bahanJadi->update($request->only('nama_bahan', 'stok', 'satuan'));

        return redirect()->route('owner.bahan_jadi.index')
                         ->with('success', 'Bahan berhasil diupdate!');
    }

    public function destroy(BahanJadi $bahanJadi)
    {
        $bahanJadi->delete();

        return redirect()->route('owner.bahan_jadi.index')
                         ->with('success', 'Bahan berhasil dihapus!');
    }

    // Update stok harian
    public function updateStok(Request $request, BahanJadi $bahanJadi)
    {
        $request->validate([
            'stok' => 'required|numeric|min:0',
        ]);

        $bahanJadi->update(['stok' => $request->stok]);

        return redirect()->route('owner.bahan_jadi.index')
                         ->with('success', 'Stok ' . $bahanJadi->nama_bahan . ' berhasil diupdate!');
    }
}