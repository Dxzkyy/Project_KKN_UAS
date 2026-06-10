@extends('layouts.app')

@section('title', 'Laporan Harian')

@section('sidebar-menu')
@include('kasir.partials.sidebar')
@endsection

@section('page-title', 'Laporan Harian')

@section('header-user')
    <div class="flex items-center gap-4 text-right">
        <div>
            <p class="text-xs text-gray-400">Waktu</p>
            <p class="text-sm font-semibold text-gray-700" id="clock">--:--</p>
        </div>
        <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2">
            <div class="w-8 h-8 rounded-full bg-[#C97B2E] flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'K', 0, 1)) }}
            </div>
            <div class="text-left">
                <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Kasir' }}</p>
                <p class="text-xs text-gray-400">Kasir</p>
            </div>
        </div>
    </div>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: '{{ session('success') }}', confirmButtonColor: '#C97B2E', timer: 3000, timerProgressBar: true });
    });
</script>
@endif

<div class="flex flex-col gap-6">

    {{-- Header Tanggal + Tombol --}}
    <div class="flex justify-between items-center">
        <div>
            <p class="text-sm text-gray-500">Rekapitulasi transaksi untuk</p>
            <p class="text-lg font-bold text-gray-800">{{ $today->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex gap-3">
            {{-- Download PDF --}}
            <button onclick="window.print()"
                class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Download PDF
            </button>

            {{-- Tombol Kirim ke Pemilik — selalu aktif --}}
            <button onclick="bukaModalKirim()"
                class="flex items-center gap-2 px-4 py-2 bg-[#C97B2E] hover:bg-orange-600 text-white font-semibold rounded-xl transition text-sm shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Kirim ke Pemilik
            </button>
        </div>
    </div>

    {{-- 3 Kartu Statistik --}}
    <div class="grid grid-cols-3 gap-4">

        {{-- Kartu 1: Pendapatan Kotor --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-[#C97B2E]">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total Pendapatan Kotor</p>
            <p class="text-2xl font-bold text-gray-800">
                Rp {{ number_format($pendapatanKotor, 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Seluruh transaksi hari ini (non-batal)</p>
        </div>

        {{-- Kartu 2: Total Pesanan --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-blue-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total Pesanan</p>
            <p class="text-2xl font-bold text-gray-800">{{ $totalPesanan }} Order</p>
            <p class="text-xs text-gray-400 mt-1">Pesanan masuk hari ini</p>
        </div>

        {{-- Kartu 3: Laba Bersih --}}
        <div class="bg-white rounded-2xl shadow-sm p-5 border-l-4 border-green-400">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Laba Bersih</p>
            @if($modalHariIni)
                <p class="text-2xl font-bold {{ $labaBersih >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    Rp {{ number_format($labaBersih, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400 mt-1">Modal: Rp {{ number_format($modalHariIni->nominal, 0, ',', '.') }}</p>
            @else
                <p class="text-2xl font-bold text-gray-300">—</p>
                <p class="text-xs text-orange-400 mt-1">Menunggu pemilik set modal hari ini</p>
            @endif
        </div>
    </div>

    {{-- Grafik Penjualan Bulanan --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Grafik Penjualan Bulanan</h3>
        <canvas id="grafikPenjualan" height="80"></canvas>
    </div>

    {{-- Rincian Transaksi Hari Ini --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Rincian Transaksi Hari Ini</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-3 px-4 rounded-tl-lg font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Order ID</th>
                        <th class="py-3 px-4 font-semibold">Items</th>
                        <th class="py-3 px-4 font-semibold">Total</th>
                        <th class="py-3 px-4 font-semibold">Pembayaran</th>
                        <th class="py-3 px-4 rounded-tr-lg font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($ordersHariIni as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 text-gray-500">{{ $order->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-700">{{ $order->kode_order }}</td>
                        <td class="py-3 px-4 text-gray-600">
                            @foreach($order->orderItems as $item)
                                <span class="text-xs">{{ $item->jumlah }}x {{ $item->menu?->nama_produk ?? 'Menu Dihapus' }}</span>@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="py-3 px-4 font-bold text-[#C97B2E]">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        <td class="py-3 px-4">
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs capitalize">{{ $order->metode_bayar }}</span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php
                                $badge = match($order->status) {
                                    'pending'  => 'bg-yellow-100 text-yellow-700',
                                    'proses'   => 'bg-purple-100 text-purple-700',
                                    'selesai'  => 'bg-green-100 text-green-700',
                                    default    => 'bg-gray-100 text-gray-500'
                                };
                                $label = match($order->status) {
                                    'pending'  => 'Menunggu',
                                    'proses'   => 'Dimasak',
                                    'selesai'  => 'Selesai',
                                    default    => $order->status
                                };
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ $label }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-400">Belum ada transaksi hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Riwayat Laporan Terkirim --}}
    @if($riwayatLaporan->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-800 mb-4">Riwayat Laporan Terkirim</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-3 px-4 rounded-tl-lg font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Pendapatan Kotor</th>
                        <th class="py-3 px-4 font-semibold">Total Pesanan</th>
                        <th class="py-3 px-4 font-semibold">Modal</th>
                        <th class="py-3 px-4 rounded-tr-lg font-semibold">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($riwayatLaporan as $lap)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium text-gray-700">{{ $lap->tanggal->translatedFormat('d F Y') }}</td>
                        <td class="py-3 px-4 text-gray-700">Rp {{ number_format($lap->pendapatan_kotor, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $lap->total_pesanan }} order</td>
                        <td class="py-3 px-4 text-gray-700">
                            {{ $lap->modal_harian !== null ? 'Rp ' . number_format($lap->modal_harian, 0, ',', '.') : '—' }}
                        </td>
                        <td class="py-3 px-4 font-bold {{ ($lap->laba_bersih ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $lap->laba_bersih !== null ? 'Rp ' . number_format($lap->laba_bersih, 0, ',', '.') : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- MODAL: Konfirmasi Kirim Laporan --}}
<div id="modal-kirim" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="font-bold text-gray-800 text-lg mb-1">Kirim Laporan ke Pemilik</h3>
        <p class="text-sm text-gray-500 mb-5">Pastikan semua transaksi sudah selesai sebelum mengirim laporan.</p>

        <form id="form-kirim" action="{{ route('kasir.laporan.kirim') }}" method="POST">
            @csrf
            <div class="flex flex-col gap-4">

                {{-- Ringkasan --}}
                <div class="bg-orange-50 rounded-xl p-4 text-sm">
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-600">Pendapatan Kotor</span>
                        <span class="font-semibold">Rp {{ number_format($pendapatanKotor, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mb-1">
                        <span class="text-gray-600">Total Pesanan</span>
                        <span class="font-semibold">{{ $totalPesanan }} order</span>
                    </div>
                    @if($modalHariIni)
                    <div class="flex justify-between pt-2 border-t border-orange-100 mt-1">
                        <span class="text-gray-600">Modal (dari Pemilik)</span>
                        <span class="font-semibold">Rp {{ number_format($modalHariIni->nominal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="font-semibold text-gray-700">Laba Bersih</span>
                        <span class="font-bold {{ $labaBersih >= 0 ? 'text-green-600' : 'text-red-500' }}">
                            Rp {{ number_format($labaBersih, 0, ',', '.') }}
                        </span>
                    </div>
                    @else
                    <div class="flex justify-between pt-2 border-t border-orange-100 mt-1">
                        <span class="text-gray-500">Modal</span>
                        <span class="text-orange-400 text-xs">Belum diset pemilik</span>
                    </div>
                    @endif
                </div>

                {{-- Catatan opsional kasir --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (opsional)</label>
                    <textarea id="input-catatan" name="catatan" rows="2" placeholder="Misal: ada promo hari ini, ramai, dll..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E] resize-none"></textarea>
                </div>

                <div class="flex gap-3 mt-1">
                    <button type="button" onclick="tutupModal()"
                        class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-[#C97B2E] hover:bg-orange-600 text-white rounded-xl text-sm font-semibold transition">
                        Kirim Sekarang
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalKirim() {
        // Reset textarea catatan setiap kali modal dibuka
        document.getElementById('input-catatan').value = '';
        document.getElementById('modal-kirim').classList.remove('hidden');
    }

    function tutupModal() {
        document.getElementById('modal-kirim').classList.add('hidden');
    }

    // Tutup modal jika klik backdrop
    document.getElementById('modal-kirim').addEventListener('click', function(e) {
        if (e.target === this) tutupModal();
    });

    // Jam WIB
    function updateClock() {
        const now = new Date();
        const wib = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }));
        const h = String(wib.getHours()).padStart(2, '0');
        const m = String(wib.getMinutes()).padStart(2, '0');
        const s = String(wib.getSeconds()).padStart(2, '0');
        document.getElementById('clock').textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Grafik Chart.js
    const grafikData = @json($grafikData);
    const labels = grafikData.map(d => d.bulan);
    const values = grafikData.map(d => d.total);

    new Chart(document.getElementById('grafikPenjualan'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data: values,
                borderColor: '#C97B2E',
                backgroundColor: 'rgba(201, 123, 46, 0.1)',
                borderWidth: 2.5,
                pointBackgroundColor: '#C97B2E',
                pointRadius: 4,
                tension: 0.4,
                fill: true,
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
                        callback: val => 'Rp ' + (val / 1000).toLocaleString('id-ID') + 'rb'
                    }
                }
            }
        }
    });
</script>

@endsection