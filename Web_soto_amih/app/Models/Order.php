<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Order - GANTIKAN file app/Models/Order.php yang sudah ada
 *
 * Perubahan dari versi sebelumnya:
 * - kasir_id sekarang nullable (untuk support order dari Android/guest)
 * - Tambah field: catatan, email_pembeli
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_order',
        'nama_pembeli',
        'tipe',
        'nomor_meja',
        'metode_bayar',
        'diskon',
        'tipe_diskon',
        'total',
        'status',
        'kasir_id',       // nullable untuk order dari Android
        'catatan',        // catatan tambahan dari pelanggan
        'email_pembeli',  // email untuk kirim resi
    ];

    protected $casts = [
        'total'  => 'decimal:2',
        'diskon' => 'decimal:2',
        'harga'  => 'decimal:2',
    ];

    // Relasi ke kasir (nullable)
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    // Relasi ke order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}