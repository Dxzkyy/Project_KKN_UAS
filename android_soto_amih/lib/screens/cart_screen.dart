// TODO Implement this library.
import 'package:flutter/material.dart';
import '../widgets/bottombar.dart';

class CartScreen extends StatelessWidget {
  const CartScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Keranjang")),
      body: const Center(child: Text("Ini adalah halaman Keranjang")),
      bottomNavigationBar: const BottomBar(currentIndex: 0),
    );
  }
}
