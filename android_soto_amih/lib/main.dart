import 'package:android_soto_amih/screens/welcome_screen.dart';
import 'package:flutter/material.dart';
// pastikan file login_screen.dart berada di folder yang sama atau sesuaikan path

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Soto Amih',
      theme: ThemeData(
        primarySwatch: Colors.blue,
        visualDensity: VisualDensity.adaptivePlatformDensity,
      ),
      home: WelcomeScreen(),
      debugShowCheckedModeBanner: false,
    );
  }
}
