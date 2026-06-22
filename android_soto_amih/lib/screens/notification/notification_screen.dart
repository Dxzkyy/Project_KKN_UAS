import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/constants.dart';
import '../../../services/api_service.dart';

class NotificationScreen extends StatefulWidget {
  final String? initialKodeOrder;

  const NotificationScreen({super.key, this.initialKodeOrder});

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  List<Map<String, dynamic>> _riwayat = [];
  Map<String, Map<String, dynamic>> _statusCache = {};
  Timer? _pollingTimer;
  bool _loading = true;

  @override
  void initState() {
    super.initState();
    _loadRiwayat();
    // Polling status setiap 10 detik
    _pollingTimer = Timer.periodic(
      const Duration(seconds: 10),
      (_) => _refreshStatus(),
    );
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    super.dispose();
  }

  Future<void> _loadRiwayat() async {
    final prefs = await SharedPreferences.getInstance();
    final list = prefs.getStringList('riwayat_order') ?? [];
    final parsed = list
        .map((e) => jsonDecode(e) as Map<String, dynamic>)
        .toList();
    setState(() {
      _riwayat = parsed;
      _loading = false;
    });
    _refreshStatus();
  }

  Future<void> _refreshStatus() async {
    for (final item in _riwayat) {
      final kode = item['kode_order'] as String;
      final status = await ApiService.getOrderStatus(kode);
      if (status != null && mounted) {
        setState(() => _statusCache[kode] = status);
      }
    }
  }

  Future<void> _hapusRiwayat(String kodeOrder) async {
    final prefs = await SharedPreferences.getInstance();
    final list = prefs.getStringList('riwayat_order') ?? [];
    list.removeWhere((e) {
      final parsed = jsonDecode(e) as Map<String, dynamic>;
      return parsed['kode_order'] == kodeOrder;
    });
    await prefs.setStringList('riwayat_order', list);
    setState(() {
      _riwayat.removeWhere((e) => e['kode_order'] == kodeOrder);
      _statusCache.remove(kodeOrder);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Notifikasi'),
        leading: Navigator.canPop(context)
            ? IconButton(
                icon: const Icon(Icons.arrow_back),
                onPressed: () => Navigator.pop(context),
              )
            : null,
        actions: [
          if (_riwayat.isNotEmpty)
            IconButton(
              icon: const Icon(Icons.refresh, color: AppColors.primary),
              onPressed: _refreshStatus,
              tooltip: 'Refresh status',
            ),
        ],
      ),
      body: _loading
          ? const Center(
              child: CircularProgressIndicator(color: AppColors.primary),
            )
          : _riwayat.isEmpty
          ? _buildEmpty()
          : RefreshIndicator(
              color: AppColors.primary,
              onRefresh: _loadRiwayat,
              child: ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: _riwayat.length,
                itemBuilder: (_, i) => _buildNotifCard(_riwayat[i]),
              ),
            ),
    );
  }

  Widget _buildNotifCard(Map<String, dynamic> item) {
    final kode = item['kode_order'] as String;
    final statusData = _statusCache[kode];
    final status = statusData?['status'] ?? 'pending';
    final pesan = statusData?['pesan'] ?? 'Memuat status...';

    final config = _statusConfig(status);

    return Dismissible(
      key: Key(kode),
      direction: DismissDirection.endToStart,
      background: Container(
        alignment: Alignment.centerRight,
        padding: const EdgeInsets.only(right: 20),
        margin: const EdgeInsets.only(bottom: 12),
        decoration: BoxDecoration(
          color: AppColors.error,
          borderRadius: BorderRadius.circular(14),
        ),
        child: const Icon(Icons.delete_outline, color: Colors.white, size: 28),
      ),
      onDismissed: (_) => _hapusRiwayat(kode),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
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
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: (config['bgColor'] as Color).withOpacity(0.15),
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(13),
                ),
              ),
              child: Row(
                children: [
                  Icon(
                    config['icon'] as IconData,
                    color: config['color'] as Color,
                    size: 20,
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      config['title'] as String,
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
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

            // Body
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(pesan, style: AppTextStyles.body),
                  const SizedBox(height: 12),
                  const Divider(height: 1),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      Expanded(
                        child: _infoItem(
                          Icons.confirmation_number_outlined,
                          'Kode',
                          kode,
                          small: true,
                        ),
                      ),
                      if (item['nama'] != null)
                        Expanded(
                          child: _infoItem(
                            Icons.person_outline,
                            'Nama',
                            item['nama'],
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: _infoItem(
                          Icons.access_time,
                          'Waktu',
                          item['waktu'] ?? '-',
                        ),
                      ),
                      if (item['total'] != null)
                        Expanded(
                          child: _infoItem(
                            Icons.receipt_outlined,
                            'Total',
                            'Rp. ${(item['total'] as num).toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]}.')}',
                          ),
                        ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _infoItem(
    IconData icon,
    String label,
    String value, {
    bool small = false,
  }) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 14, color: AppColors.textGrey),
        const SizedBox(width: 4),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(label, style: AppTextStyles.caption),
              Text(
                value,
                style: AppTextStyles.body.copyWith(
                  fontSize: small ? 11 : 13,
                  fontWeight: FontWeight.w500,
                ),
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: const BoxDecoration(
              color: AppColors.primaryLight,
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.notifications_none,
              size: 60,
              color: AppColors.primary,
            ),
          ),
          const SizedBox(height: 20),
          Text('Belum Ada Notifikasi', style: AppTextStyles.heading2),
          const SizedBox(height: 8),
          Text(
            'Riwayat pesanan kamu akan muncul di sini',
            style: AppTextStyles.bodyGrey,
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
          'title': 'Pesanan Sedang Dimasak',
          'badge': 'Dimasak',
          'color': const Color(0xFF9C27B0),
          'bgColor': const Color(0xFF9C27B0),
          'borderColor': const Color(0xFFE1BEE7),
        };
      case 'selesai':
        return {
          'icon': Icons.check_circle_outline,
          'title': 'Pesanan Anda Selesai!',
          'badge': 'Selesai',
          'color': AppColors.success,
          'bgColor': AppColors.success,
          'borderColor': const Color(0xFFC8E6C9),
        };
      case 'dibatalkan':
        return {
          'icon': Icons.cancel_outlined,
          'title': 'Pesanan Dibatalkan',
          'badge': 'Dibatalkan',
          'color': AppColors.error,
          'bgColor': AppColors.error,
          'borderColor': const Color(0xFFFFCDD2),
        };
      default: // pending
        return {
          'icon': Icons.hourglass_top_outlined,
          'title': 'Pesanan Diterima',
          'badge': 'Menunggu',
          'color': AppColors.primary,
          'bgColor': AppColors.primary,
          'borderColor': const Color(0xFFFFE0B2),
        };
    }
  }
}
