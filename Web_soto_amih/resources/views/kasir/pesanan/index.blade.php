@extends('layouts.app')

@section('title', 'Proses Pesanan')

@section('sidebar-menu')
    <a href="{{ route('kasir.pesanan.index') }}"
        class="flex items-center gap-3 bg-white text-[#C97B2E] font-semibold rounded-xl px-4 py-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Proses Pesanan
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
        </svg>
        Kirim Laporan ke Pemilik
    </a>
    <a href="#" class="flex items-center gap-3 text-white hover:bg-orange-600 rounded-xl px-4 py-2 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Riwayat Pesanan
    </a>
@endsection

@section('page-title', 'Proses Pesanan')

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
    <div class="grid grid-cols-12 gap-6 h-full">

        {{-- KOLOM KIRI: KATALOG MENU (Span 8) --}}
        <div class="col-span-8 flex flex-col gap-4">

            {{-- Search & Filter --}}
            <div class="bg-white p-4 rounded-2xl shadow-sm flex flex-col gap-4">
                <div class="relative">
                    <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="searchInput" placeholder="Cari produk (F2)"
                        class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-[#C97B2E] focus:border-[#C97B2E] outline-none transition">
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-1.5 bg-[#C97B2E] text-white text-sm font-medium rounded-lg">Semua produk</button>
                    <button
                        class="px-4 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 text-sm font-medium rounded-lg transition">Makanan</button>
                    <button
                        class="px-4 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 text-sm font-medium rounded-lg transition">Minuman</button>
                </div>
            </div>

            {{-- Grid Menu --}}
            <div class="grid grid-cols-3 gap-4 overflow-y-auto" style="max-height: calc(100vh - 280px);">

                @foreach ($menus as $item)
                    <div class="bg-white rounded-2xl p-3 shadow-sm hover:shadow-md transition cursor-pointer group">
                        <div class="relative h-32 mb-3 rounded-xl overflow-hidden">
                            {{-- Foto disesuaikan dengan folder menus/ dan kolom foto --}}
                            <img src="{{ asset('storage/menus/' . $item->foto) }}" alt="{{ $item->nama_produk }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <span
                                class="absolute top-2 right-2 bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded-md">{{ $item->kategori }}</span>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                {{-- Nama, Kode, Harga, dan Stok disesuaikan dengan kolom asli --}}
                                <h3 class="font-semibold text-gray-800 text-sm">{{ $item->nama_produk }}</h3>
                                <p class="text-xs text-gray-400 mb-1">{{ $item->kode_produk }}</p>
                                <p class="font-bold text-[#C97B2E]">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-500 mt-1">Stok: {{ $item->stok_otomatis }}</p>
                            </div>
                            <button class="bg-[#C97B2E] hover:bg-orange-600 text-white p-1.5 rounded-lg transition"
                                onclick="tambahKeKeranjang({{ $item->id }}, '{{ $item->nama_produk }}', {{ $item->harga }})">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- KOLOM KANAN: RINGKASAN PEMBAYARAN (Span 4) --}}
        <div class="col-span-4 bg-white rounded-2xl shadow-sm flex flex-col h-full"
            style="max-height: calc(100vh - 120px);">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 text-lg">Ringkasan Pembayaran</h2>
            </div>

            {{-- Area Keranjang --}}
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50/50" id="cartContainer">
                <div class="flex flex-col items-center justify-center h-full text-gray-400 opacity-70">
                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <p class="text-sm">Keranjang masih kosong</p>
                </div>
            </div>

            {{-- Kalkulasi & Checkout --}}
            <div class="p-4 border-t border-gray-100 flex flex-col gap-4">

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Sub total</span>
                    <span class="font-semibold text-gray-800" id="subtotalLabel">Rp 0</span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 w-24">Diskon (F7)</span>
                    <select id="tipeDiskon"
                        class="border border-gray-200 rounded-lg text-sm p-1.5 focus:ring-[#C97B2E] outline-none">
                        <option value="persen">%</option>
                        <option value="nominal">Rp</option>
                    </select>
                    <input type="number" id="inputDiskon" placeholder="0"
                        class="flex-1 border border-gray-200 rounded-lg text-sm p-1.5 focus:ring-[#C97B2E] outline-none text-right">
                </div>

                <div class="flex justify-between items-center pt-2 border-t border-dashed border-gray-200">
                    <span class="font-bold text-gray-800">Total pembayaran</span>
                    <span class="font-bold text-[#C97B2E] text-xl" id="totalLabel">Rp 0</span>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-2">Metode pembayaran:</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] focus:border-[#C97B2E] focus:bg-orange-50 transition">Tunai</button>
                        <button
                            class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] focus:border-[#C97B2E] focus:bg-orange-50 transition">QRIS</button>
                        <button
                            class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] focus:border-[#C97B2E] focus:bg-orange-50 transition">Bank</button>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-2">Pilihan:</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button
                            class="border border-[#C97B2E] bg-orange-50 rounded-lg py-2 text-xs font-bold text-[#C97B2E] flex justify-center items-center gap-1">Dine
                            in</button>
                        <button
                            class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] transition flex justify-center items-center gap-1">Takeaway</button>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-2">
                    {{-- Tombol sudah dipasang ID btnBayar --}}
                    <button id="btnBayar"
                        class="w-full bg-[#C97B2E] hover:bg-orange-600 text-white font-bold py-3 rounded-xl shadow-md transition flex justify-center items-center gap-2">
                        Bayar (F9)
                    </button>
                    {{-- Tombol sudah dipasang ID btnHapusKeranjang --}}
                    <button id="btnHapusKeranjang"
                        class="w-full border border-gray-200 hover:bg-red-50 text-red-500 font-semibold py-2.5 rounded-xl transition flex justify-center items-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus keranjang
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- CDN SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // --- 1. SCRIPT JAM REAL-TIME ---
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- 2. LOGIKA KERANJANG (CART) ---
        let cart = [];

        // Format angka ke Rupiah
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        };

        // Pemanis Notifikasi Toast (SweetAlert)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
        });

        // Fungsi Tambah ke Keranjang
        window.tambahKeKeranjang = (id, nama, harga) => {
            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                existingItem.qty += 1;
            } else {
                cart.push({
                    id,
                    nama,
                    harga,
                    qty: 1
                });
            }

            renderCart();

            Toast.fire({
                icon: 'success',
                title: `${nama} ditambahkan!`
            });
        };

        // Fungsi Ubah Kuantitas
        window.ubahQty = (index, delta) => {
            cart[index].qty += delta;
            if (cart[index].qty <= 0) {
                cart.splice(index, 1);
            }
            renderCart();
        };

        // Render HTML Keranjang
        const renderCart = () => {
            const container = document.getElementById('cartContainer');

            if (cart.length === 0) {
                container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-400 opacity-70">
                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Keranjang masih kosong</p>
                </div>`;
            } else {
                let html = '<div class="flex flex-col gap-3">';
                cart.forEach((item, index) => {
                    html += `
                    <div class="bg-white p-3 rounded-xl shadow-sm flex justify-between items-center border border-gray-100">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-800">${item.nama}</h4>
                            <p class="text-xs text-[#C97B2E] font-bold">${formatRupiah(item.harga)}</p>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-50 rounded-lg p-1 border border-gray-200">
                            <button onclick="ubahQty(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-white text-gray-600 rounded shadow-sm hover:bg-gray-100 font-bold">-</button>
                            <span class="text-sm font-semibold w-6 text-center">${item.qty}</span>
                            <button onclick="ubahQty(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-[#C97B2E] text-white rounded shadow-sm hover:bg-orange-600 font-bold">+</button>
                        </div>
                    </div>
                `;
                });
                html += '</div>';
                container.innerHTML = html;
            }
            kalkulasiTotal();
        };

        // Fungsi Kalkulasi Subtotal & Total
        const kalkulasiTotal = () => {
            let subtotal = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            let diskonVal = parseFloat(document.getElementById('inputDiskon').value) || 0;
            let tipeDiskon = document.getElementById('tipeDiskon').value;
            let totalDiskon = 0;

            if (tipeDiskon === 'persen') {
                totalDiskon = subtotal * (diskonVal / 100);
            } else {
                totalDiskon = diskonVal;
            }

            let totalAkhir = subtotal - totalDiskon;
            if (totalAkhir < 0) totalAkhir = 0;

            document.getElementById('subtotalLabel').textContent = formatRupiah(subtotal);
            document.getElementById('totalLabel').textContent = formatRupiah(totalAkhir);
        };

        document.getElementById('inputDiskon').addEventListener('input', kalkulasiTotal);
        document.getElementById('tipeDiskon').addEventListener('change', kalkulasiTotal);

        // --- 3. SWEETALERT UNTUK TOMBOL AKSI ---

        // Tombol Hapus Keranjang
        document.getElementById('btnHapusKeranjang').addEventListener('click', () => {
            if (cart.length === 0) return;

            Swal.fire({
                title: 'Hapus Keranjang?',
                text: "Semua pesanan yang belum dibayar akan hilang!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    document.getElementById('inputDiskon').value = '';
                    renderCart();
                    Swal.fire('Terhapus!', 'Keranjang berhasil dikosongkan.', 'success');
                }
            });
        });

        // Tombol Bayar (Terhubung dengan Backend)
        document.getElementById('btnBayar').addEventListener('click', () => {
            if (cart.length === 0) {
                Swal.fire('Eits!', 'Keranjang masih kosong, pilih menu dulu ya.', 'info');
                return;
            }

            // Kalkulasi ulang untuk Payload
            let subtotal = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            let diskonVal = parseFloat(document.getElementById('inputDiskon').value) || 0;
            let tipeDiskon = document.getElementById('tipeDiskon').value;
            let totalDiskon = (tipeDiskon === 'persen') ? subtotal * (diskonVal / 100) : diskonVal;
            let totalAkhir = Math.max(subtotal - totalDiskon, 0);

            // Siapkan data yang akan dikirim ke Controller
            let payload = {
                _token: '{{ csrf_token() }}',
                nama_pembeli: 'Pelanggan Umum', // Dummy sementara
                tipe: 'dine_in',
                metode_bayar: 'tunai',
                diskon: diskonVal,
                tipe_diskon: tipeDiskon,
                total: totalAkhir,
                cart: cart
            };

            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: "Data pesanan akan disimpan ke sistem.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#C97B2E',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    // Proses pengiriman data via AJAX Fetch
                    fetch('{{ route('kasir.pesanan.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                    // Reset tampilan jika berhasil
                                    cart = [];
                                    document.getElementById('inputDiskon').value = '';
                                    renderCart();
                                });
                            } else {
                                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan pada input',
                                    'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error sistem!', 'Cek koneksi internet atau server.', 'error');
                            console.error('Error Fetch:', error);
                        });

                }
            });
        });
    </script>
@endpush
