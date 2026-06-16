// TODO Implement this library.
import 'package:flutter/material.dart';
import '../widgets/bottombar.dart';

class profileScreen extends StatelessWidget {
  const profileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Profil")),
      body: const Center(child: Text("Ini adalah halaman Profil")),
      bottomNavigationBar: const BottomBar(currentIndex: 0),
    );
  }
}
