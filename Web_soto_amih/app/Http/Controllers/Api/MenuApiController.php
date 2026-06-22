<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class MenuApiController extends Controller
{
    /**
     * GET /api/v1/menus
     * Ambil semua menu yang stok > 0
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        if ($request->has('kategori') && $request->kategori !== 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        $menus = $query->get()->map(function ($menu) {
            return [
                'id'          => $menu->id,
                'kode_produk' => $menu->kode_produk,
                'nama_produk' => $menu->nama_produk,
                'kategori'    => $menu->kategori,
                'harga'       => (float) $menu->harga,
                'stok'        => $menu->stok,
                'foto_url' => $menu->foto ? url('/api/v1/image/' . $menu->foto) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $menus,
        ]);
    }

    /**
     * GET /api/v1/menus/{id}
     * Detail satu menu
     */
    public function show($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $menu->id,
                'kode_produk' => $menu->kode_produk,
                'nama_produk' => $menu->nama_produk,
                'kategori'    => $menu->kategori,
                'harga'       => (float) $menu->harga,
                'stok'        => $menu->stok,
                'foto_url' => $menu->foto ? url('/api/v1/image/' . $menu->foto) : null,
            ],
        ]);
    }
}