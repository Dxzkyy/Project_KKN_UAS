import 'menu_model.dart';

class CartItem {
  final MenuModel menu;
  int jumlah;

  CartItem({required this.menu, this.jumlah = 1});

  double get subtotal => menu.harga * jumlah;
}
