@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('sidebar-menu')
    <a href="{{ route('kasir.pesanan.index') }}" class="flex items-center gap-3 bg-white text-[#C97B2E] font-semibold rounded-xl px-4 py-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Pesanan
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
        </svg>
        Kirim Laporan ke Pemilik
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Riwayat Pesanan
    </a>
@endsection

@section('page-title', 'Dashboard Kasir')

@section('header-user')
    <div class="flex items-center gap-2 text-right">
        <div>
            <p class="text-xs text-gray-400">Waktu</p>
            <p class="text-sm font-semibold text-gray-700" id="clock">--:--</p>
        </div>
        <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2">
            <div class="w-8 h-8 rounded-full bg-[#C97B2E] flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">Kasir</p>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
@endsection

@section('content')
    <p class="text-gray-400 italic">Konten akan diisi setelah integrasi data.</p>
@endsection