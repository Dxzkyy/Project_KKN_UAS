<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 560px; margin: auto; background: #fff; border-radius: 10px; overflow: hidden; }
        .header { background: #F5A623; padding: 24px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; font-size: 13px; opacity: 0.9; }
        .body { padding: 24px; }
        .info-box { background: #fef9f0; border: 1px solid #F5A623; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .info-row:last-child { margin-bottom: 0; }
        .label { color: #888; }
        .value { font-weight: bold; color: #333; }
        h3 { font-size: 15px; color: #333; margin: 20px 0 12px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th { text-align: left; padding: 8px; background: #f9f9f9; color: #666; font-weight: normal; border-bottom: 1px solid #eee; }
        td { padding: 10px 8px; border-bottom: 1px solid #f0f0f0; color: #333; }
        .total-row td { font-weight: bold; font-size: 15px; border-bottom: none; padding-top: 14px; }
        .footer { background: #f9f9f9; padding: 16px 24px; text-align: center; font-size: 12px; color: #aaa; }
        .badge { display: inline-block; background: #fff3e0; color: #F5A623; border-radius: 20px; padding: 4px 12px; font-size: 13px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🍜 Soto Amih</h1>
        <p>Tasikmalaya - Konfirmasi Pesanan</p>
    </div>
    <div class="body">
        <p style="font-size:15px;color:#333;">Halo, <strong>{{ $order->nama_pembeli }}</strong>! 👋</p>
        <p style="color:#666;font-size:14px;">Pesanan kamu sudah kami terima dan sedang diproses. Berikut rinciannya:</p>

        <div class="info-box">
            <div class="info-row">
                <span class="label">Kode Pesanan</span>
                <span class="value">{{ $order->kode_order }}</span>
            </div>
            <div class="info-row">
                <span class="label">Nomor Meja</span>
                <span class="value">Meja {{ $order->nomor_meja ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tipe</span>
                <span class="value">{{ $order->tipe === 'dine_in' ? 'Makan di Tempat' : 'Take Away' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Waktu</span>
                <span class="value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Status</span>
                <span><span class="badge">Menunggu</span></span>
            </div>
        </div>

        <h3>Daftar Pesanan</h3>
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr>
                    <td>{{ $item['menu']->nama_produk }}</td>
                    <td style="text-align:center">{{ $item['jumlah'] }}x</td>
                    <td style="text-align:right">Rp {{ number_format($item['harga'] * $item['jumlah'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2">TOTAL</td>
                    <td style="text-align:right;color:#F5A623;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @if ($order->catatan)
        <div style="margin-top:16px;padding:12px;background:#f9f9f9;border-radius:8px;font-size:14px;color:#666;">
            📝 <strong>Catatan:</strong> {{ $order->catatan }}
        </div>
        @endif

        <p style="margin-top:24px;font-size:13px;color:#888;text-align:center;">
            Terima kasih sudah memesan di Soto Amih! Kami akan segera menyiapkan pesanan kamu. 🙏
        </p>
    </div>
    <div class="footer">
        Soto Amih · Tasikmalaya · Email ini dikirim otomatis, jangan dibalas.
    </div>
</div>
</body>
</html>