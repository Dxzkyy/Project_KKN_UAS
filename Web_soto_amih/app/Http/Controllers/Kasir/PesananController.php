<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    // Method untuk menampilkan halaman POS kasir
    public function index()
    {
        // Ambil semua data menu dari database
        $menus = Menu::all(); 
        
        // Kirim data $menus ke view
        return view('kasir.pesanan.index', compact('menus'));
    }

    // Method untuk memproses pembayaran via AJAX
    public function store(Request $request)
    {
        // 1. Validasi data yang dikirim dari Javascript Frontend
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
            // Mulai transaksi database agar aman jika ada error di tengah jalan
            DB::beginTransaction();

            // 2. Simpan data utama transaksi ke tabel orders
            $order = Order::create([
                'kode_order'   => 'ORD' . date('YmdHis') . rand(10, 99), // Contoh: ORD20260606203015xx
                'nama_pembeli' => $request->nama_pembeli,
                'tipe'         => $request->tipe,
                'metode_bayar' => $request->metode_bayar,
                'diskon'       => $request->diskon ?? 0,
                'tipe_diskon'  => $request->tipe_diskon,
                'total'        => $request->total,
                'status'       => 'pending', // Menunggu diproses koki di dapur
                'kasir_id'     => auth()->id(),
            ]);

            // 3. Simpan detail pesanan & potong stok bahan
            foreach ($request->cart as $item) {
                // Insert ke tabel order_items
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id'  => $item['id'],
                    'jumlah'   => $item['qty'],
                    'harga'    => $item['harga'],
                ]);

                // 4. Logika Pemotongan Stok Otomatis
                // Tarik data menu beserta relasi bahan jadinya
                $menu = Menu::with('bahanJadi')->find($item['id']);
                
                if ($menu && $menu->bahanJadi->isNotEmpty()) {
                    foreach ($menu->bahanJadi as $bahan) {
                        // Kebutuhan bahan per porsi dikali jumlah porsi yang dibeli
                        $totalKebutuhan = $bahan->pivot->kebutuhan * $item['qty'];
                        
                        // Kurangi langsung di database tabel bahan_jadis
                        $bahan->decrement('stok', $totalKebutuhan);
                    }
                }
            }

            // Jika semua proses di atas sukses, simpan permanen
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil disimpan dan masuk ke dapur!',
                'order'   => $order
            ]);

        } catch (\Exception $e) {
            // Jika ada error (misal koneksi putus), batalkan semua inputan ke DB
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk menampilkan halaman riwayat pesanan
    public function riwayat()
    {
        // Ambil semua data pesanan dari yang paling baru, beserta detail item dan menunya
        $orders = Order::with('orderItems.menu')->latest()->get();
        
        return view('kasir.pesanan.riwayat', compact('orders'));
    }

    // Method untuk menampilkan tampilan cetak struk
    public function cetakStruk($id)
    {
        // Cari data pesanan berdasarkan ID
        $order = Order::with('orderItems.menu')->findOrFail($id);
        
        return view('kasir.pesanan.struk', compact('order'));
    }
}