@extends('layouts.app')

@section('title', 'Layar Dapur')

@section('sidebar-menu')
    <a href="{{ route('chef.pesanan.index') }}"
        class="flex items-center gap-3 rounded-xl px-4 py-2 transition {{ request()->routeIs('chef.pesanan.*') ? 'bg-white text-[#C97B2E] font-semibold' : 'text-white hover:bg-orange-600' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Pesanan
    </a>
@endsection

@section('page-title', 'Kitchen Display System')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Antrean Pesanan</h2>

        {{-- Indikator Auto Refresh --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 font-medium bg-white px-3 py-1.5 rounded-lg shadow-sm">
            Auto-refresh 10 detik
            <span class="relative flex h-3 w-3 inline-block ml-1">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
        </div>
    </div>

    {{-- Grid Card Pesanan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($orders as $order)
            @php
                $isAndroid  = $order->sumber === 'android';
                $bgColor    = $order->status == 'pending' ? 'bg-yellow-50 border-yellow-200' : 'bg-purple-50 border-purple-200';
                $badgeColor = $order->status == 'pending' ? 'bg-yellow-200 text-yellow-800' : 'bg-purple-200 text-purple-800';
                $statusText = $order->status == 'pending' ? 'Pesanan Baru' : 'Sedang Dimasak';
            @endphp

            <div class="rounded-2xl border-2 shadow-sm flex flex-col h-full {{ $bgColor }} transition-all duration-300
                        {{ $isAndroid ? 'ring-2 ring-blue-400 ring-offset-1' : '' }}">

                {{-- ── Header Card ─────────────────────────────────────────── --}}
                <div class="p-4 border-b border-white/50">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">#{{ $order->kode_order }}</h2>
                            <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badgeColor }}">
                                {{ $statusText }}
                            </span>
                            {{-- Badge Via Android --}}
                            @if($isAndroid)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 flex items-center gap-1">
                                    📱 Via Android
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Info Nama & Meja (hanya jika ada, terutama order Android) --}}
                    @if($order->nama_pembeli || $order->nomor_meja)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($order->nama_pembeli)
                                <div class="flex items-center gap-1.5 bg-white/70 px-3 py-1.5 rounded-lg text-sm">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="font-semibold text-gray-700">{{ $order->nama_pembeli }}</span>
                                </div>
                            @endif
                            @if($order->nomor_meja)
                                <div class="flex items-center gap-1.5 bg-white/70 px-3 py-1.5 rounded-lg text-sm">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M3 14h18M10 4v16M14 4v16"/>
                                    </svg>
                                    <span class="font-semibold text-gray-700">Meja {{ $order->nomor_meja }}</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Catatan jika ada --}}
                    @if($order->catatan)
                        <div class="mt-2 flex items-start gap-1.5 bg-orange-50 border border-orange-200 px-3 py-2 rounded-lg text-sm">
                            <span>📝</span>
                            <span class="text-orange-800 italic">{{ $order->catatan }}</span>
                        </div>
                    @endif
                </div>

                {{-- ── Detail Pesanan ───────────────────────────────────────── --}}
                <div class="p-4 flex-grow">
                    <p class="text-sm font-semibold text-gray-600 mb-3 border-b pb-2">Detail Pesanan:</p>
                    <ul class="space-y-3">
                        @foreach ($order->orderItems as $item)
                            <li class="flex justify-between items-center bg-white/60 p-2 rounded-lg">
                                <span class="font-bold text-gray-800 text-lg">{{ $item->jumlah }}x</span>
                                <span class="flex-grow ml-3 text-gray-700 font-medium">
                                    {{ $item->menu?->nama_produk ?? 'Menu Dihapus' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- ── Tombol Aksi ──────────────────────────────────────────── --}}
                <div class="p-4 mt-auto">
                    @if ($order->status == 'pending')
                        <form action="{{ route('chef.pesanan.update_status', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="proses">
                            <button type="submit"
                                class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 rounded-xl transition flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/>
                                </svg>
                                Mulai Masak
                            </button>
                        </form>
                    @elseif($order->status == 'proses')
                        <form action="{{ route('chef.pesanan.update_status', $order->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit"
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition flex justify-center items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Selesai & Siap Saji
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-16 bg-white rounded-2xl shadow-sm text-gray-400">
                <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <p class="text-lg font-medium">Dapur sedang santai.</p>
                <p class="text-sm">Belum ada pesanan masuk dari Kasir.</p>
            </div>
        @endforelse
    </div>

    <script>
        setTimeout(function() {
            window.location.reload();
        }, 10000);
    </script>
@endsection