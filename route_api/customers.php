<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Konfigurasi database
$host = 'localhost';
$dbname = 'laravel';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Hanya menerima method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Baca input JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['nama']) || empty(trim($input['nama']))) {
    http_response_code(400);
    echo json_encode(['error' => 'Nama tidak boleh kosong']);
    exit();
}

$nama = trim($input['nama']);

try {
    // Cek apakah pelanggan sudah ada berdasarkan nama
    $stmt = $pdo->prepare("SELECT id, nama FROM customers WHERE nama = ?");
    $stmt->execute([$nama]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        echo json_encode([
            'success' => true,
            'message' => 'Selamat datang kembali',
            'data' => [
                'id' => $existing['id'],
                'nama' => $existing['nama']
            ]
        ]);
        exit();
    }

    // Insert pelanggan baru
    $stmt = $pdo->prepare("INSERT INTO customers (nama) VALUES (?)");
    $stmt->execute([$nama]);
    $newId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Pelanggan berhasil disimpan',
        'data' => [
            'id' => $newId,
            'nama' => $nama
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
