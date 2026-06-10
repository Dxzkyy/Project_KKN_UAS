@extends('layouts.app')

@section('title', 'Menu')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('page-title', 'Menu')

@section('content')

    {{-- SweetAlert CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#C97B2E',
                    timer: 2500,
                    timerProgressBar: true,
                });
            });
        </script>
    @endif

    {{-- Header & Tambah --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Daftar Menu</h2>
            <p class="text-sm text-gray-400 mt-0.5">Kelola semua menu yang tersedia</p>
        </div>
        <a href="{{ route('owner.menu.create') }}"
            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold transition hover:opacity-90 shadow-sm"
            style="background-color: #C97B2E;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Menu
        </a>
    </div>

    {{-- Filter Kategori --}}
    <div class="flex items-center gap-2 mb-5">
        <button onclick="filterKategori('Semua')"
            class="filter-btn active px-4 py-1.5 rounded-full text-sm font-medium transition" data-kategori="Semua">
            Semua
        </button>
        <button onclick="filterKategori('Makanan')"
            class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium transition" data-kategori="Makanan">
            Makanan
        </button>
        <button onclick="filterKategori('Minuman')"
            class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium transition" data-kategori="Minuman">
            Minuman
        </button>
        <button onclick="filterKategori('Lainnya')"
            class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium transition" data-kategori="Lainnya">
            Lainnya
        </button>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="menuTable">
                <thead>
                    <tr style="background-color: #FDF3E7;">
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($menus as $menu)
                        <tr class="menu-row hover:bg-gray-50 transition" data-kategori="{{ $menu->kategori }}">

                            {{-- Foto + Nama --}}
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('storage/menus/' . $menu->foto) }}" alt="{{ $menu->nama_produk }}"
                                        class="w-14 h-14 object-cover rounded-xl shadow-sm">
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $menu->nama_produk }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Ditambah
                                            {{ $menu->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Kode --}}
                            <td class="py-4 px-4">
                                <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">
                                    {{ $menu->kode_produk }}
                                </span>
                            </td>

                            {{-- Kategori --}}
                            <td class="py-4 px-4">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $menu->kategori == 'Makanan' ? 'bg-orange-100 text-orange-600' : '' }}
                                {{ $menu->kategori == 'Minuman' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $menu->kategori == 'Lainnya' ? 'bg-gray-100 text-gray-600' : '' }}">
                                    {{ $menu->kategori }}
                                </span>
                            </td>

                            {{-- Harga --}}
                            <td class="py-4 px-4 font-semibold text-gray-700">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </td>

                            {{-- Stok --}}
                            <td class="py-4 px-4">
                                <span
                                    class="font-semibold {{ $menu->stok_otomatis <= 5 ? 'text-red-500' : 'text-gray-700' }}">
                                    {{ $menu->stok_otomatis }} porsi
                                </span>
                                @if ($menu->stok_otomatis <= 5)
                                    <span class="ml-1 text-xs text-red-400">⚠ Stok tipis</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('owner.menu.edit', $menu->id) }}"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form id="delete-form-{{ $menu->id }}"
                                        action="{{ route('owner.menu.destroy', $menu->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $menu->id }})"
                                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition text-xs font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-16">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="font-medium">Belum ada menu</p>
                                    <a href="{{ route('owner.menu.create') }}"
                                        class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
                                        style="background-color: #C97B2E;">
                                        Tambah Menu Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($menus->hasPages())
            <div class="px-4 py-4 border-t border-gray-100">
                {{ $menus->links() }}
            </div>
        @endif
    </div>

    <style>
        .filter-btn {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .filter-btn.active {
            background-color: #C97B2E;
            color: white;
        }
    </style>

    <script>
        function filterKategori(kategori) {
            // Update tombol aktif
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.kategori === kategori) {
                    btn.classList.add('active');
                }
            });

            // Filter baris tabel
            document.querySelectorAll('.menu-row').forEach(row => {
                if (kategori === 'Semua' || row.dataset.kategori === kategori) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Menu?',
                text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#C97B2E',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

@endsection
