@extends('layouts.app')

@section('title', 'Edit Menu')

@section('sidebar-menu')
    <a href="{{ route('owner.dashboard') }}"
        class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Laporan Penjualan
    </a>
    <a href="{{ route('owner.bahan_jadi.index') }}"
        class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
        </svg>
        Bahan Jadi
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        Penjualan
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
        </svg>
        Arsip Laporan
    </a>
    <a href="{{ route('owner.menu.index') }}"
        class="flex items-center gap-3 bg-white text-[#C97B2E] font-semibold rounded-xl px-4 py-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Menu
    </a>
@endsection

@section('page-title', 'Edit Menu')

@section('content')
    <div class="flex flex-col items-center">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-6 w-full max-w-5xl">
            <a href="{{ route('owner.menu.index') }}" class="hover:text-[#C97B2E] transition">Menu</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-600 font-medium">Edit Menu</span>
        </div>

        <form action="{{ route('owner.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data"
            class="w-full max-w-5xl">
            @csrf
            @method('PUT')
            <div class="flex gap-6">

                {{-- Kolom Kiri: Upload Foto --}}
                <div class="w-1/2 flex-shrink-0">
                    <div class="bg-white rounded-2xl shadow p-5 h-full">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Foto Produk</p>
                        <div id="drop-area" onclick="document.getElementById('foto').click()"
                            class="relative border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:border-[#C97B2E] transition-all"
                            style="height: 300px;">
                            <img id="preview" src="{{ asset('storage/menus/' . $menu->foto) }}"
                                alt="{{ $menu->nama_produk }}"
                                class="absolute inset-0 w-full h-full object-cover rounded-2xl">
                            <div id="overlay"
                                class="absolute inset-0 bg-black bg-opacity-40 rounded-2xl flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                <div class="flex flex-col items-center gap-2 text-white">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-semibold">Klik untuk ganti foto</p>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="foto" name="foto" accept="image/*" class="hidden"
                            onchange="previewFoto(event)">
                        <p class="text-xs text-gray-400 mt-2 text-center">Kosongkan jika tidak ingin mengganti foto</p>
                        @error('foto')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Kolom Kanan: Detail + Resep --}}
                <div class="w-1/2 flex flex-col gap-5">

                    {{-- Detail Produk --}}
                    <div class="bg-white rounded-2xl shadow p-6">
                        <p class="text-base font-bold text-gray-700 mb-5 pb-3 border-b border-gray-100">Detail Produk</p>

                        {{-- Kode Produk --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-600 mb-1">Kode Produk</label>
                            <input type="text" value="{{ $menu->kode_produk }}" disabled
                                class="w-full border border-gray-100 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                        </div>

                        {{-- Nama Produk --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                Nama Produk <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="nama_produk" value="{{ old('nama_produk', $menu->nama_produk) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#C97B2E] focus:ring-1 focus:ring-[#C97B2E] transition">
                            @error('nama_produk')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="mb-5">
                            <label class="block text-sm font-semibold text-gray-600 mb-1">
                                Kategori <span class="text-red-400">*</span>
                            </label>
                            <div class="flex gap-3">
                                @foreach (['Makanan', 'Minuman', 'Lainnya'] as $kat)
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="kategori" value="{{ $kat }}"
                                            class="hidden peer"
                                            {{ old('kategori', $menu->kategori) == $kat ? 'checked' : '' }}>
                                        <div
                                            class="text-center py-2.5 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-500
                                            peer-checked:border-[#C97B2E] peer-checked:text-[#C97B2E] peer-checked:bg-orange-50 transition">
                                            {{ $kat }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('kategori')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Harga & Stok --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">
                                    Harga <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">Rp</span>
                                    <input type="number" name="harga" value="{{ old('harga', $menu->harga) }}"
                                        class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#C97B2E] focus:ring-1 focus:ring-[#C97B2E] transition">
                                </div>
                                @error('harga')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-1">Stok</label>
                                <div
                                    class="w-full border border-gray-100 rounded-xl px-4 py-2.5 text-sm bg-gray-50 text-gray-400 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $menu->stok_otomatis }} porsi (dihitung otomatis)
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Resep Bahan --}}
                    <div class="bg-white rounded-2xl shadow p-6">
                        <p class="text-base font-bold text-gray-700 mb-1 pb-3 border-b border-gray-100">Resep Bahan</p>
                        <p class="text-xs text-gray-400 mb-4">Tentukan kebutuhan bahan per 1 porsi</p>

                        <div class="flex flex-col gap-3">
                            @foreach ($bahans as $bahan)
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 text-sm font-medium text-gray-700">
                                        {{ $bahan->nama_bahan }}
                                        <span class="text-xs text-gray-400">({{ $bahan->satuan }})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="bahan[{{ $bahan->id }}]"
                                            value="{{ old('bahan.' . $bahan->id, isset($resep[$bahan->id]) ? $resep[$bahan->id]->pivot->kebutuhan : '') }}"
                                            class="w-28 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-[#C97B2E] transition"
                                            placeholder="0" min="0" step="0.01">
                                        <span class="text-xs text-gray-400 w-12">{{ $bahan->satuan }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-semibold transition hover:opacity-90"
                            style="background-color: #C97B2E;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Update Menu
                        </button>
                        <a href="{{ route('owner.menu.index') }}"
                            class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-gray-600 font-semibold bg-gray-100 hover:bg-gray-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batal
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        function previewFoto(event) {
            const file = event.target.files[0];
            if (!file) return;
            const preview = document.getElementById('preview');
            preview.src = URL.createObjectURL(file);
        }
    </script>
@endsection
