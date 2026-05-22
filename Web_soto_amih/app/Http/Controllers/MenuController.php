<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::latest()->paginate(10);
        return view('owner.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('owner.menu.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori'    => 'required|in:Makanan,Minuman,Lainnya',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
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

        Menu::create([
            'kode_produk' => $kodeProduk,
            'nama_produk' => $request->nama_produk,
            'kategori'    => $request->kategori,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'foto'        => $fotoName,
        ]);

        return redirect()->route('owner.menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        return view('owner.menu.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'kategori'    => 'required|in:Makanan,Minuman,Lainnya',
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $fotoName = $menu->foto;

        // Kalau ada foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama
            Storage::delete('public/menus/' . $menu->foto);

            $foto = $request->file('foto');
            $fotoName = time() . '_' . Str::slug($request->nama_produk) . '.' . $foto->extension();
            $foto->storeAs('public/menus', $fotoName);
        }

        $menu->update([
            'nama_produk' => $request->nama_produk,
            'kategori'    => $request->kategori,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'foto'        => $fotoName,
        ]);

        return redirect()->route('owner.menu.index')->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy(Menu $menu)
    {
        Storage::delete('public/menus/' . $menu->foto);
        $menu->delete();

        return redirect()->route('owner.menu.index')->with('success', 'Menu berhasil dihapus!');
    }
}