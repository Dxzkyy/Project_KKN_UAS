<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\LaporanHarian;
use App\Models\ModalHarian;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta');

        // Modal hari ini yang sudah diset owner
        $modalHariIni = ModalHarian::whereDate('tanggal', $today)->first();

        // Query orders dengan filter
        $query = Order::with('kasir')
            ->whereNotIn('status', ['pending', 'proses']); // hanya tampilkan yang sudah final

        // Filter tanggal mulai
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        // Filter tanggal selesai
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Search berdasarkan kode order atau nama kasir
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_order', 'like', "%{$search}%")
                  ->orWhereHas('kasir', fn($k) => $k->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('owner.laporan.index', compact(
            'orders',
            'modalHariIni',
            'today'
        ));
    }

    public function setModal(Request $request)
    {
        $request->validate([
            'nominal'  => 'required|numeric|min:0',
            'catatan'  => 'nullable|string|max:500',
        ]);

        $today = Carbon::today('Asia/Jakarta');

        // Update atau buat baru modal hari ini
        ModalHarian::updateOrCreate(
            ['tanggal' => $today],
            [
                'nominal'  => $request->nominal,
                'catatan'  => $request->catatan,
                'owner_id' => auth()->id(),
            ]
        );

        // Jika sudah ada laporan hari ini, update laba bersih-nya
        $laporan = LaporanHarian::whereDate('tanggal', $today)->first();
        if ($laporan) {
            $laporan->update([
                'modal_harian' => $request->nominal,
                'laba_bersih'  => $laporan->pendapatan_kotor - $request->nominal,
                'owner_id'     => auth()->id(),
            ]);
        }

        return redirect()->back()->with('success', 'Modal harian hari ini berhasil disimpan.');
    }

    public function eksporPdf(Request $request)
    {
        // Implementasi ekspor PDF bisa menggunakan DomPDF / Snappy
        // Untuk sekarang redirect back dengan info
        return redirect()->back()->with('info', 'Fitur ekspor PDF segera hadir.');
    }
}