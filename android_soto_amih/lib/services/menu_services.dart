import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/menu_item.dart';

class MenuService {
  // Ganti dengan URL endpoint API Anda
  static const String baseUrl = 'http://localhost/Project_KKN_UAS/route_api';
  static const String menuEndpoint = '$baseUrl/menu.php';

  /// Mengambil semua menu dari server
  static Future<List<MenuItem>> fetchAllMenus() async {
    try {
      final response = await http.get(Uri.parse(menuEndpoint));

      if (response.statusCode == 200) {
        final Map<String, dynamic> jsonResponse = json.decode(response.body);

        if (jsonResponse['status'] == 'success') {
          final List<dynamic> data = jsonResponse['data'];
          return data.map((item) => MenuItem.fromJson(item)).toList();
        } else {
          throw Exception('API error: ${jsonResponse['message']}');
        }
      } else {
        throw Exception('Failed to load menus (HTTP ${response.statusCode})');
      }
    } catch (e) {
      throw Exception('Network error: $e');
    }
  }

  /// Mengambil menu berdasarkan kategori (opsional)
  static Future<List<MenuItem>> fetchMenusByCategory(String kategori) async {
    final allMenus = await fetchAllMenus();
    return allMenus.where((menu) => menu.kategori == kategori).toList();
  }
}
