@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('sidebar-menu')
@include('kasir.partials.sidebar')
@endsection

@section('page-title', 'Riwayat Pesanan')

@section('header-user')
    <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2">
        <div class="w-8 h-8 rounded-full bg-[#C97B2E] flex items-center justify-center text-white font-bold text-sm">
            {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
        </div>
        <div class="text-left">
            <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Kasir' }}</p>
            <p class="text-xs text-gray-400">Kasir</p>
        </div>
    </div>
@endsection

@section('content')
<div class="bg-white rounded-2xl shadow-sm p-6 h-full">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-800">Daftar Transaksi Selesai</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="py-3 px-4 rounded-tl-lg font-semibold">Tanggal</th>
                    <th class="py-3 px-4 font-semibold">Kode Order</th>
                    <th class="py-3 px-4 font-semibold">Tipe</th>
                    <th class="py-3 px-4 font-semibold">Metode Bayar</th>
                    <th class="py-3 px-4 font-semibold">Total</th>
                    <th class="py-3 px-4 rounded-tr-lg font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-3 px-4">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4 font-semibold text-gray-700">{{ $order->kode_order }}</td>
                    <td class="py-3 px-4 capitalize">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ str_replace('_', ' ', $order->tipe) }}</span>
                    </td>
                    <td class="py-3 px-4 capitalize">{{ $order->metode_bayar }}</td>
                    <td class="py-3 px-4 font-bold text-[#C97B2E]">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-center">
                        {{-- Tombol Cetak Struk mengarah ke rute struk dengan target blank (buka tab baru) --}}
                        <a href="{{ route('kasir.pesanan.struk', $order->id) }}" target="_blank" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition text-xs font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Cetak
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-400">Belum ada transaksi pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection