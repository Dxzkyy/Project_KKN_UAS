<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\BahanJadi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('bahanJadi')->latest()->paginate(10);
        return view('owner.menu.index', compact('menus'));
    }

    public function create()
    {
        $bahans = BahanJadi::all();
        return view('owner.menu.create', compact('bahans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori'    => 'required|in:Makanan,Minuman,Lainnya',
            'harga'       => 'required|numeric|min:0',
            'foto'        => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Upload foto
        $foto = $request->file('foto');
        $fotoName = time() . '_' . Str::slug($request->nama_produk) . '.' . $foto->extension();
        $foto->storeAs('public/menus', $fotoName);

        // Generate kode produk otomatis
        $lastMenu = Menu::latest()->first();
        $number = $lastMenu ? (intval(substr($lastMenu->kode_produk, 4)) + 1) : 1;
        $kodeProduk = 'BRG-' . str_pad($number, 3, '0', STR_PAD_LEFT);

        $menu = Menu::create([
            'kode_produk' => $kodeProduk,
            'nama_produk' => $request->nama_produk,
            'kategori'    => $request->kategori,
            'harga'       => $request->harga,
            'stok'        => 0, // default 0, nanti dihitung otomatis
            'foto'        => $fotoName,
        ]);

        // Simpan resep bahan
        if ($request->has('bahan') && is_array($request->bahan)) {
            $resep = [];
            foreach ($request->bahan as $bahan_id => $kebutuhan) {
                if (!empty($kebutuhan) && $kebutuhan > 0) {
                    $resep[$bahan_id] = ['kebutuhan' => $kebutuhan];
                }
            }
            $menu->bahanJadi()->sync($resep);
        }

        return redirect()->route('owner.menu.index')
                         ->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        $bahans = BahanJadi::all();
        $resep = $menu->bahanJadi->keyBy('id');
        return view('owner.menu.edit', compact('menu', 'bahans', 'resep'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori'    => 'required|in:Makanan,Minuman,Lainnya',
            'harga'       => 'required|numeric|min:0',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $fotoName = $menu->foto;

        if ($request->hasFile('foto')) {
            Storage::delete('public/menus/' . $menu->foto);
            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($request->nama_produk) . '.' . $foto->extension();
            $foto->storeAs('public/menus', $fotoName);
        }

        $menu->update([
            'nama_produk' => $request->nama_produk,
            'kategori'    => $request->kategori,
            'harga'       => $request->harga,
            'foto'        => $fotoName,
        ]);

        // Update resep bahan
        if ($request->has('bahan') && is_array($request->bahan)) {
            $resep = [];
            foreach ($request->bahan as $bahan_id => $kebutuhan) {
                if (!empty($kebutuhan) && $kebutuhan > 0) {
                    $resep[$bahan_id] = ['kebutuhan' => $kebutuhan];
                }
            }
            $menu->bahanJadi()->sync($resep);
        } else {
            $menu->bahanJadi()->detach();
        }

        return redirect()->route('owner.menu.index')
                         ->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy(Menu $menu)
    {
        Storage::delete('public/menus/' . $menu->foto);
        $menu->bahanJadi()->detach();
        $menu->delete();

        return redirect()->route('owner.menu.index')
                         ->with('success', 'Menu berhasil dihapus!');
    }
}