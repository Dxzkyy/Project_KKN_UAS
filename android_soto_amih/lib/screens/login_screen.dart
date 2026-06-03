import 'package:android_soto_amih/services/customers_services.dart';
import 'package:flutter/material.dart';
import 'package:android_soto_amih/screens/home_screen.dart'; // Import HomeScreen

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _namaController = TextEditingController();
  final _customersService = CustomersService();
  bool _isLoading = false;

  final Color mainOrange = const Color(0xFFF39C12);
  final Color lightYellow = const Color(0xFFFFCA28);

  @override
  void dispose() {
    _namaController.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _isLoading = true;
    });

    try {
      final result = await _customersService.saveCustomer(
        _namaController.text.trim(),
      );

      if (!mounted) return;

      // Ambil nama customer dari response
      final String customerName =
          result['data']['nama'] ?? _namaController.text.trim();

      // Tampilkan pesan sukses
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('${result['message']}, $customerName!'),
          duration: const Duration(seconds: 1),
        ),
      );

      // Navigasi ke HomeScreen dengan membawa nama customer
      Navigator.pushReplacement(
        context,
        MaterialPageRoute(
          builder: (context) => HomeScreen(userName: customerName),
        ),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error: $e'), backgroundColor: Colors.red),
      );
    } finally {
      if (mounted) {
        setState(() {
          _isLoading = false;
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            SizedBox(
              height: 420,
              child: Stack(
                children: [
                  // Latar Belakang Gelombang Orange
                  ClipPath(
                    clipper: HeaderClipper(),
                    child: Container(
                      width: double.infinity,
                      height: 420,
                      color: mainOrange,
                    ),
                  ),
                  // Dekorasi Lingkaran
                  Positioned(
                    top: -30,
                    right: -20,
                    child: CircleAvatar(
                      radius: 90,
                      backgroundColor: lightYellow.withOpacity(0.8),
                    ),
                  ),
                  Positioned(
                    top: 40,
                    right: 90,
                    child: CircleAvatar(
                      radius: 35,
                      backgroundColor: const Color(0xFFE67E22).withOpacity(0.5),
                    ),
                  ),
                  Positioned(
                    top: 170,
                    right: 15,
                    child: CircleAvatar(
                      radius: 75,
                      backgroundColor: lightYellow.withOpacity(0.9),
                    ),
                  ),
                  Positioned(
                    bottom: 120,
                    left: 70,
                    child: CircleAvatar(
                      radius: 40,
                      backgroundColor: lightYellow.withOpacity(0.9),
                    ),
                  ),
                  Positioned(
                    bottom: 50,
                    right: 90,
                    child: CircleAvatar(
                      radius: 25,
                      backgroundColor: lightYellow.withOpacity(0.9),
                    ),
                  ),
                  Positioned(
                    bottom: 0,
                    left: -20,
                    child: CircleAvatar(
                      radius: 50,
                      backgroundColor: lightYellow.withOpacity(0.9),
                    ),
                  ),

                  SafeArea(
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 24.0,
                        vertical: 16.0,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          GestureDetector(
                            onTap: () {
                              Navigator.maybePop(context);
                            },
                            child: const Icon(
                              Icons.arrow_back,
                              color: Colors.white,
                              size: 28,
                            ),
                          ),
                          const SizedBox(height: 40),
                          const Text(
                            "Selamat Datang\nPelanggan",
                            style: TextStyle(
                              fontSize: 38,
                              fontWeight: FontWeight.w800,
                              color: Colors.white,
                              height: 1.3,
                              shadows: [
                                Shadow(
                                  blurRadius: 5.0,
                                  color: Colors.black26,
                                  offset: Offset(1.0, 2.0),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32.0),
              child: Form(
                key: _formKey,
                child: Column(
                  children: [
                    const SizedBox(height: 20),
                    TextFormField(
                      controller: _namaController,
                      style: TextStyle(
                        color: mainOrange,
                        fontWeight: FontWeight.w500,
                      ),
                      decoration: InputDecoration(
                        hintText: "Masukkan Nama Anda",
                        hintStyle: TextStyle(
                          color: Colors.grey.withOpacity(0.7),
                          fontWeight: FontWeight.w500,
                        ),
                        prefixIcon: const Icon(
                          Icons.person,
                          color: Colors.grey,
                        ),
                        enabledBorder: const UnderlineInputBorder(
                          borderSide: BorderSide(
                            color: Colors.grey,
                            width: 1.5,
                          ),
                        ),
                        focusedBorder: UnderlineInputBorder(
                          borderSide: BorderSide(color: mainOrange, width: 2.0),
                        ),
                      ),
                      validator: (value) =>
                          value == null || value.trim().isEmpty
                          ? 'Nama tidak boleh kosong'
                          : null,
                    ),
                    const SizedBox(height: 50),
                    _isLoading
                        ? Center(
                            child: CircularProgressIndicator(
                              valueColor: AlwaysStoppedAnimation<Color>(
                                mainOrange,
                              ),
                            ),
                          )
                        : SizedBox(
                            width: double.infinity,
                            height: 55,
                            child: ElevatedButton(
                              onPressed: _handleLogin,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: mainOrange,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(8.0),
                                ),
                                elevation: 2,
                              ),
                              child: const Text(
                                "Masuk",
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              ),
                            ),
                          ),
                    const SizedBox(height: 40),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class HeaderClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    var path = Path();
    path.lineTo(0, size.height - 80);

    var firstControlPoint = Offset(size.width / 4, size.height);
    var firstEndPoint = Offset(size.width / 2.25, size.height - 30.0);
    path.quadraticBezierTo(
      firstControlPoint.dx,
      firstControlPoint.dy,
      firstEndPoint.dx,
      firstEndPoint.dy,
    );

    var secondControlPoint = Offset(
      size.width - (size.width / 3.25),
      size.height - 80,
    );
    var secondEndPoint = Offset(size.width, size.height - 50);
    path.quadraticBezierTo(
      secondControlPoint.dx,
      secondControlPoint.dy,
      secondEndPoint.dx,
      secondEndPoint.dy,
    );

    path.lineTo(size.width, size.height - 50);
    path.lineTo(size.width, 0.0);
    path.close();

    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}
