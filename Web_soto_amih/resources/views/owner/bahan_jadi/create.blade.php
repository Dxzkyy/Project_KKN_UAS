@extends('layouts.app')

@section('title', 'Tambah Bahan Jadi')

@section('sidebar-menu')
    <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Laporan Penjualan
    </a>
    <a href="{{ route('owner.bahan_jadi.index') }}" class="flex items-center gap-3 bg-white text-[#C97B2E] font-semibold rounded-xl px-4 py-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
        </svg>
        Bahan Jadi
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Penjualan
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
        </svg>
        Arsip Laporan
    </a>
    <a href="{{ route('owner.menu.index') }}" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        Menu
    </a>
@endsection

@section('page-title', 'Tambah Bahan Jadi')

@section('content')
<div class="flex flex-col items-center">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6 w-full max-w-2xl">
        <a href="{{ route('owner.bahan_jadi.index') }}" class="hover:text-[#C97B2E] transition">Bahan Jadi</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-600 font-medium">Tambah Bahan</span>
    </div>

    <div class="bg-white rounded-2xl shadow p-6 w-full max-w-2xl">
        <p class="text-base font-bold text-gray-700 mb-5 pb-3 border-b border-gray-100">Detail Bahan Jadi</p>

        <form action="{{ route('owner.bahan_jadi.store') }}" method="POST">
            @csrf

            {{-- Nama Bahan --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-600 mb-1">
                    Nama Bahan <span class="text-red-400">*</span>
                </label>
                <input type="text" name="nama_bahan" value="{{ old('nama_bahan') }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#C97B2E] focus:ring-1 focus:ring-[#C97B2E] transition"
                       placeholder="Contoh: Daging Ayam Kampung">
                @error('nama_bahan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Stok Awal --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-600 mb-1">
                    Stok Awal <span class="text-red-400">*</span>
                </label>
                <input type="number" name="stok" value="{{ old('stok', 0) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#C97B2E] focus:ring-1 focus:ring-[#C97B2E] transition"
                       placeholder="0" min="0" step="0.01">
                @error('stok')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Satuan --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-600 mb-1">
                    Satuan <span class="text-red-400">*</span>
                </label>
                <div class="flex gap-3">
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="satuan" value="gram" class="hidden peer"
                               {{ old('satuan') == 'gram' ? 'checked' : '' }}>
                        <div class="text-center py-2.5 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-500
                                    peer-checked:border-[#C97B2E] peer-checked:text-[#C97B2E] peer-checked:bg-orange-50 transition">
                            Gram (gr)
                        </div>
                    </label>
                    <label class="flex-1 cursor-pointer">
                        <input type="radio" name="satuan" value="mililiter" class="hidden peer"
                               {{ old('satuan') == 'mililiter' ? 'checked' : '' }}>
                        <div class="text-center py-2.5 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-500
                                    peer-checked:border-[#C97B2E] peer-checked:text-[#C97B2E] peer-checked:bg-orange-50 transition">
                            Mililiter (ml)
                        </div>
                    </label>
                </div>
                @error('satuan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-white font-semibold transition hover:opacity-90"
                        style="background-color: #C97B2E;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Bahan
                </button>
                <a href="{{ route('owner.bahan_jadi.index') }}"
                   class="flex items-center gap-2 px-6 py-2.5 rounded-xl text-gray-600 font-semibold bg-gray-100 hover:bg-gray-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection