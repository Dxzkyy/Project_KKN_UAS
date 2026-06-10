@extends('layouts.app')

@section('title', 'Bahan Jadi')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('page-title', 'Bahan Jadi')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#C97B2E',
                timer: 2500,
                timerProgressBar: true,
            });
        });
    </script>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Stok Bahan Jadi</h2>
            <p class="text-sm text-gray-400 mt-0.5">Update stok bahan setiap hari</p>
        </div>
        <a href="{{ route('owner.bahan_jadi.create') }}"
           class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-white font-semibold transition hover:opacity-90 shadow-sm"
           style="background-color: #C97B2E;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Bahan
        </a>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr style="background-color: #FDF3E7;">
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Update Stok Harian</th>
                        <th class="py-3.5 px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bahans as $bahan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-4 px-4 font-semibold text-gray-800">{{ $bahan->nama_bahan }}</td>
                        <td class="py-4 px-4">
                            <span class="font-bold text-lg {{ $bahan->stok <= 500 ? 'text-red-500' : 'text-gray-700' }}">
                                {{ number_format($bahan->stok, 0, ',', '.') }}
                            </span>
                            @if($bahan->stok <= 500)
                                <span class="ml-1 text-xs text-red-400">⚠ Stok menipis</span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $bahan->satuan == 'gram' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600' }}">
                                {{ $bahan->satuan }}
                            </span>
                        </td>

                        {{-- Form update stok harian --}}
                        <td class="py-4 px-4">
                            <form action="{{ route('owner.bahan_jadi.update_stok', $bahan->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="stok" value="{{ $bahan->stok }}"
                                       class="w-32 border border-gray-200 rounded-xl px-3 py-1.5 text-sm focus:outline-none focus:border-[#C97B2E]"
                                       min="0" step="0.01">
                                <button type="submit"
                                        class="px-3 py-1.5 rounded-xl text-white text-xs font-semibold transition hover:opacity-90"
                                        style="background-color: #C97B2E;">
                                    Simpan
                                </button>
                            </form>
                        </td>

                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('owner.bahan_jadi.edit', $bahan->id) }}"
                                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form id="delete-form-{{ $bahan->id }}" action="{{ route('owner.bahan_jadi.destroy', $bahan->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            onclick="confirmDelete({{ $bahan->id }})"
                                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition text-xs font-semibold">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-16">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                                <p class="font-medium">Belum ada bahan jadi</p>
                                <a href="{{ route('owner.bahan_jadi.create') }}"
                                   class="px-4 py-2 rounded-xl text-white text-sm font-semibold"
                                   style="background-color: #C97B2E;">
                                    Tambah Bahan Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Bahan?',
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