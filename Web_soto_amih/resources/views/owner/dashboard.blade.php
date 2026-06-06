@extends('layouts.app')

@section('title', 'Dashboard Pemilik')

@section('sidebar-menu')
    <a href="#" class="flex items-center gap-3 bg-white text-[#C97B2E] font-semibold rounded-xl px-4 py-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Laporan Penjualan
    </a>
    <a href="{{ route('owner.bahan_jadi.index') }}" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
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
        class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
        </svg>
        Menu
    </a>
@endsection

@section('page-title', 'Dashboard Pemilik')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('login_success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Masuk!',
                    text: "{{ session('login_success') }}",
                    confirmButtonColor: '#C97B2E',
                    timer: 3000,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    <p class="text-gray-400 italic">Konten akan diisi setelah integrasi data.</p>
@endsection
