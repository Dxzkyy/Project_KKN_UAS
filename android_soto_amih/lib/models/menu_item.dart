class MenuItem {
  final int id;
  final String kodeProduk;
  final String namaProduk;
  final String kategori;
  final double harga;
  final int stok;
  final String foto;
  final String? fotoUrl;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  MenuItem({
    required this.id,
    required this.kodeProduk,
    required this.namaProduk,
    required this.kategori,
    required this.harga,
    required this.stok,
    required this.foto,
    this.fotoUrl,
    this.createdAt,
    this.updatedAt,
  });

  factory MenuItem.fromJson(Map<String, dynamic> json) {
    double _toDouble(dynamic value) {
      if (value == null) return 0.0;
      if (value is double) return value;
      if (value is int) return value.toDouble();
      if (value is String) return double.tryParse(value.trim()) ?? 0.0;
      return 0.0;
    }

    int _toInt(dynamic value) {
      if (value == null) return 0;
      if (value is int) return value;
      if (value is double) return value.toInt();
      if (value is String) return int.tryParse(value.trim()) ?? 0;
      return 0;
    }

    return MenuItem(
      id: _toInt(json['id']),
      kodeProduk: json['kode_produk'] ?? '',
      namaProduk: json['nama_produk'] ?? '',
      kategori: json['kategori'] ?? '',
      harga: _toDouble(json['harga']),
      stok: _toInt(json['stok']),
      foto: json['foto'] ?? '',
      fotoUrl:
          json['foto_url'] ??
          (json['foto'] != null
              ? 'http://localhost/Project_KKN_UAS/Web_soto_amih/storage/app/public/menus/${json['foto']}'
              : null),
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'])
          : null,
      updatedAt: json['updated_at'] != null
          ? DateTime.tryParse(json['updated_at'])
          : null,
    );
  }
}
