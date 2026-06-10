<a href="{{ route('kasir.pesanan.index') }}"
        class="flex items-center gap-3 rounded-xl px-4 py-2 transition {{ request()->routeIs('kasir.pesanan.index') ? 'bg-white text-[#C97B2E] font-semibold' : 'text-white hover:bg-orange-600' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Proses Pesanan
    </a>
    
    {{-- Karena rute ini belum ada, saya beri contoh 'kasir.laporan.*' --}}
    <a href="#" 
        class="flex items-center gap-3 rounded-xl px-4 py-2 transition {{ request()->routeIs('kasir.laporan.*') ? 'bg-white text-[#C97B2E] font-semibold' : 'text-white hover:bg-orange-600' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
        </svg>
        Kirim Laporan ke Pemilik
    </a>
    
    <a href="{{ route('kasir.pesanan.riwayat') }}" 
        class="flex items-center gap-3 rounded-xl px-4 py-2 transition {{ request()->routeIs('kasir.pesanan.riwayat') ? 'bg-white text-[#C97B2E] font-semibold' : 'text-white hover:bg-orange-600' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Riwayat Pesanan
    </a>