class MenuModel {
  final int id;
  final String kodeProduk;
  final String namaProduk;
  final String kategori;
  final double harga;
  final int stok;
  final String? fotoUrl;

  MenuModel({
    required this.id,
    required this.kodeProduk,
    required this.namaProduk,
    required this.kategori,
    required this.harga,
    required this.stok,
    this.fotoUrl,
  });

  factory MenuModel.fromJson(Map<String, dynamic> json) {
    return MenuModel(
      id: json['id'],
      kodeProduk: json['kode_produk'] ?? '',
      namaProduk: json['nama_produk'] ?? '',
      kategori: json['kategori'] ?? '',
      harga: (json['harga'] as num).toDouble(),
      stok: json['stok'] ?? 0,
      fotoUrl: json['foto_url'],
    );
  }
}
