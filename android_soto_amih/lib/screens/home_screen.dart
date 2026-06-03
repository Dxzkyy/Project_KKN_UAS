import 'package:android_soto_amih/screens/menu_screen.dart';
import 'package:android_soto_amih/screens/notification_screen.dart';
import 'package:android_soto_amih/widgets/bottombar.dart';
import 'package:flutter/material.dart';
import '../services/menu_services.dart';
import '../models/menu_item.dart';

class HomeScreen extends StatefulWidget {
  final String userName;
  const HomeScreen({super.key, required this.userName});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final Color mainOrange = const Color(0xFFF39C12);

  List<MenuItem> _recommendationMenus = [];
  bool _isLoading = true;
  String _errorMessage = '';

  // Mapping nama produk ke asset lokal (sesuaikan dengan nama file asli di folder assets/images)
  String _getLocalImageAsset(String productName) {
    switch (productName.toLowerCase()) {
      case 'soto ayam':
        return 'assets/images/sotoayam1.png';
      case 'soto special':
        return 'assets/images/sotospecial.png';
      case 'soto sapi':
        return 'assets/images/sotosapi1.png';
      case 'gongso':
        return 'assets/images/gongso1.png';
      case 'es teh':
        return 'assets/images/esteh.jpg';
      case 'air minum':
        return 'assets/images/airminum.jpg';
      default:
        // fallback: gunakan nama file dari database jika ada, atau placeholder
        return 'assets/images/placeholder.png';
    }
  }

  @override
  void initState() {
    super.initState();
    _loadMenus();
  }

