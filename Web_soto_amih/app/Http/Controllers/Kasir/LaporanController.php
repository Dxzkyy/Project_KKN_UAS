<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\LaporanHarian;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $today      = Carbon::today('Asia/Jakarta');
        $startOfDay = Carbon::now('Asia/Jakarta')->startOfDay()->utc();
        $endOfDay   = Carbon::now('Asia/Jakarta')->endOfDay()->utc();

        // Ambil semua order hari ini (kecuali dibatalkan)
        $ordersHariIni = Order::with('orderItems.menu')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->whereIn('status', ['selesai', 'pending', 'proses'])
            ->get();

        // Statistik hari ini — hanya order selesai
        $pendapatanKotor = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'selesai')
            ->sum('total');

        $totalPesanan = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'selesai')
            ->count();

        // Cek laporan hari ini
        $laporanTerkirim = LaporanHarian::whereDate('tanggal', $today)->first();

        // Ambil modal yang sudah diset owner untuk hari ini
        $modalHariIni = \App\Models\ModalHarian::whereDate('tanggal', $today)->first();

        // Hitung laba bersih otomatis jika modal sudah diset owner
        $labaBersih = null;
        if ($modalHariIni) {
            $labaBersih = $pendapatanKotor - $modalHariIni->nominal;
        }

        // Grafik 12 bulan terakhir
        $grafikData = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulan      = Carbon::now('Asia/Jakarta')->subMonths($i);
            $startBulan = $bulan->copy()->startOfMonth()->utc();
            $endBulan   = $bulan->copy()->endOfMonth()->utc();
            $total = Order::whereBetween('created_at', [$startBulan, $endBulan])
                ->where('status', 'selesai')
                ->sum('total');
            $grafikData[] = [
                'bulan' => $bulan->translatedFormat('M'),
                'total' => $total,
            ];
        }

        // Riwayat laporan terkirim (10 terbaru)
        $riwayatLaporan = LaporanHarian::with('kasir')
            ->orderBy('tanggal', 'desc')
            ->take(10)
            ->get();

        return view('kasir.laporan.index', compact(
            'pendapatanKotor',
            'totalPesanan',
            'ordersHariIni',
            'laporanTerkirim',
            'modalHariIni',
            'labaBersih',
            'grafikData',
            'riwayatLaporan',
            'today'
        ));
    }

    public function kirim(Request $request)
    {
        $request->validate([
            'catatan' => 'nullable|string|max:500',
        ]);

        $today      = Carbon::today('Asia/Jakarta');
        $startOfDay = Carbon::now('Asia/Jakarta')->startOfDay()->utc();
        $endOfDay   = Carbon::now('Asia/Jakarta')->endOfDay()->utc();

        // Hitung hanya order selesai
        $pendapatanKotor = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'selesai')
            ->sum('total');

        $totalPesanan = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'selesai')
            ->count();

        // Ambil modal yang sudah diset owner
        $modalHariIni = \App\Models\ModalHarian::whereDate('tanggal', $today)->first();
        $modal        = $modalHariIni ? $modalHariIni->nominal : null;
        $laba         = $modal !== null ? ($pendapatanKotor - $modal) : null;

        // Update jika sudah ada, buat baru jika belum
        LaporanHarian::updateOrCreate(
            ['tanggal' => $today],
            [
                'pendapatan_kotor' => $pendapatanKotor,
                'total_pesanan'    => $totalPesanan,
                'modal_harian'     => $modal,
                'laba_bersih'      => $laba,
                'catatan'          => $request->catatan,
                'kasir_id'         => auth()->id(),
            ]
        );

        return redirect()->back()->with('success', 'Laporan harian berhasil dikirim ke Pemilik!');
    }
}