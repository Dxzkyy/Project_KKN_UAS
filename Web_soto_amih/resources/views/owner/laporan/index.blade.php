@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('page-title', 'Laporan Penjualan')

@section('header-user')
    <div class="flex items-center gap-4 text-right">
        <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2">
            <div class="w-8 h-8 rounded-full bg-[#C97B2E] flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}
            </div>
            <div class="text-left">
                <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Owner' }}</p>
                <p class="text-xs text-gray-400">Pemilik</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', confirmButtonColor: '#C97B2E', timer: 3000, timerProgressBar: true });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'error', title: 'Oops!', text: '{{ session('error') }}', confirmButtonColor: '#C97B2E' });
    });
</script>
@endif

<div class="flex flex-col gap-6">

    {{-- Header: Info modal + Tombol aksi --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

        {{-- Info modal hari ini --}}
        <div>
            @if($modalHariIni)
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                    <p class="text-sm text-gray-500">Modal hari ini sudah diset:</p>
                    <p class="text-sm font-bold text-gray-800">Rp {{ number_format($modalHariIni->nominal, 0, ',', '.') }}</p>
                    <button onclick="openModalSet()"
                        class="text-xs text-[#C97B2E] hover:underline font-medium ml-1">Ubah</button>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-orange-400 animate-pulse"></div>
                    <p class="text-sm text-orange-600 font-medium">Modal hari ini belum diset — kasir belum bisa lihat laba bersih.</p>
                </div>
            @endif
        </div>

        {{-- Tombol aksi --}}
        <div class="flex gap-2 shrink-0">
            <button onclick="openModalSet()"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm shadow-sm">
                <svg class="w-4 h-4 text-[#C97B2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ $modalHariIni ? 'Ubah Modal' : 'Set Modal Hari Ini' }}
            </button>
            <a href="{{ route('owner.laporan.ekspor_pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                class="flex items-center gap-2 px-4 py-2 bg-[#C97B2E] hover:bg-orange-600 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Ekspor PDF
            </a>
        </div>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <form method="GET" action="{{ route('owner.laporan.index') }}" class="flex flex-wrap gap-3 items-end">

            {{-- Tanggal Mulai --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Mulai</label>
                <input type="date" name="dari" value="{{ request('dari') }}"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E] bg-gray-50">
            </div>

            {{-- Tanggal Selesai --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal Selesai</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E] bg-gray-50">
            </div>

            {{-- Filter Status --}}
            <div class="flex flex-col gap-1">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</label>
                <select name="status"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E] bg-gray-50 pr-8">
                    <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            {{-- Search --}}
            <div class="flex flex-col gap-1 flex-1 min-w-[180px]">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Cari</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. order / nama kasir..."
                        class="w-full border border-gray-200 rounded-xl pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E] bg-gray-50">
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-[#C97B2E] hover:bg-orange-600 text-white rounded-xl text-sm font-semibold transition">
                    Cari
                </button>
                @if(request()->hasAny(['dari', 'sampai', 'status', 'search']))
                <a href="{{ route('owner.laporan.index') }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-semibold transition">
                    Reset
                </a>
                @endif
            </div>

        </form>
    </div>

    {{-- Tabel Laporan Penjualan --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Laporan Penjualan</h3>
            <p class="text-xs text-gray-400 mt-0.5">{{ $orders->total() }} transaksi ditemukan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="py-3 px-5 font-semibold">No</th>
                        <th class="py-3 px-5 font-semibold">Nama Kasir</th>
                        <th class="py-3 px-5 font-semibold">Tanggal</th>
                        <th class="py-3 px-5 font-semibold">Jumlah</th>
                        <th class="py-3 px-5 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-orange-50/40 transition">
                        <td class="py-3.5 px-5 font-bold text-gray-700">{{ $order->kode_order }}</td>
                        <td class="py-3.5 px-5 text-gray-600">{{ $order->kasir?->name ?? '—' }}</td>
                        <td class="py-3.5 px-5 text-gray-500">
                            {{ $order->created_at->setTimezone('Asia/Jakarta')->format('d - m - Y') }}
                        </td>
                        <td class="py-3.5 px-5 font-semibold text-gray-800">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-5 text-center">
                            @if($order->status === 'selesai')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Sudah Kirim Bukti
                                </span>
                            @elseif($order->status === 'dibatalkan')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                    Dibatalkan
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 capitalize">
                                    {{ $order->status }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Belum ada transaksi ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>

{{-- MODAL: Set Modal Harian --}}
<div id="modal-set-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#C97B2E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 text-base">Set Modal Hari Ini</h3>
                <p class="text-xs text-gray-400">{{ $today->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        <form action="{{ route('owner.laporan.set_modal') }}" method="POST">
            @csrf
            <div class="flex flex-col gap-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Modal Belanja Hari Ini <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-gray-400">Rp</span>
                        <input type="number" name="nominal"
                            value="{{ $modalHariIni?->nominal ?? '' }}"
                            placeholder="0"
                            min="0"
                            class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E] bg-gray-50"
                            required>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Modal belanja bahan baku hari ini. Kasir akan otomatis melihat laba bersih.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Catatan (opsional)</label>
                    <textarea name="catatan" rows="2"
                        placeholder="Misal: ada tambahan beli gas, dll..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E] bg-gray-50 resize-none">{{ $modalHariIni?->catatan ?? '' }}</textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModalSet()"
                        class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-[#C97B2E] hover:bg-orange-600 text-white rounded-xl text-sm font-semibold transition">
                        Simpan Modal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalSet() {
        document.getElementById('modal-set-modal').classList.remove('hidden');
    }
    function closeModalSet() {
        document.getElementById('modal-set-modal').classList.add('hidden');
    }
    // Tutup modal saat klik backdrop
    document.getElementById('modal-set-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModalSet();
    });
</script>

@endsection