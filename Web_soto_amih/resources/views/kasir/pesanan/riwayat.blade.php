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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', confirmButtonColor: '#C97B2E', timer: 2500, timerProgressBar: true });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'error', title: 'Gagal!', text: '{{ session('error') }}', confirmButtonColor: '#C97B2E' });
    });
</script>
@endif

<div class="bg-white rounded-2xl shadow-sm p-6 h-full">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-lg font-bold text-gray-800">Riwayat Pesanan</h2>

        {{-- Legend status --}}
        <div class="flex items-center gap-3 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-yellow-400 inline-block"></span> Pending</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span> Dimasak</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span> Selesai</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-400 inline-block"></span> Dibatalkan</span>
        </div>
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
                    <th class="py-3 px-4 font-semibold text-center">Status</th>
                    <th class="py-3 px-4 rounded-tr-lg font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                {{-- Warna baris berdasarkan status --}}
                @php
                    $rowBg = match($order->status) {
                        'pending'     => 'bg-yellow-50',
                        'proses'      => 'bg-purple-50',
                        'selesai'     => 'bg-green-50',
                        'dibatalkan'  => 'bg-red-50 opacity-70',
                        default       => ''
                    };
                @endphp
                <tr class="hover:brightness-95 transition {{ $rowBg }}" 
                    onclick="toggleDetail({{ $order->id }})" 
                    style="cursor:pointer;">
                    <td class="py-3 px-4">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4 font-semibold text-gray-700">{{ $order->kode_order }}</td>
                    <td class="py-3 px-4">
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ str_replace('_', ' ', ucfirst($order->tipe)) }}</span>
                    </td>
                    <td class="py-3 px-4 capitalize">{{ $order->metode_bayar }}</td>
                    <td class="py-3 px-4 font-bold text-[#C97B2E]">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="py-3 px-4 text-center">
                        @php
                            $badge = match($order->status) {
                                'pending'    => 'bg-yellow-100 text-yellow-700',
                                'proses'     => 'bg-purple-100 text-purple-700',
                                'selesai'    => 'bg-green-100 text-green-700',
                                'dibatalkan' => 'bg-red-100 text-red-600',
                                default      => 'bg-gray-100 text-gray-600'
                            };
                            $label = match($order->status) {
                                'pending'    => '🕐 Menunggu',
                                'proses'     => '👨‍🍳 Sedang Dimasak',
                                'selesai'    => '✅ Selesai',
                                'dibatalkan' => '❌ Dibatalkan',
                                default      => $order->status
                            };
                        @endphp
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                    </td>
                    <td class="py-3 px-4 text-center" onclick="event.stopPropagation()">
                        <div class="flex justify-center items-center gap-2">
                            {{-- Tombol Cetak (selalu tampil kecuali dibatalkan) --}}
                            @if($order->status !== 'dibatalkan')
                            <a href="{{ route('kasir.pesanan.struk', $order->id) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Cetak
                            </a>
                            @endif

                            {{-- Tombol Batalkan (hanya untuk status pending) --}}
                            @if($order->status === 'pending')
                            <button onclick="konfirmasiBatal({{ $order->id }}, '{{ $order->kode_order }}')"
                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition text-xs font-semibold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Batalkan
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- Baris Detail (tersembunyi, muncul saat diklik) --}}
                <tr id="detail-{{ $order->id }}" class="hidden {{ $rowBg }}">
                    <td colspan="7" class="px-6 pb-4 pt-1">
                        <div class="bg-white rounded-xl border border-gray-100 p-4">
                            <p class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide">Detail Item Pesanan</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($order->orderItems as $item)
                                <span class="bg-gray-50 border border-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg">
                                    <span class="font-bold text-[#C97B2E]">{{ $item->jumlah }}x</span>
                                    {{ $item->menu?->nama_produk ?? 'Menu Dihapus' }}
                                    <span class="text-gray-400 ml-1">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </span>
                                @endforeach
                            </div>
                            @if($order->status === 'dibatalkan')
                            <p class="text-xs text-red-500 mt-3 font-medium">❌ Pesanan ini telah dibatalkan.</p>
                            @endif
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-400">Belum ada riwayat pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    // Toggle detail baris
    function toggleDetail(id) {
        const row = document.getElementById('detail-' + id);
        row.classList.toggle('hidden');
    }

    // Konfirmasi pembatalan dengan SweetAlert
    function konfirmasiBatal(id, kodeOrder) {
        Swal.fire({
            title: 'Batalkan Pesanan?',
            html: `Pesanan <strong>${kodeOrder}</strong> akan dibatalkan dan dihapus dari dapur.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-batal-' + id).submit();
            }
        });
    }
</script>

{{-- Form tersembunyi untuk setiap pesanan pending --}}
@foreach($orders as $order)
    @if($order->status === 'pending')
    <form id="form-batal-{{ $order->id }}" 
          action="{{ route('kasir.pesanan.cancel', $order->id) }}" 
          method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>
    @endif
@endforeach

@endsection