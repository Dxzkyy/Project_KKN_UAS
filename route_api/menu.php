<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

$host = 'localhost';
$dbname = 'laravel';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, kode_produk, nama_produk, kategori, harga, stok, foto, created_at, updated_at FROM menus ORDER BY id DESC");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Base URL foto (sesuaikan dengan lokasi storage Laravel Anda)
    $baseFotoUrl = 'http://localhost/Project_KKN_UAS/Web_soto_amih/storage/app/public/menus/';
    // Jika pakai emulator Android, ganti localhost dengan 10.0.2.2
    // $baseFotoUrl = 'http://10.0.2.2/Project_KKN_UAS/Web_soto_amih/public/storage/menus/';

    foreach ($menus as &$menu) {
        $menu['harga'] = (float) $menu['harga'];   // konversi ke float
        $menu['stok'] = (int) $menu['stok'];       // konversi ke int
        $menu['foto_url'] = $baseFotoUrl . $menu['foto'];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $menus
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
