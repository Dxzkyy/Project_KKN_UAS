// import 'package:amihsotobetawii/screens/home_screen.dart';
import 'package:flutter/material.dart';

void main() {
  runApp(
    const MaterialApp(home: LoginScreen(), debugShowCheckedModeBanner: false),
  );
}

class LoginScreen extends StatelessWidget {
  const LoginScreen({super.key});

  // Warna utama yang digunakan dalam desain
  final Color mainOrange = const Color(0xFFF39C12);
  final Color lightYellow = const Color(0xFFFFCA28);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SingleChildScrollView(
        child: Column(
          children: [
            // -- BAGIAN HEADER (LENGKUNGAN & TEKS) --
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
                  // Dekorasi Lingkaran (Mirip keju/gelembung)
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

                  // Konten Header (Tombol Back & Judul)
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
                              // Aksi tombol back
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

            // -- BAGIAN FORM (INPUT NAMA & TOMBOL MASUK) --
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 32.0),
              child: Column(
                children: [
                  const SizedBox(height: 20),

                  // Field Nama Pelanggan
                  TextFormField(
                    initialValue: "",
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
                      prefixIcon: const Icon(Icons.person, color: Colors.grey),
                      enabledBorder: const UnderlineInputBorder(
                        borderSide: BorderSide(color: Colors.grey, width: 1.5),
                      ),
                      focusedBorder: UnderlineInputBorder(
                        borderSide: BorderSide(color: mainOrange, width: 2.0),
                      ),
                    ),
                  ),

                  const SizedBox(height: 50),

                  // Tombol Masuk
                  SizedBox(
                    width: double.infinity,
                    height: 55,
                    // child: ElevatedButton(
                    //   onPressed: () => Navigator.push(
                    //     context,
                    //     MaterialPageRoute(builder: (_) => const HomeScreen()),
                    //   ),
                    //   style: ElevatedButton.styleFrom(
                    //     backgroundColor: mainOrange,
                    //     shape: RoundedRectangleBorder(
                    //       borderRadius: BorderRadius.circular(8.0),
                    //     ),
                    //     elevation: 2,
                    //   ),
                    //   child: const Text(
                    //     "Masuk",
                    //     style: TextStyle(
                    //       fontSize: 18,
                    //       fontWeight: FontWeight.bold,
                    //       color: Colors.white,
                    //     ),
                    //   ),
                    // ),
                  ),
                  const SizedBox(height: 40),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// -- CUSTOM CLIPPER UNTUK EFEK GELOMBANG --
class HeaderClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    var path = Path();
    path.lineTo(0, size.height - 80);

    // Titik kontrol lengkungan pertama (Kiri ke Tengah)
    var firstControlPoint = Offset(size.width / 4, size.height);
    var firstEndPoint = Offset(size.width / 2.25, size.height - 30.0);
    path.quadraticBezierTo(
      firstControlPoint.dx,
      firstControlPoint.dy,
      firstEndPoint.dx,
      firstEndPoint.dy,
    );

    // Titik kontrol lengkungan kedua (Tengah ke Kanan)
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