  Future<void> _loadMenus() async {
    setState(() {
      _isLoading = true;
      _errorMessage = '';
    });

    try {
      final menus = await MenuService.fetchAllMenus();
      setState(() {
        _recommendationMenus = menus;
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _errorMessage = e.toString();
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const SizedBox(height: 20),

              // HEADER
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24.0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        const CircleAvatar(
                          radius: 20,
                          backgroundImage: NetworkImage(
                            'https://cdn-icons-png.flaticon.com/512/3135/3135715.png',
                          ),
                        ),
                        const SizedBox(width: 12),
                        Text(
                          widget.userName,
                          style: const TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.w600,
                            color: Colors.black87,
                          ),
                        ),
                      ],
                    ),
                    IconButton(
                      onPressed: () {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const NotificationScreen(),
                          ),
                        );
                      },
                      icon: const Icon(Icons.notifications_none_rounded),
                      color: Colors.black87,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // SEARCH BAR
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24.0),
                child: Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(15),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.grey.withOpacity(0.15),
                        spreadRadius: 2,
                        blurRadius: 10,
                        offset: const Offset(0, 3),
                      ),
                    ],
                  ),
                  child: TextField(
                    decoration: InputDecoration(
                      hintText: "Cari Lauk...",
                      hintStyle: TextStyle(color: Colors.grey.shade400),
                      prefixIcon: Icon(
                        Icons.search,
                        color: Colors.grey.shade400,
                      ),
                      border: InputBorder.none,
                      contentPadding: const EdgeInsets.symmetric(vertical: 15),
                    ),
                    onSubmitted: (query) => _searchMenus(query),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // CAROUSEL (statis dari asset)
              SizedBox(
                height: 140,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  children: [
                    _buildCarouselImage('assets/images/sotocampur2.png'),
                    _buildCarouselImage('assets/images/sotospecial.png'),
                    _buildCarouselImage('assets/images/gongso2.png'),
                  ],
                ),
              ),
              const SizedBox(height: 30),

              // TAKE AWAY & DINE IN
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 24.0),
                child: Row(
                  children: [
                    Expanded(
                      child: _buildOrderTypeButton(
                        title: "Take\nAway",
                        icon: Icons.shopping_bag_outlined,
                        isSelected: true,
                        onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) =>
                                const MenuScreen(serviceType: 'takeaway'),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: _buildOrderTypeButton(
                        title: "Dine\nIn",
                        icon: Icons.restaurant_outlined,
                        isSelected: false,
                        onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) =>
                                const MenuScreen(serviceType: 'dinein'),
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 30),

              // REKOMENDASI SECTION
              const Padding(
                padding: EdgeInsets.symmetric(horizontal: 24.0),
                child: Text(
                  "Rekomendasi",
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF4A3B32),
                  ),
                ),
              ),
              const SizedBox(height: 16),

              if (_isLoading)
                const Center(
                  child: Padding(
                    padding: EdgeInsets.all(32.0),
                    child: CircularProgressIndicator(),
                  ),
                )
              else if (_errorMessage.isNotEmpty)
                Center(
                  child: Padding(
                    padding: const EdgeInsets.all(32.0),
                    child: Column(
                      children: [
                        Icon(
                          Icons.error_outline,
                          color: Colors.red.shade300,
                          size: 48,
                        ),
                        const SizedBox(height: 12),
                        Text(
                          'Gagal memuat data: $_errorMessage',
                          textAlign: TextAlign.center,
                          style: const TextStyle(color: Colors.red),
                        ),
                        const SizedBox(height: 12),
                        ElevatedButton(
                          onPressed: _loadMenus,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: mainOrange,
                          ),
                          child: const Text('Coba Lagi'),
                        ),
                      ],
                    ),
                  ),
                )
              else if (_recommendationMenus.isEmpty)
                const Center(
                  child: Padding(
                    padding: EdgeInsets.all(32.0),
                    child: Text('Tidak ada menu yang tersedia'),
                  ),
                )
              else
                ListView.builder(
                  physics: const NeverScrollableScrollPhysics(),
                  shrinkWrap: true,
                  itemCount: _recommendationMenus.length,
                  itemBuilder: (context, index) {
                    final menu = _recommendationMenus[index];
                    return _buildRecommendationCard(
                      title: menu.namaProduk,
                      price: menu.harga,
                      onTap: () {
                        // Bisa navigasi ke detail menu
                      },
                    );
                  },
                ),
              const SizedBox(height: 20),
            ],
          ),
        ),
      ),
      bottomNavigationBar: const BottomBar(currentIndex: 0),
    );
  }

  void _searchMenus(String query) async {
    if (query.isEmpty) return;
    try {
      final allMenus = await MenuService.fetchAllMenus();
      final results = allMenus
          .where(
            (menu) =>
                menu.namaProduk.toLowerCase().contains(query.toLowerCase()),
          )
          .toList();
      showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Hasil Pencarian'),
          content: results.isEmpty
              ? const Text('Menu tidak ditemukan')
              : SizedBox(
                  height: 300,
                  width: double.maxFinite,
                  child: ListView.builder(
                    itemCount: results.length,
                    itemBuilder: (_, i) => ListTile(
                      title: Text(results[i].namaProduk),
                      subtitle: Text('Rp ${results[i].harga}'),
                      onTap: () => Navigator.pop(ctx),
                    ),
                  ),
                ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(ctx),
              child: const Text('Tutup'),
            ),
          ],
        ),
      );
    } catch (e) {
      // ignore
    }
  }

  Widget _buildCarouselImage(String assetPath) {
    return Container(
      width: 260,
      margin: const EdgeInsets.symmetric(horizontal: 8),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        image: DecorationImage(image: AssetImage(assetPath), fit: BoxFit.cover),
      ),
    );
  }

  Widget _buildOrderTypeButton({
    required String title,
    required IconData icon,
    required bool isSelected,
    required VoidCallback onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        height: 85,
        decoration: BoxDecoration(
          color: isSelected ? mainOrange : Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: isSelected ? null : Border.all(color: Colors.grey.shade300),
          boxShadow: [
            if (isSelected)
              BoxShadow(
                color: mainOrange.withOpacity(0.4),
                spreadRadius: 1,
                blurRadius: 8,
                offset: const Offset(0, 4),
              )
            else
              BoxShadow(
                color: Colors.grey.withOpacity(0.1),
                spreadRadius: 1,
                blurRadius: 8,
                offset: const Offset(0, 4),
              ),
          ],
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                title,
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  color: isSelected ? Colors.black87 : Colors.black87,
                  height: 1.2,
                ),
              ),
              Icon(icon, size: 36, color: Colors.black87),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildRecommendationCard({
    required String title,
    required double price,
    required VoidCallback onTap,
  }) {
    // Ambil asset lokal berdasarkan nama produk
    final assetPath = _getLocalImageAsset(title);

    return GestureDetector(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 10.0),
        child: Column(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: Image.asset(
                assetPath,
                height: 180,
                width: double.infinity,
                fit: BoxFit.cover,
                errorBuilder: (context, error, stackTrace) {
                  return Container(
                    height: 180,
                    color: Colors.grey[300],
                    child: const Center(
                      child: Icon(Icons.broken_image, size: 50),
                    ),
                  );
                },
              ),
            ),
            const SizedBox(height: 12),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w500,
                    color: Colors.black87,
                  ),
                ),
                Text(
                  'Rp ${price.toStringAsFixed(0)}',
                  style: TextStyle(
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                    color: mainOrange,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Container(
              height: 4,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    Colors.grey.withOpacity(0.3),
                    Colors.grey.withOpacity(0.0),
                  ],
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
