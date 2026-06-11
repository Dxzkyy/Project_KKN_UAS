@extends('layouts.app')

@section('title', 'Penjualan')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('page-title', 'Penjualan')

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

    {{-- Sub-header tanggal --}}
    <div>
        <p class="text-sm text-gray-500">Statistik penjualan per hari ini</p>
        <p class="text-lg font-bold text-gray-800">{{ $today->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- 4 Kartu Statistik --}}
    <div class="grid grid-cols-4 gap-4">

        {{-- Penjualan Hari Ini --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-[#C97B2E]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-[#C97B2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Penjualan Hari Ini</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                @if($penjualanHariIni >= 1000000)
                    Rp {{ number_format($penjualanHariIni / 1000000, 1, ',', '.') }} JT
                @else
                    Rp {{ number_format($penjualanHariIni / 1000, 0, ',', '.') }}rb
                @endif
            </p>
            <p class="text-xs text-gray-400 mt-1">Order selesai hari ini</p>
        </div>

        {{-- Total Pesanan --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-blue-400">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Pesanan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalPesananHariIni }}</p>
            <p class="text-xs text-gray-400 mt-1">Order masuk hari ini</p>
        </div>

        {{-- Rata-rata Transaksi --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-purple-400">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Rata-rata Transaksi</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                Rp {{ number_format($rataRataTransaksi / 1000, 0, ',', '.') }}rb
            </p>
            <p class="text-xs text-gray-400 mt-1">Per order hari ini</p>
        </div>

        {{-- Stok Menipis --}}
        <a href="{{ route('owner.bahan_jadi.index') }}"
            class="bg-white rounded-2xl shadow-sm p-5 border-l-4 {{ $stokMenipis->count() > 0 ? 'border-red-400' : 'border-green-400' }} hover:shadow-md transition block">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 rounded-xl {{ $stokMenipis->count() > 0 ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center">
                    <svg class="w-5 h-5 {{ $stokMenipis->count() > 0 ? 'text-red-500' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                @if($stokMenipis->count() > 0)
                <span class="text-xs bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full">Perlu Perhatian</span>
                @endif
            </div>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Stok Menipis</p>
            <p class="text-2xl font-bold {{ $stokMenipis->count() > 0 ? 'text-red-500' : 'text-green-600' }} mt-1">
                {{ $stokMenipis->count() }} Item
            </p>
            <p class="text-xs text-gray-400 mt-1">
                {{ $stokMenipis->count() > 0 ? 'Klik untuk lihat detail' : 'Semua stok aman' }}
            </p>
        </a>
    </div>

    {{-- Grafik + Menu Terlaris --}}
    <div class="grid grid-cols-3 gap-4">

        {{-- Grafik Penjualan --}}
        <div class="col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-800">Grafik Penjualan</h3>
                {{-- Filter Periode --}}
                <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
                    @foreach(['minggu' => 'Minggu Ini', 'bulan' => 'Bulan Ini', '3bulan' => '3 Bulan'] as $key => $label)
                    <a href="{{ route('owner.penjualan.index', ['periode' => $key]) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold transition
                        {{ $periode === $key ? 'bg-white text-[#C97B2E] shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>
            <canvas id="grafikPenjualan" height="110"></canvas>
        </div>

        {{-- Menu Terlaris --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-1">Menu Terlaris</h3>
            <p class="text-xs text-gray-400 mb-4">7 hari terakhir</p>

            @if($topMenu->count() > 0)
            <div class="flex flex-col gap-3">
                @foreach($topMenu as $i => $item)
                @php
                    $persen = round(($item->total_terjual / $maxTerjual) * 100);
                    $colors = ['bg-[#C97B2E]', 'bg-orange-400', 'bg-amber-400', 'bg-yellow-400', 'bg-yellow-300', 'bg-yellow-200'];
                    $color = $colors[$i] ?? 'bg-gray-300';
                @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-700 truncate max-w-[70%]">
                            {{ $item->menu?->nama_produk ?? 'Menu Dihapus' }}
                        </span>
                        <span class="text-xs font-bold text-gray-500">{{ $item->total_terjual }} Porsi</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="{{ $color }} h-2 rounded-full transition-all duration-500" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-gray-400 text-sm">Belum ada data penjualan</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Metode Bayar + Pesanan Terbaru --}}
    <div class="grid grid-cols-3 gap-4">

        {{-- Breakdown Metode Bayar --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-1">Metode Pembayaran</h3>
            <p class="text-xs text-gray-400 mb-4">Hari ini</p>

            @if($metodeBayar->count() > 0)
            <div class="flex flex-col gap-4">
                @foreach($metodeBayar as $metode)
                @php
                    $totalSemua = $metodeBayar->sum('total') ?: 1;
                    $persen = round(($metode->total / $totalSemua) * 100);
                    $icons = [
                        'tunai'  => ['icon' => '💵', 'color' => 'bg-green-100 text-green-700'],
                        'qris'   => ['icon' => '📱', 'color' => 'bg-blue-100 text-blue-700'],
                        'bank'   => ['icon' => '🏦', 'color' => 'bg-purple-100 text-purple-700'],
                    ];
                    $style = $icons[strtolower($metode->metode_bayar)] ?? ['icon' => '💳', 'color' => 'bg-gray-100 text-gray-700'];
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $style['color'] }} flex items-center justify-center text-base flex-shrink-0">
                        {{ $style['icon'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700 capitalize">{{ $metode->metode_bayar }}</span>
                            <span class="text-gray-400 text-xs">{{ $persen }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-[#C97B2E] h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $metode->jumlah }} order · Rp {{ number_format($metode->total, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <p class="text-gray-400 text-sm">Belum ada transaksi hari ini</p>
            </div>
            @endif

            {{-- Stok menipis detail --}}
            @if($stokMenipis->count() > 0)
            <div class="mt-5 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-red-500 uppercase tracking-wide mb-2">⚠ Stok Menipis</p>
                <div class="flex flex-col gap-1.5">
                    @foreach($stokMenipis->take(3) as $bahan)
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-600">{{ $bahan->nama_bahan }}</span>
                        <span class="font-bold text-red-500">{{ $bahan->stok }} {{ $bahan->satuan }}</span>
                    </div>
                    @endforeach
                    @if($stokMenipis->count() > 3)
                    <a href="{{ route('owner.bahan_jadi.index') }}" class="text-xs text-[#C97B2E] font-semibold mt-1">
                        +{{ $stokMenipis->count() - 3 }} lainnya →
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Pesanan Terbaru --}}
        <div class="col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-4">Pesanan Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="py-2.5 px-3 rounded-tl-lg font-semibold text-xs">Order ID</th>
                            <th class="py-2.5 px-3 font-semibold text-xs">Waktu</th>
                            <th class="py-2.5 px-3 font-semibold text-xs">Items</th>
                            <th class="py-2.5 px-3 font-semibold text-xs">Kasir</th>
                            <th class="py-2.5 px-3 font-semibold text-xs text-right">Total</th>
                            <th class="py-2.5 px-3 rounded-tr-lg font-semibold text-xs text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pesananTerbaru as $order)
                        <tr class="hover:bg-orange-50 transition">
                            <td class="py-2.5 px-3 font-semibold text-gray-700 text-xs">{{ $order->kode_order }}</td>
                            <td class="py-2.5 px-3 text-gray-500 text-xs">
                                {{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M, H:i') }}
                            </td>
                            <td class="py-2.5 px-3 text-gray-600 text-xs max-w-[200px] truncate">
                                @foreach($order->orderItems as $item)
                                    {{ $item->jumlah }}x {{ $item->menu?->nama_produk ?? '—' }}@if(!$loop->last), @endif
                                @endforeach
                            </td>
                            <td class="py-2.5 px-3 text-gray-500 text-xs">{{ $order->kasir?->name ?? '—' }}</td>
                            <td class="py-2.5 px-3 font-bold text-[#C97B2E] text-xs text-right">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    Selesai
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-400 text-sm">Belum ada pesanan selesai</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    const grafikData = @json($grafikData);

    new Chart(document.getElementById('grafikPenjualan'), {
        type: 'line',
        data: {
            labels: grafikData.map(d => d.label),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: grafikData.map(d => d.total),
                borderColor: '#C97B2E',
                backgroundColor: 'rgba(201, 123, 46, 0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#C97B2E',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#9ca3af',
                    bodyColor: '#fff',
                    padding: 10,
                    callbacks: {
                        label: ctx => ' Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        callback: val => {
                            if (val === 0) return 'Rp 0';
                            if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                            return 'Rp ' + (val / 1000) + 'rb';
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>

@endsection