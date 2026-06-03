import 'dart:convert';
import 'package:http/http.dart' as http;

class CustomersService {
  // Ganti dengan IP/URL server Anda
  static const String baseUrl =
      'http://localhost/Project_KKN_UAS/route_api/customers.php';

  Future<Map<String, dynamic>> saveCustomer(String nama) async {
    final url = Uri.parse(baseUrl);
    final headers = {'Content-Type': 'application/json'};
    final body = jsonEncode({'nama': nama});

    try {
      final response = await http.post(url, headers: headers, body: body);

      if (response.statusCode == 200) {
        final Map<String, dynamic> data = jsonDecode(response.body);
        if (data['success'] == true) {
          return data;
        } else {
          throw Exception(data['error'] ?? 'Unknown error');
        }
      } else {
        throw Exception('Server error: ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Network error: $e');
    }
  }
}
