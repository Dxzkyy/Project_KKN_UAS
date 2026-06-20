<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChefController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.menu')
            ->whereIn('status', ['pending', 'proses'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        return view('chef.pesanan.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('orderItems.menu')->findOrFail($id);
        $statusLama = $order->status;
        $order->status = $request->status;
        $order->save();

        // Kirim notifikasi ke kasir berdasarkan perubahan status
        if ($statusLama === 'pending' && $request->status === 'proses') {
            // Chef mulai masak → notif ke semua kasir
            $namaMenu = $order->orderItems->map(fn($i) => $i->jumlah . 'x ' . ($i->menu?->nama_produk ?? '?'))->join(', ');
            Notification::kirimKeRole('kasir', [
                'judul'       => '👨‍🍳 Pesanan Sedang Dimasak',
                'pesan'       => "#{$order->kode_order} ({$namaMenu}) sedang diproses dapur.",
                'ikon'        => 'fire',
                'warna'       => 'purple',
                'dari_user_id'=> auth()->id(),
                'order_id'    => $order->id,
            ]);
        }

        if ($request->status === 'selesai') {
            // Pesanan selesai → notif ke kasir
            $namaMenu = $order->orderItems->map(fn($i) => $i->jumlah . 'x ' . ($i->menu?->nama_produk ?? '?'))->join(', ');
            Notification::kirimKeRole('kasir', [
                'judul'       => '✅ Pesanan Siap Saji!',
                'pesan'       => "#{$order->kode_order} ({$namaMenu}) sudah siap disajikan.",
                'ikon'        => 'check',
                'warna'       => 'green',
                'dari_user_id'=> auth()->id(),
                'order_id'    => $order->id,
            ]);
        }

        return redirect()->back()->with('success', 'Status pesanan diperbarui!');
    }
}