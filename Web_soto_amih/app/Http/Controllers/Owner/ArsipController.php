<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\LaporanHarian;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanHarian::with('kasir')
            ->orderBy('tanggal', 'desc');

        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal', Carbon::parse($request->bulan)->month)
                  ->whereYear('tanggal', Carbon::parse($request->bulan)->year);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $arsipList = $query->paginate(15)->withQueryString();

        // Ringkasan total keseluruhan (untuk filter aktif)
        $totalPendapatan = $query->sum('pendapatan_kotor');
        $totalLaba       = $query->sum('laba_bersih');

        // Daftar tahun yang tersedia
        $tahunTersedia = LaporanHarian::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('owner.arsip.index', compact(
            'arsipList',
            'tahunTersedia',
            'totalPendapatan',
            'totalLaba'
        ));
    }

    public function show($id)
    {
        $laporan = LaporanHarian::with('kasir')->findOrFail($id);

        $tanggal    = Carbon::parse($laporan->tanggal);
        $startOfDay = $tanggal->copy()->setTimezone('Asia/Jakarta')->startOfDay()->utc();
        $endOfDay   = $tanggal->copy()->setTimezone('Asia/Jakarta')->endOfDay()->utc();

        // Order pada hari itu
        $orders = Order::with('orderItems.menu')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'selesai')
            ->orderBy('created_at')
            ->get();

        // Breakdown per jam (untuk grafik)
        $grafikJam = [];
        for ($h = 6; $h <= 22; $h++) {
            $start = $tanggal->copy()->setTimezone('Asia/Jakarta')->setTime($h, 0, 0)->utc();
            $end   = $tanggal->copy()->setTimezone('Asia/Jakarta')->setTime($h, 59, 59)->utc();
            $total = Order::whereBetween('created_at', [$start, $end])
                ->where('status', 'selesai')
                ->sum('total');
            $grafikJam[] = [
                'jam'   => sprintf('%02d:00', $h),
                'total' => (int) $total,
            ];
        }

        // Top menu terjual hari itu
        $topMenu = \App\Models\OrderItem::whereHas('order', function ($q) use ($startOfDay, $endOfDay) {
                $q->whereBetween('created_at', [$startOfDay, $endOfDay])
                  ->where('status', 'selesai');
            })
            ->with('menu')
            ->selectRaw('menu_id, SUM(jumlah) as total_terjual, SUM(jumlah * harga) as total_pendapatan')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        // Breakdown metode bayar
        $metodeBayar = Order::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'selesai')
            ->selectRaw('metode_bayar, COUNT(*) as jumlah, SUM(total) as total')
            ->groupBy('metode_bayar')
            ->get();

        return view('owner.arsip.show', compact(
            'laporan',
            'orders',
            'grafikJam',
            'topMenu',
            'metodeBayar',
            'tanggal'
        ));
    }

    public function cetakPdf($id)
    {
        $laporan = LaporanHarian::with('kasir')->findOrFail($id);

        $tanggal    = Carbon::parse($laporan->tanggal);
        $startOfDay = $tanggal->copy()->setTimezone('Asia/Jakarta')->startOfDay()->utc();
        $endOfDay   = $tanggal->copy()->setTimezone('Asia/Jakarta')->endOfDay()->utc();

        $orders = Order::with('orderItems.menu')
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'selesai')
            ->orderBy('created_at')
            ->get();

        $topMenu = \App\Models\OrderItem::whereHas('order', function ($q) use ($startOfDay, $endOfDay) {
                $q->whereBetween('created_at', [$startOfDay, $endOfDay])
                  ->where('status', 'selesai');
            })
            ->with('menu')
            ->selectRaw('menu_id, SUM(jumlah) as total_terjual, SUM(jumlah * harga) as total_pendapatan')
            ->groupBy('menu_id')
            ->orderByDesc('total_terjual')
            ->take(5)
            ->get();

        return view('owner.arsip.pdf', compact(
            'laporan',
            'orders',
            'topMenu',
            'tanggal'
        ));
    }
}