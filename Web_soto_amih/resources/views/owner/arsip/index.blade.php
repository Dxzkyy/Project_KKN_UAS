@extends('layouts.app')

@section('title', 'Arsip Laporan')

@section('sidebar-menu')
@include('owner.partials.sidebar')
@endsection

@section('page-title', 'Arsip Laporan')

@section('header-user')
    <div class="flex items-center gap-2 bg-gray-100 rounded-xl px-3 py-2">
        <div class="w-8 h-8 rounded-full bg-[#C97B2E] flex items-center justify-center text-white font-bold text-sm">
            {{ strtoupper(substr(auth()->user()->name ?? 'O', 0, 1)) }}
        </div>
        <div class="text-left">
            <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name ?? 'Owner' }}</p>
            <p class="text-xs text-gray-400">Owner</p>
        </div>
    </div>
@endsection

@section('content')
<div class="flex flex-col gap-6">

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4">
        <form method="GET" action="{{ route('owner.arsip.index') }}" class="flex items-center gap-3 flex-wrap">
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Filter Bulan</label>
                <input type="month" name="bulan" value="{{ request('bulan') }}"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E]">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1 font-medium">Filter Tahun</label>
                <select name="tahun" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#C97B2E]">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunTersedia as $thn)
                        <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit"
                    class="px-4 py-2 bg-[#C97B2E] text-white rounded-xl text-sm font-semibold hover:bg-orange-600 transition">
                    Terapkan
                </button>
                <a href="{{ route('owner.arsip.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Tabel Arsip --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Daftar Arsip Laporan Harian</h3>
            <span class="text-sm text-gray-400">{{ $arsipList->total() }} laporan tersimpan</span>
        </div>

        @if($arsipList->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="py-3 px-4 rounded-tl-lg font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Kasir</th>
                        <th class="py-3 px-4 font-semibold">Total Pesanan</th>
                        <th class="py-3 px-4 font-semibold">Pendapatan Kotor</th>
                        <th class="py-3 px-4 font-semibold">Modal</th>
                        <th class="py-3 px-4 font-semibold">Laba Bersih</th>
                        <th class="py-3 px-4 rounded-tr-lg font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($arsipList as $arsip)
                    <tr class="hover:bg-orange-50 transition">
                        <td class="py-3 px-4">
                            <p class="font-semibold text-gray-800">{{ Carbon\Carbon::parse($arsip->tanggal)->translatedFormat('d F Y') }}</p>
                            <p class="text-xs text-gray-400">{{ Carbon\Carbon::parse($arsip->tanggal)->translatedFormat('l') }}</p>
                        </td>
                        <td class="py-3 px-4 text-gray-600">{{ $arsip->kasir?->name ?? '—' }}</td>
                        <td class="py-3 px-4 text-gray-700">{{ $arsip->total_pesanan }} order</td>
                        <td class="py-3 px-4 font-semibold text-gray-800">
                            Rp {{ number_format($arsip->pendapatan_kotor, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-gray-600">
                            {{ $arsip->modal_harian !== null ? 'Rp ' . number_format($arsip->modal_harian, 0, ',', '.') : '—' }}
                        </td>
                        <td class="py-3 px-4 font-bold {{ ($arsip->laba_bersih ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $arsip->laba_bersih !== null ? 'Rp ' . number_format($arsip->laba_bersih, 0, ',', '.') : '—' }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Detail --}}
                                <a href="{{ route('owner.arsip.show', $arsip->id) }}"
                                    class="flex items-center gap-1 px-3 py-1.5 bg-orange-100 text-[#C97B2E] rounded-lg text-xs font-semibold hover:bg-orange-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Detail
                                </a>
                                {{-- Cetak PDF --}}
                                <a href="{{ route('owner.arsip.pdf', $arsip->id) }}" target="_blank"
                                    class="flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    Cetak
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $arsipList->links() }}
        </div>

        @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
            </svg>
            <p class="text-gray-400 font-medium">Belum ada arsip laporan</p>
            <p class="text-gray-300 text-sm mt-1">Laporan akan muncul setelah kasir mengirimkan laporan harian</p>
        </div>
        @endif
    </div>

</div>
@endsection