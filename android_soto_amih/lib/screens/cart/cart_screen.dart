import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../../core/constants.dart';
import '../../providers/cart_provider.dart';
import '../../../../models/cart_item_model.dart';
import '../../../checkout/checkout_screen.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  String _formatHarga(double harga) {
    return harga
        .toStringAsFixed(0)
        .replaceAllMapped(
          RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
          (m) => '${m[1]}.',
        );
  }

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();

    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        title: const Text('Keranjang'),
        actions: [
          if (cart.items.isNotEmpty)
            TextButton(
              onPressed: () {
                showDialog(
                  context: context,
                  builder: (_) => AlertDialog(
                    title: const Text('Kosongkan Keranjang?'),
                    content: const Text(
                      'Semua item akan dihapus dari keranjang.',
                    ),
                    actions: [
                      TextButton(
                        onPressed: () => Navigator.pop(context),
                        child: const Text('Batal'),
                      ),
                      TextButton(
                        onPressed: () {
                          context.read<CartProvider>().clearCart();
                          Navigator.pop(context);
                        },
                        child: const Text(
                          'Hapus',
                          style: TextStyle(color: AppColors.error),
                        ),
                      ),
                    ],
                  ),
                );
              },
              child: const Text(
                'Hapus Semua',
                style: TextStyle(color: AppColors.error, fontSize: 13),
              ),
            ),
        ],
      ),
      body: cart.items.isEmpty
          ? _buildEmpty(context)
          : Column(
              children: [
                Expanded(
                  child: SingleChildScrollView(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        _buildSectionLabel('Daftar Pesanan'),
                        ...cart.items.map(
                          (item) => _buildCartItem(context, item),
                        ),
                        _buildCatatanField(context, cart),
                        const SizedBox(height: 8),
                      ],
                    ),
                  ),
                ),
                _buildBottomSummary(context, cart),
              ],
            ),
    );
  }

  Widget _buildEmpty(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: AppColors.primaryLight,
              shape: BoxShape.circle,
            ),
            child: const Icon(
              Icons.shopping_cart_outlined,
              size: 60,
              color: AppColors.primary,
            ),
          ),
          const SizedBox(height: 20),
          Text('Keranjang Kosong', style: AppTextStyles.heading2),
          const SizedBox(height: 8),
          Text(
            'Yuk tambahkan menu yang kamu suka!',
            style: AppTextStyles.bodyGrey,
          ),
        ],
      ),
    );
  }

  Widget _buildSectionLabel(String label) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
      child: Text(label, style: AppTextStyles.heading3),
    );
  }

  Widget _buildCartItem(BuildContext context, CartItem item) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: AppColors.cardShadow,
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: item.menu.fotoUrl != null
                ? Image.network(
                    item.menu.fotoUrl!,
                    width: 65,
                    height: 65,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => _placeholder(),
                  )
                : _placeholder(),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.menu.namaProduk,
                  style: AppTextStyles.heading3.copyWith(fontSize: 14),
                ),
                const SizedBox(height: 4),
                Text(
                  'Rp. ${_formatHarga(item.menu.harga)}',
                  style: AppTextStyles.bodyGrey,
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    _qtyBtn(
                      context,
                      Icons.remove,
                      () => context.read<CartProvider>().kurang(item.menu),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      child: Text(
                        '${item.jumlah}',
                        style: AppTextStyles.heading3,
                      ),
                    ),
                    _qtyBtn(
                      context,
                      Icons.add,
                      () => context.read<CartProvider>().tambah(item.menu),
                    ),
                  ],
                ),
              ],
            ),
          ),
          Column(
            children: [
              IconButton(
                onPressed: () => context.read<CartProvider>().hapus(item.menu),
                icon: const Icon(
                  Icons.delete_outline,
                  color: AppColors.textGrey,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildCatatanField(BuildContext context, CartProvider cart) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: AppColors.cardShadow,
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: TextField(
        onChanged: (val) => context.read<CartProvider>().setCatatan(val),
        decoration: const InputDecoration(
          hintText: 'Tambahkan Catatan...',
          hintStyle: TextStyle(color: AppColors.textLight),
          border: InputBorder.none,
          icon: Icon(Icons.edit_note, color: AppColors.primary),
          labelText: 'Catatan',
          labelStyle: TextStyle(
            color: AppColors.primary,
            fontWeight: FontWeight.w600,
          ),
        ),
        maxLines: 2,
        style: AppTextStyles.body,
      ),
    );
  }

  Widget _buildBottomSummary(BuildContext context, CartProvider cart) {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 20),
      decoration: const BoxDecoration(
        color: AppColors.primary,
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Jumlah Item',
                style: const TextStyle(color: Colors.white70, fontSize: 13),
              ),
              Text(
                '${cart.totalItem} Item',
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Total Pesanan',
                style: const TextStyle(color: Colors.white70, fontSize: 13),
              ),
              Text(
                'Rp. ${_formatHarga(cart.totalHarga)}',
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const CheckoutScreen()),
                );
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.white,
                foregroundColor: AppColors.primary,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                elevation: 0,
              ),
              child: const Text(
                'Checkout',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _qtyBtn(BuildContext context, IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 28,
        height: 28,
        decoration: BoxDecoration(
          color: AppColors.primaryLight,
          borderRadius: BorderRadius.circular(6),
        ),
        child: Icon(icon, size: 14, color: AppColors.primary),
      ),
    );
  }

  Widget _placeholder() {
    return Container(
      width: 65,
      height: 65,
      decoration: BoxDecoration(
        color: AppColors.primaryLight,
        borderRadius: BorderRadius.circular(8),
      ),
      child: const Icon(Icons.restaurant, color: AppColors.primary),
    );
  }
}
