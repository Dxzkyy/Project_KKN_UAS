@extends('layouts.app')

@section('title', 'Detail Arsip - ' . $tanggal->format('d M Y'))

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('page-title', 'Detail Arsip Laporan')

@section('header-user')
    <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2">
        <div class="w-8 h-8 rounded-full bg-[#C97B2E] flex items-center justify-center text-white font-bold text-sm">
            {{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}
        </div>
        <div class="text-left">
            <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Owner' }}</p>
            <p class="text-xs text-gray-400">Owner</p>
        </div>
    </div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="flex flex-col gap-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('owner.arsip.index') }}"
                class="p-2 bg-white rounded-xl shadow-sm hover:bg-gray-50 transition text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <p class="text-sm text-gray-500">Laporan Harian</p>
                <p class="text-lg font-bold text-gray-800">{{ $tanggal->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('owner.arsip.pdf', $laporan->id) }}" target="_blank"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / Download PDF
            </a>
        </div>
    </div>

    {{-- 4 Kartu Statistik --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-[#C97B2E]">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Pendapatan Kotor</p>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($laporan->pendapatan_kotor, 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Total transaksi selesai</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-blue-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total Pesanan</p>
            <p class="text-2xl font-bold text-gray-800">{{ $laporan->total_pesanan }} Order</p>
            <p class="text-xs text-gray-400 mt-1">Transaksi selesai</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-purple-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Modal</p>
            <p class="text-2xl font-bold text-gray-800">
                {{ $laporan->modal_harian !== null ? 'Rp ' . number_format($laporan->modal_harian, 0, ',', '.') : '—' }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Modal diset owner</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-green-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Laba Bersih</p>
            @if($laporan->laba_bersih !== null)
                <p class="text-2xl font-bold {{ $laporan->laba_bersih >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    Rp {{ number_format($laporan->laba_bersih, 0, ',', '.') }}
                </p>
            @else
                <p class="text-2xl font-bold text-gray-300">—</p>
            @endif
            <p class="text-xs text-gray-400 mt-1">Pendapatan - Modal</p>
        </div>
    </div>

    {{-- Grafik per Jam + Metode Bayar --}}
    <div class="grid grid-cols-3 gap-4">

        {{-- Grafik per jam --}}
        <div class="col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Grafik Penjualan Per Jam</h3>
            <canvas id="grafikJam" height="100"></canvas>
        </div>

        {{-- Metode Bayar --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Metode Pembayaran</h3>
            @if($metodeBayar->count() > 0)
                <div class="flex flex-col gap-3">
                    @foreach($metodeBayar as $metode)
                    @php
                        $persen = $laporan->pendapatan_kotor > 0
                            ? round(($metode->total / $laporan->pendapatan_kotor) * 100)
                            : 0;
                        $colors = ['tunai' => 'bg-orange-400', 'qris' => 'bg-blue-400', 'transfer' => 'bg-purple-400'];
                        $color = $colors[strtolower($metode->metode_bayar)] ?? 'bg-gray-400';
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700 capitalize">{{ $metode->metode_bayar }}</span>
                            <span class="text-gray-500">{{ $metode->jumlah }} order · {{ $persen }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="{{ $color }} h-2 rounded-full transition-all" style="width: {{ $persen }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">Rp {{ number_format($metode->total, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm text-center mt-8">Tidak ada data</p>
            @endif

            {{-- Info kasir --}}
            <div class="mt-6 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-2">Dikirim oleh</p>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-[#C97B2E] flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($laporan->kasir?->name ?? 'K', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">{{ $laporan->kasir?->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $laporan->created_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</p>
                    </div>
                </div>
                @if($laporan->catatan)
                <div class="mt-3 bg-orange-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 font-medium mb-1">Catatan Kasir</p>
                    <p class="text-sm text-gray-700">{{ $laporan->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Menu Terjual --}}
    @if($topMenu->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Menu Terlaris Hari Ini</h3>
        <div class="grid grid-cols-5 gap-4">
            @foreach($topMenu as $i => $item)
            <div class="text-center">
                <div class="w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 font-bold text-sm
                    {{ $i === 0 ? 'bg-yellow-100 text-yellow-600' : ($i === 1 ? 'bg-gray-100 text-gray-500' : ($i === 2 ? 'bg-orange-100 text-orange-500' : 'bg-gray-50 text-gray-400')) }}">
                    #{{ $i + 1 }}
                </div>
                <p class="text-sm font-semibold text-gray-700 leading-tight">{{ $item->menu?->nama_produk ?? 'Menu Dihapus' }}</p>
                <p class="text-xs text-[#C97B2E] font-bold mt-1">{{ $item->total_terjual }}x terjual</p>
                <p class="text-xs text-gray-400">Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Rincian Transaksi --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Rincian Transaksi ({{ $orders->count() }} order)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-3 px-4 rounded-tl-lg font-semibold">Waktu</th>
                        <th class="py-3 px-4 font-semibold">Kode Order</th>
                        <th class="py-3 px-4 font-semibold">Tipe</th>
                        <th class="py-3 px-4 font-semibold">Items</th>
                        <th class="py-3 px-4 font-semibold">Metode Bayar</th>
                        <th class="py-3 px-4 rounded-tr-lg font-semibold text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 text-gray-500">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-700">{{ $order->kode_order }}</td>
                        <td class="py-3 px-4">
                            <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs capitalize">{{ $order->tipe ?? 'dine in' }}</span>
                        </td>
                        <td class="py-3 px-4 text-gray-600 text-xs">
                            @foreach($order->orderItems as $item)
                                {{ $item->jumlah }}x {{ $item->menu?->nama_produk ?? 'Menu Dihapus' }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="py-3 px-4">
                            <span class="bg-orange-50 text-[#C97B2E] px-2 py-0.5 rounded text-xs capitalize">{{ $order->metode_bayar }}</span>
                        </td>
                        <td class="py-3 px-4 font-bold text-[#C97B2E] text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">Tidak ada data transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($orders->count() > 0)
                <tfoot class="border-t-2 border-gray-200">
                    <tr class="bg-orange-50">
                        <td colspan="5" class="py-3 px-4 font-bold text-gray-700">Total Pendapatan</td>
                        <td class="py-3 px-4 font-bold text-[#C97B2E] text-right text-base">Rp {{ number_format($laporan->pendapatan_kotor, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>

<script>
    const grafikJam = @json($grafikJam);
    new Chart(document.getElementById('grafikJam'), {
        type: 'bar',
        data: {
            labels: grafikJam.map(d => d.jam),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: grafikJam.map(d => d.total),
                backgroundColor: 'rgba(201, 123, 46, 0.15)',
                borderColor: '#C97B2E',
                borderWidth: 2,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: val => val === 0 ? 'Rp 0' : 'Rp ' + (val / 1000).toLocaleString('id-ID') + 'rb'
                    }
                }
            }
        }
    });
</script>
@endsection