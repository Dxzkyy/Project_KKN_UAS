import 'package:flutter/material.dart';
import '../../core/constants.dart';
import '../../services/api_service.dart';

class NotificationScreen extends StatefulWidget {
  final String? initialKodeOrder;
  const NotificationScreen({super.key, this.initialKodeOrder});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  final _emailCtrl = TextEditingController();
  List<dynamic> _orders = [];
  bool _loading = false;
  bool _searched = false;
  String _errorMsg = '';

  Future<void> _cariPesanan() async {
    final email = _emailCtrl.text.trim();
    if (email.isEmpty) {
      setState(() => _errorMsg = 'Email tidak boleh kosong');
      return;
    }
    if (!RegExp(r'^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$').hasMatch(email)) {
      setState(() => _errorMsg = 'Format email tidak valid');
      return;
    }

    setState(() {
      _loading = true;
      _errorMsg = '';
      _searched = false;
    });

    try {
      final result = await ApiService.getRiwayatByEmail(email);
      setState(() {
        _orders = result;
        _loading = false;
        _searched = true;
      });
    } catch (e) {
      setState(() {
        _loading = false;
        _searched = true;
        _orders = [];
        _errorMsg = 'Gagal memuat data. Periksa koneksi internet.';
      });
    }
  }

  Future<void> _konfirmasiBatal(String kodeOrder) async {
    final konfirmasi = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Batalkan Pesanan?'),
        content: Text('Yakin ingin membatalkan pesanan $kodeOrder?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Tidak'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            child: const Text(
              'Ya, Batalkan',
              style: TextStyle(
                color: AppColors.error,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );

    if (konfirmasi != true) return;

    final result = await ApiService.cancelOrder(kodeOrder);

    if (!mounted) return;

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(result['message']),
        backgroundColor: result['success'] == true
            ? AppColors.success
            : AppColors.error,
      ),
    );

    if (result['success'] == true) {
      // Refresh daftar pesanan
      _cariPesanan();
    }
  }

