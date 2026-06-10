<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $order->kode_order }}</title>
    <style>
        /* Desain khusus kertas printer thermal kasir */
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 80mm; /* Standar printer kasir 80mm */
            margin: 0 auto;
            padding: 10px;
            color: #000;
        }
        .text-center { text-align: center; }
        .border-dashed { border-bottom: 1px dashed #000; margin: 8px 0; }
        .flex-between { display: flex; justify-content: space-between; }
        .mb-1 { margin-bottom: 4px; }
        .bold { font-weight: bold; }
        
        /* Hilangkan margin di browser saat di-print */
        @media print {
            @page { margin: 0; }
            body { margin: 0.5cm; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h2 style="margin: 0;">Warung Amih Soto Betawi</h2>
        <p style="margin: 4px 0;">Sistem Kasir Pintar</p>
    </div>
    
    <div class="border-dashed"></div>
    
    <div class="flex-between mb-1">
        <span>Tgl: {{ $order->created_at->format('d/m/Y H:i') }}</span>
        <span>Ksr: {{ $order->kasir_id }}</span>
    </div>
    <div class="flex-between mb-1">
        <span>No : {{ $order->kode_order }}</span>
        <span class="bold">{{ strtoupper(str_replace('_', ' ', $order->tipe)) }}</span>
    </div>

    <div class="border-dashed"></div>

    {{-- Looping daftar pesanan --}}
    @foreach($order->orderItems as $item)
    <div class="mb-1">
        <div>{{ $item->menu->nama_produk ?? 'Menu Dihapus' }}</div>
        <div class="flex-between">
            <span>{{ $item->jumlah }} x {{ number_format($item->harga, 0, ',', '.') }}</span>
            <span>{{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</span>
        </div>
    </div>
    @endforeach

    <div class="border-dashed"></div>

    {{-- Totalan dan Metode Bayar --}}
    @if($order->diskon > 0)
    <div class="flex-between mb-1">
        <span>Diskon</span>
        <span>-{{ number_format($order->diskon, 0, ',', '.') }}</span>
    </div>
    @endif
    <div class="flex-between mb-1 bold" style="font-size: 14px;">
        <span>TOTAL</span>
        <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
    </div>
    <div class="flex-between mb-1 mt-2">
        <span>Pembayaran</span>
        <span style="text-transform: uppercase;">{{ $order->metode_bayar }}</span>
    </div>

    <div class="border-dashed"></div>
    
    <div class="text-center" style="margin-top: 15px;">
        <p style="margin: 0;">Terima Kasih Atas Kunjungan Anda!</p>
        <p style="margin: 2px 0; font-size: 10px;">Harap simpan struk ini sebagai bukti pembayaran yang sah.</p>
    </div>

</body>
</html>