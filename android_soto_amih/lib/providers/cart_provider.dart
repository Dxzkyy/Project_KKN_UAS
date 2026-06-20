import 'package:flutter/foundation.dart';
import '../../models/menu_model.dart';
import '../../models/cart_item_model.dart';

class CartProvider extends ChangeNotifier {
  final List<CartItem> _items = [];
  String _catatan = '';

  List<CartItem> get items => _items;
  String get catatan => _catatan;

  int get totalItem => _items.fold(0, (sum, e) => sum + e.jumlah);

  double get totalHarga => _items.fold(0.0, (sum, e) => sum + e.subtotal);

  bool contains(MenuModel menu) => _items.any((e) => e.menu.id == menu.id);

  int getJumlah(MenuModel menu) {
    final idx = _items.indexWhere((e) => e.menu.id == menu.id);
    return idx >= 0 ? _items[idx].jumlah : 0;
  }

  void tambah(MenuModel menu) {
    final idx = _items.indexWhere((e) => e.menu.id == menu.id);
    if (idx >= 0) {
      _items[idx].jumlah++;
    } else {
      _items.add(CartItem(menu: menu));
    }
    notifyListeners();
  }

  void kurang(MenuModel menu) {
    final idx = _items.indexWhere((e) => e.menu.id == menu.id);
    if (idx >= 0) {
      if (_items[idx].jumlah > 1) {
        _items[idx].jumlah--;
      } else {
        _items.removeAt(idx);
      }
      notifyListeners();
    }
  }

  void hapus(MenuModel menu) {
    _items.removeWhere((e) => e.menu.id == menu.id);
    notifyListeners();
  }

  void setCatatan(String val) {
    _catatan = val;
    notifyListeners();
  }

  void clearCart() {
    _items.clear();
    _catatan = '';
    notifyListeners();
  }

  List<Map<String, dynamic>> toApiItems() {
    return _items
        .map((e) => {'menu_id': e.menu.id, 'jumlah': e.jumlah})
        .toList();
  }
}
