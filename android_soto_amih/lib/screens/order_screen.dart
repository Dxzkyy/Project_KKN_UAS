// TODO Implement this library.
import 'package:flutter/material.dart';
import '../widgets/bottombar.dart';

class OrderScreen extends StatelessWidget {
  const OrderScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Pesanan")),
      body: const Center(child: Text("Ini adalah halaman Pesanan")),
      bottomNavigationBar: const BottomBar(currentIndex: 0),
    );
  }
}
