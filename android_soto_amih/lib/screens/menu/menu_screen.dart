import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../../../core/constants.dart';
import '../../../../models/menu_model.dart';
import '../../../../services/api_service.dart';
import '../../providers/cart_provider.dart';
import '../../../../widgets/menu_card.dart';

class MenuScreen extends StatefulWidget {
  const MenuScreen({super.key});

  @override
  State<MenuScreen> createState() => _MenuScreenState();
}

class _MenuScreenState extends State<MenuScreen> {
  List<MenuModel> _menus = [];
  bool _loading = true;
  String _kategori = 'Semua';
  String _search = '';
  final _searchController = TextEditingController();

  final List<String> _kategoriList = ['Semua', 'Makanan', 'Minuman', 'Lainnya'];

  @override
  void initState() {
    super.initState();
    _loadMenus();
  }

  Future<void> _loadMenus() async {
    setState(() => _loading = true);
    try {
      final menus = await ApiService.getMenus(
        kategori: _kategori,
        search: _search,
      );
      setState(() {
        _menus = menus;
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
        child: Column(
          children: [
            _buildSearchBar(),
            _buildKategoriChips(),
            Expanded(
              child: _loading
                  ? const Center(
                      child: CircularProgressIndicator(
                        color: AppColors.primary,
                      ),
                    )
                  : _menus.isEmpty
                  ? _buildEmpty()
                  : RefreshIndicator(
                      color: AppColors.primary,
                      onRefresh: _loadMenus,
                      child: GridView.builder(
                        padding: const EdgeInsets.all(16),
                        gridDelegate:
                            const SliverGridDelegateWithFixedCrossAxisCount(
                              crossAxisCount: 2,
                              childAspectRatio: 0.8,
                              crossAxisSpacing: 12,
                              mainAxisSpacing: 12,
                            ),
                        itemCount: _menus.length,
                        itemBuilder: (_, i) => MenuCard(menu: _menus[i]),
                      ),
                    ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSearchBar() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
      child: TextField(
        controller: _searchController,
        onChanged: (val) {
          _search = val;
          _loadMenus();
        },
        decoration: InputDecoration(
          hintText: 'Cari Lauk...',
          hintStyle: AppTextStyles.bodyGrey,
          prefixIcon: const Icon(Icons.search, color: AppColors.textGrey),
          suffixIcon: _search.isNotEmpty
              ? IconButton(
                  icon: const Icon(Icons.clear, color: AppColors.textGrey),
                  onPressed: () {
                    _searchController.clear();
                    _search = '';
                    _loadMenus();
                  },
                )
              : null,
          filled: true,
          fillColor: AppColors.white,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: BorderSide.none,
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(12),
            borderSide: BorderSide.none,
          ),
          contentPadding: const EdgeInsets.symmetric(vertical: 14),
        ),
      ),
    );
  }

  Widget _buildKategoriChips() {
    return SizedBox(
      height: 44,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: _kategoriList.length,
        itemBuilder: (_, i) {
          final k = _kategoriList[i];
          final selected = _kategori == k;
          return GestureDetector(
            onTap: () {
              setState(() => _kategori = k);
              _loadMenus();
            },
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 200),
              margin: const EdgeInsets.only(right: 8),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
              decoration: BoxDecoration(
                color: selected ? AppColors.primary : AppColors.white,
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: selected ? AppColors.primary : AppColors.border,
                ),
              ),
              child: Text(
                k,
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: selected ? AppColors.white : AppColors.textGrey,
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(
            Icons.restaurant_menu,
            size: 60,
            color: AppColors.textLight,
          ),
          const SizedBox(height: 12),
          Text('Menu tidak ditemukan', style: AppTextStyles.bodyGrey),
        ],
      ),
    );
  }
}
