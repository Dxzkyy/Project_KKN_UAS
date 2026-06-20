class OrderItemDetail {
  final String namaProduk;
  final String? fotoUrl;
  final int jumlah;
  final double harga;
  final double subtotal;

  OrderItemDetail({
    required this.namaProduk,
    this.fotoUrl,
    required this.jumlah,
    required this.harga,
    required this.subtotal,
  });

  factory OrderItemDetail.fromJson(Map<String, dynamic> json) {
    return OrderItemDetail(
      namaProduk: json['nama_produk'] ?? '',
      fotoUrl: json['foto_url'],
      jumlah: json['jumlah'],
      harga: (json['harga'] as num).toDouble(),
      subtotal: (json['subtotal'] as num).toDouble(),
    );
  }
}

class OrderModel {
  final String kodeOrder;
  final String namaPembeli;
  final String? nomorMeja;
  final String tipe;
  final String metodeBayar;
  final double total;
  final String status;
  final String? catatan;
  final String waktu;
  final List<OrderItemDetail> items;

  OrderModel({
    required this.kodeOrder,
    required this.namaPembeli,
    this.nomorMeja,
    required this.tipe,
    required this.metodeBayar,
    required this.total,
    required this.status,
    this.catatan,
    required this.waktu,
    required this.items,
  });

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    return OrderModel(
      kodeOrder: json['kode_order'],
      namaPembeli: json['nama_pembeli'] ?? '',
      nomorMeja: json['nomor_meja'],
      tipe: json['tipe'],
      metodeBayar: json['metode_bayar'],
      total: (json['total'] as num).toDouble(),
      status: json['status'],
      catatan: json['catatan'],
      waktu: json['waktu'] ?? '',
      items: (json['items'] as List<dynamic>? ?? [])
          .map((e) => OrderItemDetail.fromJson(e))
          .toList(),
    );
  }

  String get statusLabel {
    switch (status) {
      case 'pending':
        return 'Menunggu';
      case 'proses':
        return 'Diproses';
      case 'selesai':
        return 'Selesai';
      case 'dibatalkan':
        return 'Dibatalkan';
      default:
        return status;
    }
  }

  String get statusPesan {
    switch (status) {
      case 'pending':
        return 'Pesanan sedang menunggu konfirmasi dapur.';
      case 'proses':
        return 'Pesanan Anda sedang diproses oleh dapur.';
      case 'selesai':
        return 'Pesanan Anda sudah selesai! Silakan ambil di kasir.';
      case 'dibatalkan':
        return 'Pesanan Anda telah dibatalkan.';
      default:
        return '-';
    }
  }
}
