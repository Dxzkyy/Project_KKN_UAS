<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderApiController extends Controller
{
    /**
     * POST /api/v1/orders
     * Buat pesanan baru dari pelanggan Android (guest)
     *
     * Body JSON:
     * {
     *   "nama_pembeli": "Putri Aulia",
     *   "nomor_meja": "5",
     *   "email": "putri@email.com",
     *   "tipe": "dine_in",          // dine_in | takeaway
     *   "metode_bayar": "tunai",    // tunai | qris | bank
     *   "catatan": "Tanpa cabai",
     *   "items": [
     *     { "menu_id": 1, "jumlah": 2 },
     *     { "menu_id": 3, "jumlah": 1 }
     *   ]
     * }
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string|max:100',
            'nomor_meja' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:100',
            'tipe' => 'required|in:dine_in,takeaway',
            'metode_bayar' => 'required|in:tunai,qris,bank',
            'catatan' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Hitung total & validasi stok
            $total = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $menu = Menu::findOrFail($item['menu_id']);

                // if ($menu->stok < $item['jumlah']) {
                //     return response()->json([
                //         'success' => false,
                //         'message' => "Stok {$menu->nama_produk} tidak cukup (tersisa {$menu->stok})",
                //     ], 422);
                // }

                $subtotal = $menu->harga * $item['jumlah'];
                $total += $subtotal;

                $itemsData[] = [
                    'menu' => $menu,
                    'jumlah' => $item['jumlah'],
                    'harga' => $menu->harga,
                ];
            }

            // Generate kode order unik
            $kodeOrder = 'MOB-' . strtoupper(Str::random(6)) . '-' . now()->format('His');

            // Simpan order
            // kasir_id nullable karena dari Android (guest), bukan kasir internal
            $order = Order::create([
                'kode_order' => $kodeOrder,
                'nama_pembeli' => $request->nama_pembeli,
                'tipe' => $request->tipe,
                'nomor_meja' => $request->nomor_meja,
                'metode_bayar' => $request->metode_bayar,
                'diskon' => 0,
                'tipe_diskon' => 'persen',
                'total' => $total,
                'status' => 'pending',
                'kasir_id' => null, // Guest order dari Android
                'catatan' => $request->catatan,
                'email_pembeli' => $request->email,
            ]);

            // Simpan order items & kurangi stok
            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item['menu']->id,
                    'jumlah' => $item['jumlah'],
                    'harga' => $item['harga'],
                ]);

                // // Kurangi stok
                // $item['menu']->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            // Kirim email konfirmasi jika email diisi
            if ($request->email) {
                try {
                    Mail::to($request->email)->send(
                        new \App\Mail\OrderConfirmationMail($order, $itemsData)
                    );
                } catch (\Exception $e) {
                    // Email gagal tidak membatalkan order
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => [
                    'kode_order' => $order->kode_order,
                    'nama_pembeli' => $order->nama_pembeli,
                    'nomor_meja' => $order->nomor_meja,
                    'tipe' => $order->tipe,
                    'metode_bayar' => $order->metode_bayar,
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'waktu' => $order->created_at->format('d M Y | H:i'),
                    'items' => collect($itemsData)->map(fn($i) => [
                        'nama_produk' => $i['menu']->nama_produk,
                        'jumlah' => $i['jumlah'],
                        'harga' => (float) $i['harga'],
                        'subtotal' => (float) ($i['harga'] * $i['jumlah']),
                    ]),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/v1/orders/{kode_order}
     * Lihat detail pesanan berdasarkan kode
     */
    public function show($kode_order)
    {
        $order = Order::with('orderItems.menu')
            ->where('kode_order', $kode_order)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kode_order' => $order->kode_order,
                'nama_pembeli' => $order->nama_pembeli,
                'nomor_meja' => $order->nomor_meja,
                'tipe' => $order->tipe,
                'metode_bayar' => $order->metode_bayar,
                'total' => (float) $order->total,
                'status' => $order->status,
                'catatan' => $order->catatan,
                'waktu' => $order->created_at->format('d M Y | H:i'),
                'items' => $order->orderItems->map(fn($item) => [
                    'nama_produk' => $item->menu->nama_produk,
                    'foto_url' => $item->menu->foto ? asset('storage/' . $item->menu->foto) : null,
                    'jumlah' => $item->jumlah,
                    'harga' => (float) $item->harga,
                    'subtotal' => (float) ($item->harga * $item->jumlah),
                ]),
            ],
        ]);
    }

    /**
     * GET /api/v1/orders/{kode_order}/status
     * Polling status pesanan (untuk notifikasi realtime)
     */
    public function status($kode_order)
    {
        $order = Order::where('kode_order', $kode_order)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        $pesan = match ($order->status) {
            'pending' => 'Pesanan Anda sedang menunggu konfirmasi dapur.',
            'proses' => 'Pesanan Anda sedang diproses oleh dapur.',
            'selesai' => 'Pesanan Anda sudah selesai! Silakan ambil di kasir.',
            'dibatalkan' => 'Pesanan Anda telah dibatalkan.',
            default => '-',
        };

        return response()->json([
            'success' => true,
            'data' => [
                'kode_order' => $order->kode_order,
                'status' => $order->status,
                'pesan' => $pesan,
                'updated_at' => $order->updated_at->format('d M Y | H:i'),
            ],
        ]);
    }

    public function riwayatByEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $orders = Order::with('orderItems.menu')
            ->where('email_pembeli', $request->email)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'kode_order' => $order->kode_order,
                    'nama_pembeli' => $order->nama_pembeli,
                    'nomor_meja' => $order->nomor_meja,
                    'tipe' => $order->tipe,
                    'metode_bayar' => $order->metode_bayar,
                    'total' => (float) $order->total,
                    'status' => $order->status,
                    'waktu' => $order->created_at->format('d M Y | H:i'),
                    'items' => $order->orderItems->map(fn($item) => [
                        'nama_produk' => $item->menu?->nama_produk ?? 'Menu dihapus',
                        'jumlah' => $item->jumlah,
                        'harga' => (float) $item->harga,
                        'subtotal' => (float) ($item->harga * $item->jumlah),
                    ]),
                ];
            });

        return response()->json([
            'success' => $orders->isNotEmpty(),
            'message' => $orders->isEmpty() ? 'Tidak ada pesanan ditemukan.' : '',
            'data' => $orders,
        ]);
    }

    public function cancelByCustomer(Request $request, $kode_order)
    {
        $order = Order::where('kode_order', $kode_order)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        // Hanya bisa batalkan jika masih pending
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak bisa dibatalkan karena sudah ' . $order->status . '.',
            ], 422);
        }

        $order->status = 'dibatalkan';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan.',
        ]);
    }
}