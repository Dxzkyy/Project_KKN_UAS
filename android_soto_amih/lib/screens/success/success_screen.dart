import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../../../core/constants.dart';
import '../notification/notification_screen.dart';

class SuccessScreen extends StatefulWidget {
  final Map<String, dynamic> orderData;

  const SuccessScreen({super.key, required this.orderData});

  @override
  State<SuccessScreen> createState() => _SuccessScreenState();
}

class _SuccessScreenState extends State<SuccessScreen>
    with SingleTickerProviderStateMixin {
  late AnimationController _animCtrl;
  late Animation<double> _scaleAnim;
  late Animation<double> _fadeAnim;

  @override
  void initState() {
    super.initState();
    _simpanKodeOrder();

    _animCtrl = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _scaleAnim = CurvedAnimation(parent: _animCtrl, curve: Curves.elasticOut);
    _fadeAnim = CurvedAnimation(parent: _animCtrl, curve: Curves.easeIn);
    _animCtrl.forward();
  }

  Future<void> _simpanKodeOrder() async {
    // Simpan kode order ke local storage untuk Notifikasi
    final prefs = await SharedPreferences.getInstance();
    final existing = prefs.getStringList('riwayat_order') ?? [];
    final entry = jsonEncode({
      'kode_order': widget.orderData['kode_order'],
      'waktu': widget.orderData['waktu'],
      'total': widget.orderData['total'],
      'nama': widget.orderData['nama_pembeli'],
    });
    existing.insert(0, entry);
    // Simpan max 20 riwayat
    if (existing.length > 20) existing.removeLast();
    await prefs.setStringList('riwayat_order', existing);
  }

  @override
  void dispose() {
    _animCtrl.dispose();
    super.dispose();
  }

  String _formatHarga(double h) => h
      .toStringAsFixed(0)
      .replaceAllMapped(
        RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
        (m) => '${m[1]}.',
      );

  @override
  Widget build(BuildContext context) {
    final data = widget.orderData;
    final kodeOrder = data['kode_order'] ?? '-';

    return Scaffold(
      backgroundColor: AppColors.white,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 24),
          child: Column(
            children: [
              const Spacer(),

              // Animasi ikon centang
              ScaleTransition(
                scale: _scaleAnim,
                child: Container(
                  width: 130,
                  height: 130,
                  decoration: const BoxDecoration(
                    color: AppColors.primary,
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.check_rounded,
                    color: Colors.white,
                    size: 70,
                  ),
                ),
              ),

              const SizedBox(height: 32),

              FadeTransition(
                opacity: _fadeAnim,
                child: Column(
                  children: [
                    Text('Pesanan Diterima', style: AppTextStyles.heading1),
                    const SizedBox(height: 12),
                    Text(
                      'Pesanan mu sudah diterima dan akan segera\ndipersiapkan, silahkan menunggu dengan santuy ya..',
                      style: AppTextStyles.bodyGrey,
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 24),

                    // Info kode order
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 20,
                        vertical: 14,
                      ),
                      decoration: BoxDecoration(
                        color: AppColors.primaryLight,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: AppColors.primary.withOpacity(0.3),
                        ),
                      ),
                      child: Column(
                        children: [
                          _infoRow('Kode Pesanan', kodeOrder, bold: true),
                          const SizedBox(height: 6),
                          _infoRow('Nama', data['nama_pembeli'] ?? '-'),
                          const SizedBox(height: 6),
                          _infoRow(
                            'Nomor Meja',
                            'Meja ${data['nomor_meja'] ?? '-'}',
                          ),
                          const SizedBox(height: 6),
                          _infoRow(
                            'Total',
                            'Rp. ${_formatHarga((data['total'] as num).toDouble())}',
                          ),
                          const SizedBox(height: 6),
                          _infoRow('Waktu', data['waktu'] ?? '-'),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              const Spacer(),

              // Tombol Lihat Pesanan
              SizedBox(
                width: double.infinity,
                child: OutlinedButton(
                  onPressed: () {
                    Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(
                        builder: (_) =>
                            NotificationScreen(initialKodeOrder: kodeOrder),
                      ),
                      (route) => route.isFirst,
                    );
                  },
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    side: const BorderSide(color: AppColors.primary),
                  ),
                  child: const Text(
                    'Lihat Pesanan',
                    style: TextStyle(
                      color: AppColors.primary,
                      fontWeight: FontWeight.bold,
                      fontSize: 15,
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 12),

              // Tombol Pesan Lainnya
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () {
                    Navigator.popUntil(context, (route) => route.isFirst);
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 0,
                  ),
                  child: const Text(
                    'Pesan Lainnya',
                    style: AppTextStyles.button,
                  ),
                ),
              ),
              const SizedBox(height: 24),
            ],
          ),
        ),
      ),
    );
  }

  Widget _infoRow(String label, String value, {bool bold = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: AppTextStyles.bodyGrey.copyWith(fontSize: 13)),
        Text(
          value,
          style: AppTextStyles.body.copyWith(
            fontWeight: bold ? FontWeight.bold : FontWeight.w500,
            color: bold ? AppColors.primary : AppColors.textDark,
          ),
        ),
      ],
    );
  }
}