  String _formatHarga(double h) => h
      .toStringAsFixed(0)
      .replaceAllMapped(
        RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
        (m) => '${m[1]}.',
      );

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(title: const Text('Lacak Pesanan')),
      body: Column(
        children: [
          _buildSearchSection(),
          Expanded(child: _buildBody()),
        ],
      ),
    );
  }

  Widget _buildSearchSection() {
    return Container(
      padding: const EdgeInsets.all(16),
      color: AppColors.white,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Masukkan email yang digunakan saat memesan',
            style: AppTextStyles.bodyGrey.copyWith(fontSize: 13),
          ),
          const SizedBox(height: 10),
          Row(
            children: [
              Expanded(
                child: TextField(
                  controller: _emailCtrl,
                  keyboardType: TextInputType.emailAddress,
                  onSubmitted: (_) => _cariPesanan(),
                  decoration: InputDecoration(
                    hintText: 'contoh@email.com',
                    hintStyle: AppTextStyles.bodyGrey,
                    prefixIcon: const Icon(
                      Icons.email_outlined,
                      color: AppColors.primary,
                      size: 20,
                    ),
                    filled: true,
                    fillColor: AppColors.background,
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: BorderSide.none,
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                      borderSide: const BorderSide(
                        color: AppColors.primary,
                        width: 1.5,
                      ),
                    ),
                    errorText: _errorMsg.isNotEmpty ? _errorMsg : null,
                    contentPadding: const EdgeInsets.symmetric(
                      vertical: 14,
                      horizontal: 12,
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              GestureDetector(
                onTap: _loading ? null : _cariPesanan,
                child: Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: AppColors.primary,
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: _loading
                      ? const SizedBox(
                          width: 22,
                          height: 22,
                          child: CircularProgressIndicator(
                            color: Colors.white,
                            strokeWidth: 2,
                          ),
                        )
                      : const Icon(Icons.search, color: Colors.white, size: 22),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildBody() {
    if (!_searched) return _buildIllustration();
    if (_orders.isEmpty) return _buildEmpty();
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _orders.length,
      itemBuilder: (_, i) => _buildOrderCard(_orders[i]),
    );
  }

  Widget _buildIllustration() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(28),
            decoration: const BoxDecoration(
              color: AppColors.primaryLight,
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.manage_search,
              size: 64,
              color: AppColors.primary,
            ),
          ),
          const SizedBox(height: 20),
          Text('Lacak Pesanan Kamu', style: AppTextStyles.heading2),
          const SizedBox(height: 8),
          Text(
            'Masukkan email yang kamu gunakan\nsaat memesan untuk melihat status pesanan',
            style: AppTextStyles.bodyGrey,
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(
            Icons.receipt_long_outlined,
            size: 60,
            color: AppColors.textLight,
          ),
          const SizedBox(height: 12),
          Text('Tidak ada pesanan ditemukan', style: AppTextStyles.heading3),
          const SizedBox(height: 6),
          Text(
            'Pastikan email yang dimasukkan benar',
            style: AppTextStyles.bodyGrey,
          ),
        ],
      ),
    );
  }

  Widget _buildOrderCard(Map<String, dynamic> order) {
    final status = order['status'] as String;
    final config = _statusConfig(status);
    final items = order['items'] as List<dynamic>;

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: config['borderColor'] as Color, width: 1.5),
        boxShadow: [
          BoxShadow(
            color: AppColors.cardShadow,
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: (config['color'] as Color).withOpacity(0.1),
              borderRadius: const BorderRadius.vertical(
                top: Radius.circular(13),
              ),
            ),
            child: Row(
              children: [
                Icon(
                  config['icon'] as IconData,
                  color: config['color'] as Color,
                  size: 18,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    config['label'] as String,
                    style: TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                      color: config['color'] as Color,
                    ),
                  ),
                ),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 4,
                  ),
                  decoration: BoxDecoration(
                    color: config['color'] as Color,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    config['badge'] as String,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      order['kode_order'],
                      style: AppTextStyles.heading3.copyWith(fontSize: 13),
                    ),
                    Text(order['waktu'], style: AppTextStyles.caption),
                  ],
                ),
                const SizedBox(height: 4),
                Row(
                  children: [
                    const Icon(
                      Icons.person_outline,
                      size: 13,
                      color: AppColors.textGrey,
                    ),
                    const SizedBox(width: 4),
                    Text(
                      order['nama_pembeli'] ?? '-',
                      style: AppTextStyles.caption,
                    ),
                    if (order['nomor_meja'] != null) ...[
                      const SizedBox(width: 10),
                      const Icon(
                        Icons.table_restaurant_outlined,
                        size: 13,
                        color: AppColors.textGrey,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        'Meja ${order['nomor_meja']}',
                        style: AppTextStyles.caption,
                      ),
                    ],
                  ],
                ),
                const Divider(height: 20),
                ...items.map(
                  (item) => Padding(
                    padding: const EdgeInsets.only(bottom: 6),
                    child: Row(
                      children: [
                        Text(
                          '${item['jumlah']}x',
                          style: AppTextStyles.body.copyWith(
                            fontWeight: FontWeight.bold,
                            color: AppColors.primary,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            item['nama_produk'],
                            style: AppTextStyles.body,
                          ),
                        ),
                        Text(
                          'Rp. ${_formatHarga((item['subtotal'] as num).toDouble())}',
                          style: AppTextStyles.caption,
                        ),
                      ],
                    ),
                  ),
                ),
                const Divider(height: 16),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text('Total', style: AppTextStyles.heading3),
                    Text(
                      'Rp. ${_formatHarga((order['total'] as num).toDouble())}',
                      style: AppTextStyles.priceOrange,
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 8,
                  ),
                  decoration: BoxDecoration(
                    color: (config['color'] as Color).withOpacity(0.08),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        Icons.info_outline,
                        size: 14,
                        color: config['color'] as Color,
                      ),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          config['pesan'] as String,
                          style: TextStyle(
                            fontSize: 12,
                            color: config['color'] as Color,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                if (order['status'] == 'pending') ...[
                  const SizedBox(height: 10),
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      onPressed: () => _konfirmasiBatal(order['kode_order']),
                      icon: const Icon(
                        Icons.cancel_outlined,
                        size: 18,
                        color: AppColors.error,
                      ),
                      label: const Text(
                        'Batalkan Pesanan',
                        style: TextStyle(
                          color: AppColors.error,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      style: OutlinedButton.styleFrom(
                        side: const BorderSide(color: AppColors.error),
                        padding: const EdgeInsets.symmetric(vertical: 10),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }

  Map<String, dynamic> _statusConfig(String status) {
    switch (status) {
      case 'proses':
        return {
          'icon': Icons.soup_kitchen_outlined,
          'label': 'Sedang Dimasak',
          'badge': 'Dimasak',
          'pesan': 'Pesanan sedang diproses oleh dapur, mohon ditunggu.',
          'color': const Color(0xFF9C27B0),
          'borderColor': const Color(0xFFE1BEE7),
        };
      case 'selesai':
        return {
          'icon': Icons.check_circle_outline,
          'label': 'Pesanan Selesai',
          'badge': 'Selesai',
          'pesan': 'Pesanan sudah selesai! Silakan ambil di kasir.',
          'color': AppColors.success,
          'borderColor': const Color(0xFFC8E6C9),
        };
      case 'dibatalkan':
        return {
          'icon': Icons.cancel_outlined,
          'label': 'Pesanan Dibatalkan',
          'badge': 'Dibatalkan',
          'pesan': 'Pesanan ini telah dibatalkan.',
          'color': AppColors.error,
          'borderColor': const Color(0xFFFFCDD2),
        };
      default:
        return {
          'icon': Icons.hourglass_top_outlined,
          'label': 'Menunggu Konfirmasi',
          'badge': 'Pending',
          'pesan': 'Pesanan diterima, sedang menunggu konfirmasi dapur.',
          'color': AppColors.primary,
          'borderColor': const Color(0xFFFFE0B2),
        };
    }
  }
}
