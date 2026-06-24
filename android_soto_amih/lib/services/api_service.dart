import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../core/constants.dart';
import '../../models/menu_model.dart';
import '../../models/order_model.dart';

class ApiService {
  static const _headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  // ── Menu ─────────────────────────────────────────────────────────────────

  static Future<List<MenuModel>> getMenus({
    String kategori = 'Semua',
    String search = '',
  }) async {
    final params = <String, String>{};
    if (kategori != 'Semua') params['kategori'] = kategori;
    if (search.isNotEmpty) params['search'] = search;

    final uri = Uri.parse(
      '${AppConfig.apiUrl}/menus',
    ).replace(queryParameters: params);
    final res = await http.get(uri, headers: _headers);

    if (res.statusCode == 200) {
      final body = jsonDecode(res.body);
      return (body['data'] as List).map((e) => MenuModel.fromJson(e)).toList();
    }
    throw Exception('Gagal memuat menu');
  }

  // ── Order ─────────────────────────────────────────────────────────────────

  static Future<Map<String, dynamic>> createOrder({
    required String namaPembeli,
    required String nomorMeja,
    required String email,
    required String tipe, // dine_in | takeaway
    required String metodeBayar, // tunai | qris | bank
    required String catatan,
    required List<Map<String, dynamic>> items,
  }) async {
    final uri = Uri.parse('${AppConfig.apiUrl}/orders');
    final res = await http.post(
      uri,
      headers: _headers,
      body: jsonEncode({
        'nama_pembeli': namaPembeli,
        'nomor_meja': nomorMeja,
        'email': email,
        'tipe': tipe,
        'metode_bayar': metodeBayar,
        'catatan': catatan,
        'items': items,
      }),
    );

    final body = jsonDecode(res.body);
    if (res.statusCode == 201) {
      return {'success': true, 'data': body['data']};
    }
    return {
      'success': false,
      'message': body['message'] ?? 'Gagal membuat pesanan',
    };
  }

  static Future<OrderModel?> getOrder(String kodeOrder) async {
    final uri = Uri.parse('${AppConfig.apiUrl}/orders/$kodeOrder');
    final res = await http.get(uri, headers: _headers);

    if (res.statusCode == 200) {
      final body = jsonDecode(res.body);
      return OrderModel.fromJson(body['data']);
    }
    return null;
  }

  static Future<Map<String, dynamic>?> getOrderStatus(String kodeOrder) async {
    final uri = Uri.parse('${AppConfig.apiUrl}/orders/$kodeOrder/status');
    final res = await http.get(uri, headers: _headers);

    if (res.statusCode == 200) {
      final body = jsonDecode(res.body);
      return body['data'];
    }
    return null;
  }

  static Future<List<dynamic>> getRiwayatByEmail(String email) async {
    final uri = Uri.parse(
      '${AppConfig.apiUrl}/orders/riwayat',
    ).replace(queryParameters: {'email': email});
    final res = await http.get(uri, headers: _headers);

    if (res.statusCode == 200) {
      final body = jsonDecode(res.body);
      if (body['success'] == true) {
        return body['data'] as List<dynamic>;
      }
    }
    return [];
  }

  static Future<Map<String, dynamic>> cancelOrder(String kodeOrder) async {
    final uri = Uri.parse('${AppConfig.apiUrl}/orders/$kodeOrder/cancel');
    final res = await http.patch(uri, headers: _headers);
    final body = jsonDecode(res.body);
    return {
      'success': body['success'] ?? false,
      'message': body['message'] ?? '',
    };
  }
}
