// TODO Implement this library.
import 'package:flutter/material.dart';
import '../widgets/bottombar.dart';

class NotificationScreen extends StatelessWidget {
  const NotificationScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text("Notifikasi")),
      body: const Center(child: Text("Ini adalah halaman Notifikasi")),
      bottomNavigationBar: const BottomBar(currentIndex: 0),
    );
  }
}
