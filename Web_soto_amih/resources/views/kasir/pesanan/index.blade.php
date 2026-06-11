@extends('layouts.app')

@section('title', 'Proses Pesanan')

@section('sidebar-menu')
@include('kasir.partials.sidebar')
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
                    <button class="px-4 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 text-sm font-medium rounded-lg transition">Makanan</button>
                    <button class="px-4 py-1.5 bg-gray-100 text-gray-600 hover:bg-gray-200 text-sm font-medium rounded-lg transition">Minuman</button>
                </div>
            </div>

            {{-- Grid Menu --}}
            <div class="grid grid-cols-3 gap-4 overflow-y-auto" style="max-height: calc(100vh - 280px);">
                @foreach ($menus as $item)
                    <div class="bg-white rounded-2xl p-3 shadow-sm hover:shadow-md transition cursor-pointer group">
                        <div class="relative h-32 mb-3 rounded-xl overflow-hidden">
                            <img src="{{ asset('storage/menus/' . $item->foto) }}" alt="{{ $item->nama_produk }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <span class="absolute top-2 right-2 bg-blue-500 text-white text-[10px] font-bold px-2 py-1 rounded-md">{{ $item->kategori }}</span>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
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
                    <span class="text-sm text-gray-500 w-24">Diskon</span>
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
                    <div class="grid grid-cols-3 gap-2" id="metodeBayarGroup">
                        <button onclick="pilihMetode(this, 'tunai')" class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] transition">Tunai</button>
                        <button onclick="pilihMetode(this, 'qris')" class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] transition">QRIS</button>
                        <button onclick="pilihMetode(this, 'bank')" class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] transition">Bank</button>
                    </div>
                </div>

                <div>
                    <p class="text-xs text-gray-500 mb-2">Pilihan:</p>
                    <div class="grid grid-cols-2 gap-2" id="tipePesananGroup">
                        <button onclick="pilihTipe(this, 'dine_in')" class="border border-[#C97B2E] bg-orange-50 rounded-lg py-2 text-xs font-bold text-[#C97B2E] flex justify-center items-center gap-1">Dine in</button>
                        <button onclick="pilihTipe(this, 'takeaway')" class="border border-gray-200 rounded-lg py-2 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] transition flex justify-center items-center gap-1">Takeaway</button>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-2">
                    <button id="btnBayar"
                        class="w-full bg-[#C97B2E] hover:bg-orange-600 text-white font-bold py-3 rounded-xl shadow-md transition flex justify-center items-center gap-2">
                        Bayar
                    </button>
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

    {{-- MODAL PEMBAYARAN --}}
    <div id="modalPembayaran" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">

            {{-- Header Modal --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-lg">Pembayaran</h3>
                <button onclick="tutupModalPembayaran()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 py-5 flex flex-col gap-5">

                {{-- Sub info --}}
                <p class="text-sm text-gray-500">Silakan pilih metode pembayaran dan masukkan jumlah yang dibayarkan</p>

                {{-- Total yang harus dibayar --}}
                <div class="bg-orange-50 rounded-xl px-4 py-3 flex justify-between items-center">
                    <span class="text-sm text-gray-600 font-medium">Total yang harus dibayar:</span>
                    <span class="font-bold text-[#C97B2E] text-lg" id="modalTotalLabel">Rp 0</span>
                </div>

                {{-- Metode Pembayaran --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">Metode pembayaran</p>
                    <div class="grid grid-cols-3 gap-2" id="modalMetodeGroup">
                        <button onclick="pilihModalMetode(this, 'tunai')"
                            class="flex items-center justify-center gap-1.5 border-2 border-[#C97B2E] bg-orange-50 rounded-xl py-2.5 text-xs font-bold text-[#C97B2E] transition">
                            💵 Tunai
                        </button>
                        <button onclick="pilihModalMetode(this, 'qris')"
                            class="flex items-center justify-center gap-1.5 border-2 border-gray-200 rounded-xl py-2.5 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] transition">
                            📱 QRIS
                        </button>
                        <button onclick="pilihModalMetode(this, 'bank')"
                            class="flex items-center justify-center gap-1.5 border-2 border-gray-200 rounded-xl py-2.5 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] transition">
                            🏦 Bank
                        </button>
                    </div>
                </div>

                {{-- Input Jumlah Bayar --}}
                <div id="sectionJumlahBayar">
                    <p class="text-sm font-semibold text-gray-700 mb-2">Jumlah bayar</p>
                    <input type="number" id="inputJumlahBayar" placeholder="Masukkan jumlah..."
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#C97B2E] transition font-semibold"
                        oninput="hitungKembalian()">

                    {{-- Shortcut Nominal --}}
                    <div class="grid grid-cols-4 gap-2 mt-2">
                        @foreach([50000, 100000, 150000, 200000] as $nominal)
                        <button onclick="isiNominal({{ $nominal }})"
                            class="border border-gray-200 rounded-lg py-1.5 text-xs font-semibold text-gray-600 hover:border-[#C97B2E] hover:text-[#C97B2E] hover:bg-orange-50 transition">
                            Rp {{ number_format($nominal/1000, 0) }}rb
                        </button>
                        @endforeach
                    </div>

                    {{-- Kembalian --}}
                    <div id="kembalianBox" class="hidden mt-3 bg-green-50 rounded-xl px-4 py-2.5 flex justify-between items-center">
                        <span class="text-sm text-gray-600">Kembalian:</span>
                        <span class="font-bold text-green-600 text-base" id="kembalianLabel">Rp 0</span>
                    </div>
                    <div id="kurangBayarBox" class="hidden mt-3 bg-red-50 rounded-xl px-4 py-2.5 flex justify-between items-center">
                        <span class="text-sm text-gray-600">Kurang bayar:</span>
                        <span class="font-bold text-red-500 text-base" id="kurangBayarLabel">Rp 0</span>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex gap-3 pt-1">
                    <button id="btnSimpanCetak"
                        class="flex-1 bg-[#C97B2E] hover:bg-orange-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Simpan & Cetak Resi
                    </button>
                    <button id="btnSimpanSaja"
                        class="flex-1 border-2 border-[#C97B2E] text-[#C97B2E] hover:bg-orange-50 font-bold py-3 rounded-xl transition text-sm">
                        Simpan tanpa cetak resi
                    </button>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // --- JAM REAL-TIME ---
        function updateClock() {
            const now = new Date();
            const wibTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }));
            const hours   = String(wibTime.getHours()).padStart(2, '0');
            const minutes = String(wibTime.getMinutes()).padStart(2, '0');
            const seconds = String(wibTime.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- STATE ---
        let cart          = [];
        let selectedMetode = 'tunai';
        let selectedTipe   = 'dine_in';
        let modalMetode    = 'tunai';

        const formatRupiah = (number) => new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
        }).format(number);

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2200,
            timerProgressBar: true,
            background: '#1f2937',
            color: '#f9fafb',
            customClass: { timerProgressBar: 'bg-orange-400' },
        });

        // --- PILIH METODE BAYAR (sidebar) ---
        window.pilihMetode = (el, metode) => {
            selectedMetode = metode;
            document.querySelectorAll('#metodeBayarGroup button').forEach(btn => {
                btn.classList.remove('border-[#C97B2E]', 'text-[#C97B2E]', 'bg-orange-50');
                btn.classList.add('border-gray-200', 'text-gray-600');
            });
            el.classList.add('border-[#C97B2E]', 'text-[#C97B2E]', 'bg-orange-50');
            el.classList.remove('border-gray-200', 'text-gray-600');
        };

        // --- PILIH TIPE PESANAN ---
        window.pilihTipe = (el, tipe) => {
            selectedTipe = tipe;
            document.querySelectorAll('#tipePesananGroup button').forEach(btn => {
                btn.classList.remove('border-[#C97B2E]', 'bg-orange-50', 'text-[#C97B2E]', 'font-bold');
                btn.classList.add('border-gray-200', 'text-gray-600');
            });
            el.classList.add('border-[#C97B2E]', 'bg-orange-50', 'text-[#C97B2E]', 'font-bold');
            el.classList.remove('border-gray-200', 'text-gray-600');
        };

        // --- KERANJANG ---
        window.tambahKeKeranjang = (id, nama, harga) => {
            const existing = cart.find(item => item.id === id);
            if (existing) { existing.qty += 1; }
            else { cart.push({ id, nama, harga, qty: 1 }); }
            renderCart();
            Toast.fire({ icon: 'success', title: `${nama} ditambahkan!` });
        };

        window.ubahQty = (index, delta) => {
            cart[index].qty += delta;
            if (cart[index].qty <= 0) cart.splice(index, 1);
            renderCart();
        };

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
                    </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
            }
            kalkulasiTotal();
        };

        const kalkulasiTotal = () => {
            let subtotal    = cart.reduce((sum, item) => sum + (item.harga * item.qty), 0);
            let diskonVal   = parseFloat(document.getElementById('inputDiskon').value) || 0;
            let tipeDiskon  = document.getElementById('tipeDiskon').value;
            let totalDiskon = tipeDiskon === 'persen' ? subtotal * (diskonVal / 100) : diskonVal;
            let totalAkhir  = Math.max(subtotal - totalDiskon, 0);

            document.getElementById('subtotalLabel').textContent = formatRupiah(subtotal);
            document.getElementById('totalLabel').textContent    = formatRupiah(totalAkhir);
            return totalAkhir;
        };

        document.getElementById('inputDiskon').addEventListener('input', kalkulasiTotal);
        document.getElementById('tipeDiskon').addEventListener('change', kalkulasiTotal);

        // --- MODAL PEMBAYARAN ---
        window.pilihModalMetode = (el, metode) => {
            modalMetode = metode;

            document.querySelectorAll('#modalMetodeGroup button').forEach(btn => {
                btn.classList.remove('border-[#C97B2E]', 'bg-orange-50', 'text-[#C97B2E]');
                btn.classList.add('border-gray-200', 'text-gray-600');
            });
            el.classList.add('border-[#C97B2E]', 'bg-orange-50', 'text-[#C97B2E]');
            el.classList.remove('border-gray-200', 'text-gray-600');

            // Sembunyikan input jumlah bayar untuk QRIS & Bank
            const showInput = metode === 'tunai';
            document.getElementById('sectionJumlahBayar').style.display = showInput ? 'block' : 'none';
        };

        window.isiNominal = (nominal) => {
            document.getElementById('inputJumlahBayar').value = nominal;
            hitungKembalian();
        };

        window.hitungKembalian = () => {
            const total    = kalkulasiTotal();
            const dibayar  = parseFloat(document.getElementById('inputJumlahBayar').value) || 0;
            const selisih  = dibayar - total;

            document.getElementById('kembalianBox').classList.add('hidden');
            document.getElementById('kurangBayarBox').classList.add('hidden');

            if (dibayar > 0) {
                if (selisih >= 0) {
                    document.getElementById('kembalianLabel').textContent = formatRupiah(selisih);
                    document.getElementById('kembalianBox').classList.remove('hidden');
                } else {
                    document.getElementById('kurangBayarLabel').textContent = formatRupiah(Math.abs(selisih));
                    document.getElementById('kurangBayarBox').classList.remove('hidden');
                }
            }
        };

        const bukaModalPembayaran = () => {
            const total = kalkulasiTotal();
            document.getElementById('modalTotalLabel').textContent = formatRupiah(total);
            document.getElementById('inputJumlahBayar').value = '';
            document.getElementById('kembalianBox').classList.add('hidden');
            document.getElementById('kurangBayarBox').classList.add('hidden');

            // Sync metode dari sidebar ke modal
            modalMetode = selectedMetode;
            document.querySelectorAll('#modalMetodeGroup button').forEach((btn, i) => {
                const metodes = ['tunai', 'qris', 'bank'];
                if (metodes[i] === selectedMetode) {
                    btn.classList.add('border-[#C97B2E]', 'bg-orange-50', 'text-[#C97B2E]');
                    btn.classList.remove('border-gray-200', 'text-gray-600');
                } else {
                    btn.classList.remove('border-[#C97B2E]', 'bg-orange-50', 'text-[#C97B2E]');
                    btn.classList.add('border-gray-200', 'text-gray-600');
                }
            });

            // Tampilkan/sembunyikan input jumlah bayar
            document.getElementById('sectionJumlahBayar').style.display = selectedMetode === 'tunai' ? 'block' : 'none';
            document.getElementById('modalPembayaran').classList.remove('hidden');
        };

        window.tutupModalPembayaran = () => {
            document.getElementById('modalPembayaran').classList.add('hidden');
        };

        // Tutup modal jika klik backdrop
        document.getElementById('modalPembayaran').addEventListener('click', function(e) {
            if (e.target === this) tutupModalPembayaran();
        });

        // --- PROSES SIMPAN ORDER ---
        const simpanOrder = (cetakResi) => {
            const total     = kalkulasiTotal();
            const diskonVal = parseFloat(document.getElementById('inputDiskon').value) || 0;
            const tipeDiskon = document.getElementById('tipeDiskon').value;

            // Validasi jumlah bayar untuk tunai
            if (modalMetode === 'tunai') {
                const dibayar = parseFloat(document.getElementById('inputJumlahBayar').value) || 0;
                if (dibayar < total) {
                    Swal.fire({
                    title: 'Kurang Bayar!',
                    text: 'Jumlah yang dibayarkan masih kurang dari total tagihan.',
                    icon: 'warning',
                    confirmButtonColor: '#C97B2E',
                    confirmButtonText: 'Cek Kembali',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6',
                    }
                });
                    return;
                }
            }

            const payload = {
                _token: '{{ csrf_token() }}',
                nama_pembeli: 'Pelanggan Umum',
                tipe: selectedTipe,
                metode_bayar: modalMetode,
                diskon: diskonVal,
                tipe_diskon: tipeDiskon,
                total: total,
                cart: cart
            };

            // Disable tombol saat proses
            document.getElementById('btnSimpanCetak').disabled = true;
            document.getElementById('btnSimpanSaja').disabled  = true;

            fetch('{{ route('kasir.pesanan.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    tutupModalPembayaran();
                    cart = [];
                    document.getElementById('inputDiskon').value = '';
                    renderCart();

                    if (cetakResi && data.order) {
                        // Buka struk di tab baru lalu tampilkan notif
                        window.open('{{ url('kasir/pesanan/struk') }}/' + data.order.id, '_blank');
                    }

                    Toast.fire({ icon: 'success', title: 'Pesanan berhasil disimpan!' });
                } else {
                    Swal.fire({
                    title: 'Gagal Menyimpan',
                    text: data.message || 'Terjadi kesalahan, coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#C97B2E',
                    confirmButtonText: 'Coba Lagi',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-xl px-6',
                    }
                });
                }
            })
            .catch(() => {
                Swal.fire({
                title: 'Koneksi Bermasalah',
                text: 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
                icon: 'error',
                confirmButtonColor: '#C97B2E',
                confirmButtonText: 'Oke',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6',
                }
            });
            })
            .finally(() => {
                document.getElementById('btnSimpanCetak').disabled = false;
                document.getElementById('btnSimpanSaja').disabled  = false;
            });
        };

        document.getElementById('btnSimpanCetak').addEventListener('click', () => simpanOrder(true));
        document.getElementById('btnSimpanSaja').addEventListener('click',  () => simpanOrder(false));

        // --- TOMBOL BAYAR ---
        document.getElementById('btnBayar').addEventListener('click', () => {
            if (cart.length === 0) {
                Swal.fire({
                title: 'Keranjang Kosong',
                text: 'Pilih menu terlebih dahulu sebelum melanjutkan pembayaran.',
                icon: 'info',
                confirmButtonColor: '#C97B2E',
                confirmButtonText: 'Oke, Pilih Menu',
                background: '#fff',
                customClass: {
                    title: 'text-gray-800',
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6',
                }
            });
                return;
            }
            bukaModalPembayaran();
        });

        // --- TOMBOL HAPUS KERANJANG ---
        document.getElementById('btnHapusKeranjang').addEventListener('click', () => {
            if (cart.length === 0) return;
            Swal.fire({
                title: 'Hapus Keranjang?',
                html: '<p class="text-gray-500 text-sm">Semua item yang sudah dipilih akan <strong>dihapus</strong> dan tidak bisa dikembalikan.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5',
                    cancelButton: 'rounded-xl px-5',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    document.getElementById('inputDiskon').value = '';
                    renderCart();
                    Toast.fire({ icon: 'success', title: 'Keranjang berhasil dikosongkan' });
                }
            });
        });
    </script>
@endpush