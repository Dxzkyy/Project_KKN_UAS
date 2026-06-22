import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../../core/constants.dart';
import '../../../../models/menu_model.dart';
import '../../../../services/api_service.dart';
import '../../providers/cart_provider.dart';
import '../menu/menu_screen.dart';
import '../../../../widgets/menu_card.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  List<MenuModel> _rekomendasi = [];
  bool _loading = true;
  final _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadRekomendasi();
  }

  Future<void> _loadRekomendasi() async {
    try {
      final menus = await ApiService.getMenus();
      setState(() {
        _rekomendasi = menus.take(6).toList();
        _loading = false;
      });
    } catch (_) {
      setState(() => _loading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: RefreshIndicator(
          color: AppColors.primary,
          onRefresh: _loadRekomendasi,
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildHeader(),
                _buildSearchBar(),
                _buildBannerCarousel(),
                _buildOrderType(),
                _buildRekomendasi(),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
      child: Row(
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Selamat Datang 👋',
                style: AppTextStyles.caption.copyWith(
                  color: AppColors.textGrey,
                ),
              ),
              const SizedBox(height: 2),
              Text(AppConfig.appName, style: AppTextStyles.heading2),
            ],
          ),
          const Spacer(),
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: AppColors.primaryLight,
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Icon(
              Icons.restaurant,
              color: AppColors.primary,
              size: 22,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSearchBar() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 16, 20, 0),
      child: GestureDetector(
        onTap: () {
          // Navigate to menu tab (index 1)
          final nav = context.findAncestorStateOfType<State>();
        },
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          decoration: BoxDecoration(
            color: AppColors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: AppColors.cardShadow,
                blurRadius: 8,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: Row(
            children: [
              const Icon(Icons.search, color: AppColors.textGrey, size: 20),
              const SizedBox(width: 10),
              Text('Cari Lauk...', style: AppTextStyles.bodyGrey),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBannerCarousel() {
    final banners = [
      {
        'emoji': '🍜',
        'title': 'Soto Khas Betawi',
        'sub': 'Mulai dari Rp 20.000',
        'color': AppColors.primary,
      },
      {
        'emoji': '🥣',
        'title': 'Segar & Lezat',
        'sub': 'Resep turun-temurun',
        'color': const Color(0xFFFF7043),
      },
      {
        'emoji': '🍗',
        'title': 'Lauk Pilihan',
        'sub': 'Aneka pelengkap soto',
        'color': const Color(0xFF66BB6A),
      },
    ];

    return Container(
      margin: const EdgeInsets.only(top: 20),
      height: 160,
      child: PageView.builder(
        itemCount: banners.length,
        controller: PageController(viewportFraction: 0.9),
        itemBuilder: (_, i) {
          final b = banners[i];
          return Container(
            margin: const EdgeInsets.symmetric(horizontal: 6),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  (b['color'] as Color),
                  (b['color'] as Color).withOpacity(0.7),
                ],
              ),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Stack(
              children: [
                Positioned(
                  right: -10,
                  bottom: -10,
                  child: Text(
                    b['emoji'] as String,
                    style: const TextStyle(fontSize: 90),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.all(20),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        b['title'] as String,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 6),
                      Text(
                        b['sub'] as String,
                        style: const TextStyle(
                          color: Colors.white70,
                          fontSize: 13,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          'Pesan Sekarang',
                          style: TextStyle(
                            color: b['color'] as Color,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildOrderType() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 24, 20, 0),
      child: Row(
        children: [
          Expanded(
            child: _orderTypeCard('Take Away', Icons.takeout_dining, false),
          ),
          const SizedBox(width: 12),
          Expanded(child: _orderTypeCard('Dine In', Icons.restaurant, true)),
        ],
      ),
    );
  }

  Widget _orderTypeCard(String label, IconData icon, bool isActive) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 16),
      decoration: BoxDecoration(
        color: isActive ? AppColors.primary : AppColors.white,
        borderRadius: BorderRadius.circular(12),
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
          Icon(
            icon,
            color: isActive ? AppColors.white : AppColors.primary,
            size: 28,
          ),
          const SizedBox(height: 6),
          Text(
            label,
            style: TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w600,
              color: isActive ? AppColors.white : AppColors.textDark,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRekomendasi() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(20, 24, 20, 12),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text('Rekomendasi', style: AppTextStyles.heading3),
              TextButton(
                onPressed: () {},
                child: const Text(
                  'Lihat Semua',
                  style: TextStyle(color: AppColors.primary),
                ),
              ),
            ],
          ),
        ),
        if (_loading)
          const Center(
            child: CircularProgressIndicator(color: AppColors.primary),
          )
        else if (_rekomendasi.isEmpty)
          const Center(
            child: Padding(
              padding: EdgeInsets.all(20),
              child: Text(
                'Belum ada menu tersedia',
                style: AppTextStyles.bodyGrey,
              ),
            ),
          )
        else
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            padding: const EdgeInsets.symmetric(horizontal: 20),
            itemCount: _rekomendasi.length,
            itemBuilder: (_, i) => _buildRekomendasiCard(_rekomendasi[i]),
          ),
      ],
    );
  }

  Widget _buildRekomendasiCard(MenuModel menu) {
    final cart = context.watch<CartProvider>();
    final jumlah = cart.getJumlah(menu);

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
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
            borderRadius: BorderRadius.circular(10),
            child: menu.fotoUrl != null
                ? Image.network(
                    menu.fotoUrl!,
                    width: 80,
                    height: 80,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => _placeholderImage(),
                  )
                : _placeholderImage(),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(menu.namaProduk, style: AppTextStyles.heading3),
                const SizedBox(height: 4),
                Text(menu.kategori, style: AppTextStyles.caption),
                const SizedBox(height: 6),
                Text(
                  'Rp ${menu.harga.toStringAsFixed(0).replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]}.')}',
                  style: AppTextStyles.priceOrange,
                ),
              ],
            ),
          ),
          jumlah == 0
              ? GestureDetector(
                  onTap: () => context.read<CartProvider>().tambah(menu),
                  child: Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: AppColors.primary,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: const Icon(Icons.add, color: Colors.white, size: 20),
                  ),
                )
              : Row(
                  children: [
                    _qtyBtn(
                      Icons.remove,
                      () => context.read<CartProvider>().kurang(menu),
                    ),
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 8),
                      child: Text('$jumlah', style: AppTextStyles.heading3),
                    ),
                    _qtyBtn(
                      Icons.add,
                      () => context.read<CartProvider>().tambah(menu),
                    ),
                  ],
                ),
        ],
      ),
    );
  }

  Widget _qtyBtn(IconData icon, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        width: 28,
        height: 28,
        decoration: BoxDecoration(
          color: AppColors.primaryLight,
          borderRadius: BorderRadius.circular(6),
        ),
        child: Icon(icon, size: 16, color: AppColors.primary),
      ),
    );
  }

  Widget _placeholderImage() {
    return Container(
      width: 80,
      height: 80,
      color: AppColors.primaryLight,
      child: const Icon(Icons.restaurant, color: AppColors.primary, size: 32),
    );
  }
}
