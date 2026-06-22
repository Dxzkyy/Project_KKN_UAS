import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../core/constants.dart';
import '../../../providers/cart_provider.dart';
import '../../../services/api_service.dart';
import '../success/success_screen.dart';

class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({super.key});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final _formKey = GlobalKey<FormState>();
  final _namaCtrl = TextEditingController();
  final _mejaCtrl = TextEditingController();
  final _emailCtrl = TextEditingController();

  String _tipe = 'dine_in';
  String _metodeBayar = 'tunai';
  bool _loading = false;

  final List<Map<String, dynamic>> _metodeBayarList = [
    {'value': 'tunai', 'label': 'Cash', 'icon': Icons.payments_outlined},
    {'value': 'qris', 'label': 'ShopeePay / QRIS', 'icon': Icons.qr_code},
    {
      'value': 'bank',
      'label': 'Transfer Bank',
      'icon': Icons.account_balance_outlined,
    },
  ];

  String _formatHarga(double h) => h
      .toStringAsFixed(0)
      .replaceAllMapped(
        RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
        (m) => '${m[1]}.',
      );

  Future<void> _konfirmasi() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _loading = true);

    final cart = context.read<CartProvider>();
    final result = await ApiService.createOrder(
      namaPembeli: _namaCtrl.text.trim(),
      nomorMeja: _mejaCtrl.text.trim(),
      email: _emailCtrl.text.trim(),
      tipe: _tipe,
      metodeBayar: _metodeBayar,
      catatan: cart.catatan,
      items: cart.toApiItems(),
    );

    setState(() => _loading = false);

    if (!mounted) return;

    if (result['success'] == true) {
      final data = result['data'];
      cart.clearCart();
      Navigator.pushAndRemoveUntil(
        context,
        MaterialPageRoute(builder: (_) => SuccessScreen(orderData: data)),
        (route) => route.isFirst,
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result['message'] ?? 'Gagal membuat pesanan'),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Detail Pesanan'),
        leading: IconButton(
          icon: Container(
            padding: const EdgeInsets.all(6),
            decoration: BoxDecoration(
              color: AppColors.primary,
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.arrow_back, color: Colors.white, size: 18),
          ),
          onPressed: () => Navigator.pop(context),
        ),
      ),
      body: Form(
        key: _formKey,
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _sectionCard(
                title: 'Informasi Pemesan',
                child: Column(
                  children: [
                    _inputField(
                      controller: _namaCtrl,
                      label: 'Nama Pembeli',
                      hint: 'Contoh: Putri Aulia',
                      icon: Icons.person_outline,
                      validator: (v) =>
                          (v == null || v.isEmpty) ? 'Nama wajib diisi' : null,
                    ),
                    const SizedBox(height: 12),
                    _inputField(
                      controller: _mejaCtrl,
                      label: 'Nomor Meja',
                      hint: 'Contoh: 5',
                      icon: Icons.table_restaurant_outlined,
                      keyboardType: TextInputType.number,
                      validator: _tipe == 'dine_in'
                          ? (v) => (v == null || v.isEmpty)
                                ? 'Nomor meja wajib diisi'
                                : null
                          : null,
                    ),
                    const SizedBox(height: 12),
                    _inputField(
                      controller: _emailCtrl,
                      label: 'Email (untuk resi)',
                      hint: 'contoh@email.com',
                      icon: Icons.email_outlined,
                      keyboardType: TextInputType.emailAddress,
                    ),
                    const SizedBox(height: 12),
                    // Tipe pesanan
                    Row(
                      children: [
                        const Icon(
                          Icons.restaurant_outlined,
                          color: AppColors.textGrey,
                          size: 20,
                        ),
                        const SizedBox(width: 10),
                        const Text('Tipe Pesanan', style: AppTextStyles.body),
                        const Spacer(),
                        _tipeToggle('Dine In', 'dine_in'),
                        const SizedBox(width: 8),
                        _tipeToggle('Take Away', 'takeaway'),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              _sectionCard(
                title: 'Daftar Pesanan',
                child: Column(
                  children: [
                    ...cart.items.map(
                      (item) => Padding(
                        padding: const EdgeInsets.only(bottom: 12),
                        child: Row(
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(8),
                              child: item.menu.fotoUrl != null
                                  ? Image.network(
                                      item.menu.fotoUrl!,
                                      width: 52,
                                      height: 52,
                                      fit: BoxFit.cover,
                                      errorBuilder: (_, __, ___) =>
                                          _photoPlaceholder(),
                                    )
                                  : _photoPlaceholder(),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    item.menu.namaProduk,
                                    style: AppTextStyles.heading3.copyWith(
                                      fontSize: 14,
                                    ),
                                  ),
                                  Text(
                                    '${item.jumlah}x',
                                    style: AppTextStyles.caption,
                                  ),
                                ],
                              ),
                            ),
                            Text(
                              'Rp. ${_formatHarga(item.subtotal)}',
                              style: AppTextStyles.price,
                            ),
                          ],
                        ),
                      ),
                    ),
                    const Divider(),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('TOTAL', style: AppTextStyles.heading3),
                        Text(
                          'Rp. ${_formatHarga(cart.totalHarga)}',
                          style: AppTextStyles.priceOrange.copyWith(
                            fontSize: 16,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              _sectionCard(
                title: 'Pilih Metode Pembayaran',
                child: Column(
                  children: _metodeBayarList
                      .map(
                        (m) => _metodeBayarTile(
                          value: m['value'],
                          label: m['label'],
                          icon: m['icon'],
                        ),
                      )
                      .toList(),
                ),
              ),
              const SizedBox(height: 24),

              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: _loading ? null : _konfirmasi,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primary,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                    elevation: 0,
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
                      : const Text(
                          'Konfirmasi Pesanan',
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

  Widget _sectionCard({required String title, required Widget child}) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(14),
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
          Text(title, style: AppTextStyles.heading3),
          const SizedBox(height: 14),
          child,
        ],
      ),
    );
  }

  Widget _inputField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    TextInputType keyboardType = TextInputType.text,
    String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: keyboardType,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        hintText: hint,
        prefixIcon: Icon(icon, color: AppColors.primary, size: 20),
        labelStyle: const TextStyle(color: AppColors.textGrey, fontSize: 13),
        filled: true,
        fillColor: AppColors.background,
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide.none,
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: AppColors.error),
        ),
        contentPadding: const EdgeInsets.symmetric(
          vertical: 14,
          horizontal: 12,
        ),
      ),
    );
  }

  Widget _tipeToggle(String label, String value) {
    final selected = _tipe == value;
    return GestureDetector(
      onTap: () => setState(() => _tipe = value),
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 7),
        decoration: BoxDecoration(
          color: selected ? AppColors.primary : AppColors.background,
          borderRadius: BorderRadius.circular(8),
          border: Border.all(
            color: selected ? AppColors.primary : AppColors.border,
          ),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w600,
            color: selected ? Colors.white : AppColors.textGrey,
          ),
        ),
      ),
    );
  }

  Widget _metodeBayarTile({
    required String value,
    required String label,
    required IconData icon,
  }) {
    final selected = _metodeBayar == value;
    return GestureDetector(
      onTap: () => setState(() => _metodeBayar = value),
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
        decoration: BoxDecoration(
          color: selected ? AppColors.primaryLight : AppColors.background,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(
            color: selected ? AppColors.primary : AppColors.border,
          ),
        ),
        child: Row(
          children: [
            Icon(
              icon,
              color: selected ? AppColors.primary : AppColors.textGrey,
              size: 24,
            ),
            const SizedBox(width: 14),
            Text(
              label,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w500,
                color: selected ? AppColors.primary : AppColors.textDark,
              ),
            ),
            const Spacer(),
            Container(
              width: 20,
              height: 20,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                border: Border.all(
                  color: selected ? AppColors.primary : AppColors.textGrey,
                  width: 2,
                ),
              ),
              child: selected
                  ? Center(
                      child: Container(
                        width: 10,
                        height: 10,
                        decoration: const BoxDecoration(
                          shape: BoxShape.circle,
                          color: AppColors.primary,
                        ),
                      ),
                    )
                  : null,
            ),
          ],
        ),
      ),
    );
  }

  Widget _photoPlaceholder() {
    return Container(
      width: 52,
      height: 52,
      decoration: BoxDecoration(
        color: AppColors.primaryLight,
        borderRadius: BorderRadius.circular(8),
      ),
      child: const Icon(Icons.restaurant, color: AppColors.primary, size: 22),
    );
  }
}
