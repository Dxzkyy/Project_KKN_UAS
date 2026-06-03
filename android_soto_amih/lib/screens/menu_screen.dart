// TODO Implement this library.
import 'package:flutter/material.dart';
import '../widgets/bottombar.dart';

class MenuScreen extends StatelessWidget {
  const MenuScreen({super.key, required String serviceType});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Menu")),
      body: const Center(child: Text("Ini adalah halaman Menu")),
      bottomNavigationBar: const BottomBar(currentIndex: 0),
    );
  }
}
