<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BahanJadi;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'minggu');

        $today      = Carbon::today('Asia/Jakarta');
        $startToday = Carbon::now('Asia/Jakarta')->startOfDay()->utc();
        $endToday   = Carbon::now('Asia/Jakarta')->endOfDay()->utc();

        // --- Kartu Statistik Hari Ini ---
        $penjualanHariIni = Order::whereBetween('created_at', [$startToday, $endToday])
            ->where('status', 'selesai')
            ->sum('total');

        $totalPesananHariIni = Order::whereBetween('created_at', [$startToday, $endToday])
            ->where('status', 'selesai')
            ->count();

        $rataRataTransaksi = $totalPesananHariIni > 0
            ? $penjualanHariIni / $totalPesananHariIni
            : 0;

        // Stok menipis (stok <= 5)
        $stokMenipis = BahanJadi::where('stok', '<=', 5)->get();

        // --- Grafik Penjualan ---
        $grafikData = [];
        if ($periode === 'minggu') {
            // 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $hari       = Carbon::now('Asia/Jakarta')->subDays($i);
                $startHari  = $hari->copy()->startOfDay()->utc();
                $endHari    = $hari->copy()->endOfDay()->utc();
                $total = Order::whereBetween('created_at', [$startHari, $endHari])
                    ->where('status', 'selesai')
                    ->sum('total');
                $grafikData[] = [
                    'label' => $hari->translatedFormat('D'),
                    'total' => (int) $total,
                ];
            }
        } elseif ($periode === 'bulan') {
            // 30 hari terakhir, dikelompok per minggu
            for ($i = 3; $i >= 0; $i--) {
                $startMinggu = Carbon::now('Asia/Jakarta')->subWeeks($i)->startOfWeek()->utc();
                $endMinggu   = Carbon::now('Asia/Jakarta')->subWeeks($i)->endOfWeek()->utc();
                $total = Order::whereBetween('created_at', [$startMinggu, $endMinggu])
                    ->where('status', 'selesai')
                    ->sum('total');
                $grafikData[] = [
                    'label' => 'Minggu ' . (4 - $i),
                    'total' => (int) $total,
                ];
            }
        } elseif ($periode === '3bulan') {
            // 3 bulan terakhir per bulan
            for ($i = 2; $i >= 0; $i--) {
                $bulan      = Carbon::now('Asia/Jakarta')->subMonths($i);
                $startBulan = $bulan->copy()->startOfMonth()->utc();
                $endBulan   = $bulan->copy()->endOfMonth()->utc();
                $total = Order::whereBetween('created_at', [$startBulan, $endBulan])
                    ->where('status', 'selesai')
                    ->sum('total');
                $grafikData[] = [
                    'label' => $bulan->translatedFormat('M Y'),
                    'total' => (int) $total,
                ];
            }
        }

        // --- Menu Terlaris (7 hari terakhir) ---
        $start7Hari = Carbon::now('Asia/Jakarta')->subDays(6)->startOfDay()->utc();
        $topMenu = OrderItem::whereHas('order', function ($q) use ($start7Hari, $endToday) {
                $q->whereBetween('created_at', [$start7Hari, $endToday])
                  ->where('status', 'selesai');
            })
            ->with('menu')
            ->selectRaw('menu_id, SUM(jumlah) as total_terjual, SUM(jumlah * harga) as total_pendapatan')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->take(6)
            ->get();

        $maxTerjual = $topMenu->max('total_terjual') ?: 1;

        // --- Pesanan Terbaru (10 terakhir) ---
        $pesananTerbaru = Order::with('orderItems.menu', 'kasir')
            ->where('status', 'selesai')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        // --- Breakdown Metode Bayar (hari ini) ---
        $metodeBayar = Order::whereBetween('created_at', [$startToday, $endToday])
            ->where('status', 'selesai')
            ->selectRaw('metode_bayar, COUNT(*) as jumlah, SUM(total) as total')
            ->groupBy('metode_bayar')
            ->get();

        return view('owner.penjualan.index', compact(
            'penjualanHariIni',
            'totalPesananHariIni',
            'rataRataTransaksi',
            'stokMenipis',
            'grafikData',
            'topMenu',
            'maxTerjual',
            'pesananTerbaru',
            'metodeBayar',
            'periode',
            'today'
        ));
    }
}