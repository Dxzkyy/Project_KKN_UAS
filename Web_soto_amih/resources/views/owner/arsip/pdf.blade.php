<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Harian - {{ $tanggal->format('d M Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1f2937; background: #fff; font-size: 13px; }

        .page { max-width: 800px; margin: 0 auto; padding: 32px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 20px; border-bottom: 2px solid #C97B2E; margin-bottom: 24px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-circle { width: 48px; height: 48px; border-radius: 50%; background: #C97B2E; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 18px; }
        .brand-name { font-size: 20px; font-weight: 700; color: #C97B2E; }
        .brand-sub { font-size: 11px; color: #6b7280; }
        .header-info { text-align: right; }
        .header-info h2 { font-size: 16px; font-weight: 700; color: #1f2937; }
        .header-info p { font-size: 12px; color: #6b7280; margin-top: 2px; }

        /* Stats grid */
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px; }
        .stat-card { background: #fafafa; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
        .stat-card.accent { border-left: 3px solid #C97B2E; }
        .stat-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px; }
        .stat-value { font-size: 16px; font-weight: 700; color: #1f2937; }
        .stat-value.green { color: #16a34a; }
        .stat-value.red { color: #dc2626; }

        /* Section */
        .section { margin-bottom: 24px; }
        .section-title { font-size: 13px; font-weight: 700; color: #1f2937; margin-bottom: 12px; padding-bottom: 6px; border-bottom: 1px solid #e5e7eb; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th { background: #f9fafb; padding: 8px 10px; text-align: left; font-weight: 600; color: #6b7280; font-size: 11px; }
        td { padding: 8px 10px; border-bottom: 1px solid #f3f4f6; color: #374151; }
        tr:last-child td { border-bottom: none; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .text-orange { color: #C97B2E; }
        .tfoot-row td { background: #fff7ed; font-weight: 700; border-top: 2px solid #fed7aa; }

        /* Top menu */
        .top-menu { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; }
        .menu-item { text-align: center; background: #fafafa; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 6px; }
        .menu-rank { font-size: 11px; font-weight: 700; color: #C97B2E; margin-bottom: 4px; }
        .menu-name { font-size: 11px; font-weight: 600; color: #1f2937; line-height: 1.3; }
        .menu-count { font-size: 11px; color: #C97B2E; font-weight: 700; margin-top: 3px; }

        /* Footer */
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: flex-end; }
        .footer-info { font-size: 11px; color: #9ca3af; }
        .signature { text-align: center; }
        .signature-line { width: 140px; border-top: 1px solid #374151; padding-top: 4px; font-size: 11px; color: #374151; font-weight: 600; }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none; }
            .page { padding: 20px; }
        }
    </style>
</head>
<body>

{{-- Tombol print (tidak ikut cetak) --}}
<div class="no-print" style="background:#f3f4f6; padding:12px 32px; display:flex; gap:10px; justify-content:flex-end;">
    <button onclick="window.print()" style="background:#C97B2E; color:white; border:none; padding:8px 20px; border-radius:8px; font-weight:600; cursor:pointer; font-size:13px;">
        🖨️ Cetak / Download PDF
    </button>
    <button onclick="window.close()" style="background:white; border:1px solid #d1d5db; padding:8px 20px; border-radius:8px; font-weight:600; cursor:pointer; font-size:13px;">
        Tutup
    </button>
</div>

<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="brand">
            <div class="brand-circle">S</div>
            <div>
                <div class="brand-name">Soto Amih</div>
                <div class="brand-sub">Laporan Harian Penjualan</div>
            </div>
        </div>
        <div class="header-info">
            <h2>{{ $tanggal->translatedFormat('l, d F Y') }}</h2>
            <p>Kasir: {{ $laporan->kasir?->name ?? '—' }}</p>
            <p>Dicetak: {{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</p>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="stats">
        <div class="stat-card accent">
            <div class="stat-label">Pendapatan Kotor</div>
            <div class="stat-value">Rp {{ number_format($laporan->pendapatan_kotor, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value">{{ $laporan->total_pesanan }} Order</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Modal</div>
            <div class="stat-value">{{ $laporan->modal_harian !== null ? 'Rp ' . number_format($laporan->modal_harian, 0, ',', '.') : '—' }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Laba Bersih</div>
            @if($laporan->laba_bersih !== null)
                <div class="stat-value {{ $laporan->laba_bersih >= 0 ? 'green' : 'red' }}">
                    Rp {{ number_format($laporan->laba_bersih, 0, ',', '.') }}
                </div>
            @else
                <div class="stat-value" style="color:#9ca3af;">—</div>
            @endif
        </div>
    </div>

    {{-- Top Menu --}}
    @if($topMenu->count() > 0)
    <div class="section">
        <div class="section-title">Menu Terlaris</div>
        <div class="top-menu">
            @foreach($topMenu as $i => $item)
            <div class="menu-item">
                <div class="menu-rank">#{{ $i + 1 }}</div>
                <div class="menu-name">{{ $item->menu?->nama_produk ?? 'Menu Dihapus' }}</div>
                <div class="menu-count">{{ $item->total_terjual }}x</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Rincian Transaksi --}}
    <div class="section">
        <div class="section-title">Rincian Transaksi</div>
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Kode Order</th>
                    <th>Items</th>
                    <th>Metode Bayar</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}</td>
                    <td class="font-bold">{{ $order->kode_order }}</td>
                    <td>
                        @foreach($order->orderItems as $item)
                            {{ $item->jumlah }}x {{ $item->menu?->nama_produk ?? '—' }}@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td style="text-transform:capitalize">{{ $order->metode_bayar }}</td>
                    <td class="text-right font-bold text-orange">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; color:#9ca3af; padding:16px;">Tidak ada transaksi</td></tr>
                @endforelse
            </tbody>
            @if($orders->count() > 0)
            <tfoot>
                <tr class="tfoot-row">
                    <td colspan="4" class="font-bold">TOTAL PENDAPATAN KOTOR</td>
                    <td class="text-right font-bold text-orange">Rp {{ number_format($laporan->pendapatan_kotor, 0, ',', '.') }}</td>
                </tr>
                @if($laporan->modal_harian !== null)
                <tr class="tfoot-row">
                    <td colspan="4" class="font-bold">Modal</td>
                    <td class="text-right font-bold">Rp {{ number_format($laporan->modal_harian, 0, ',', '.') }}</td>
                </tr>
                <tr class="tfoot-row">
                    <td colspan="4" class="font-bold">LABA BERSIH</td>
                    <td class="text-right font-bold {{ ($laporan->laba_bersih ?? 0) >= 0 ? 'green' : 'red' }}">
                        Rp {{ number_format($laporan->laba_bersih, 0, ',', '.') }}
                    </td>
                </tr>
                @endif
            </tfoot>
            @endif
        </table>
    </div>

    {{-- Catatan --}}
    @if($laporan->catatan)
    <div class="section">
        <div class="section-title">Catatan Kasir</div>
        <p style="color:#374151; line-height:1.6; background:#fff7ed; padding:10px 14px; border-radius:8px; border-left:3px solid #C97B2E;">
            {{ $laporan->catatan }}
        </p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-info">
            <p>Soto Amih · Sistem Kasir Digital</p>
            <p>Dokumen ini digenerate otomatis oleh sistem</p>
        </div>
        <div class="signature">
            <div style="margin-bottom: 40px; font-size:11px; color:#9ca3af;">Tanda Tangan Kasir</div>
            <div class="signature-line">{{ $laporan->kasir?->name ?? '—' }}</div>
        </div>
    </div>

</div>
</body>
</html>