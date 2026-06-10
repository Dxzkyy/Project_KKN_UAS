<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PesananController extends Controller
{
    public function index()
    {
        $menus = Menu::all();
        return view('kasir.pesanan.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'nullable|string|max:255',
            'tipe'         => 'required|in:dine_in,takeaway',
            'metode_bayar' => 'required|in:tunai,qris,bank',
            'diskon'       => 'nullable|numeric',
            'tipe_diskon'  => 'required|in:persen,nominal',
            'total'        => 'required|numeric',
            'cart'         => 'required|array',
            'cart.*.id'    => 'required|exists:menus,id',
            'cart.*.qty'   => 'required|integer|min:1',
            'cart.*.harga' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::create([
                'kode_order'   => 'ORD' . Carbon::now()->format('YmdHis') . rand(10, 99),
                'nama_pembeli' => $request->nama_pembeli,
                'tipe'         => $request->tipe,
                'metode_bayar' => $request->metode_bayar,
                'diskon'       => $request->diskon ?? 0,
                'tipe_diskon'  => $request->tipe_diskon,
                'total'        => $request->total,
                'status'       => 'pending',
                'kasir_id'     => auth()->id(),
            ]);

            foreach ($request->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['id'],
                    'jumlah'   => $item['qty'],
                    'harga'    => $item['harga'],
                ]);

                $menu = Menu::with('bahanJadi')->find($item['id']);

                if ($menu && $menu->bahanJadi->isNotEmpty()) {
                    foreach ($menu->bahanJadi as $bahan) {
                        $totalKebutuhan = $bahan->pivot->kebutuhan * $item['qty'];
                        $bahan->decrement('stok', $totalKebutuhan);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil disimpan dan masuk ke dapur!',
                'order'   => $order
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function riwayat()
    {
        $orders = Order::with('orderItems.menu')->latest()->get();
        return view('kasir.pesanan.riwayat', compact('orders'));
    }

    public function cetakStruk($id)
    {
        $order = Order::with('orderItems.menu')->findOrFail($id);
        return view('kasir.pesanan.struk', compact('order'));
    }

    // Method untuk membatalkan pesanan (hanya status pending)
    public function cancel($id)
    {
        $order = Order::findOrFail($id);

        // Pastikan hanya pesanan berstatus pending yang bisa dibatalkan
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Pesanan tidak bisa dibatalkan karena sudah diproses dapur.');
        }

        $order->status = 'dibatalkan';
        $order->save();

        return redirect()->back()->with('success', "Pesanan {$order->kode_order} berhasil dibatalkan.");
    }
}