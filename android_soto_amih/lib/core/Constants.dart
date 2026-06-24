import 'package:flutter/material.dart';

class AppColors {
  static const Color primary = Color(0xFFF5A623);
  static const Color primaryDark = Color(0xFFE09510);
  static const Color primaryLight = Color(0xFFFFF3E0);
  static const Color background = Color(0xFFF7F7F7);
  static const Color white = Colors.white;
  static const Color textDark = Color(0xFF1A1A1A);
  static const Color textGrey = Color(0xFF888888);
  static const Color textLight = Color(0xFFBBBBBB);
  static const Color border = Color(0xFFEEEEEE);
  static const Color success = Color(0xFF4CAF50);
  static const Color error = Color(0xFFE53935);
  static const Color cardShadow = Color(0x14000000);
}

class AppConfig {
  // ✅ GANTI sesuai kebutuhan, pilih salah satu:

  // 1️⃣ Chrome / Web browser (flutter run -d chrome)
  // static const String baseUrl = 'http://127.0.0.1:8000';

  // 2️⃣ Emulator Android Studio
  // static const String baseUrl = 'http://10.0.2.2:8000';

  // 3️⃣ HP Fisik (ganti IP sesuai hasil ipconfig)
  static const String baseUrl = 'http://192.168.137.1:8000';

  // ✅ Aktifkan salah satu di bawah ini:
  // static const String baseUrl = 'http://127.0.0.1:8000'; // ← aktif sekarang

  static const String apiUrl = '$baseUrl/api/v1';
  static const String appName = 'Soto Amih';
  static const String appTagline = 'Soto Khas Betawi';
}

class AppTextStyles {
  static const TextStyle heading1 = TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.bold,
    color: AppColors.textDark,
  );
  static const TextStyle heading2 = TextStyle(
    fontSize: 18,
    fontWeight: FontWeight.bold,
    color: AppColors.textDark,
  );
  static const TextStyle heading3 = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: AppColors.textDark,
  );
  static const TextStyle body = TextStyle(
    fontSize: 14,
    color: AppColors.textDark,
  );
  static const TextStyle bodyGrey = TextStyle(
    fontSize: 14,
    color: AppColors.textGrey,
  );
  static const TextStyle price = TextStyle(
    fontSize: 14,
    fontWeight: FontWeight.w600,
    color: AppColors.textDark,
  );
  static const TextStyle priceOrange = TextStyle(
    fontSize: 15,
    fontWeight: FontWeight.bold,
    color: AppColors.primary,
  );
  static const TextStyle caption = TextStyle(
    fontSize: 12,
    color: AppColors.textGrey,
  );
  static const TextStyle button = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.bold,
    color: AppColors.white,
  );
}
