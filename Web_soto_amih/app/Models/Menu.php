<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'kategori',
        'harga',
        'stok',
        'foto',
    ];

    public function bahanJadi()
    {
        return $this->belongsToMany(BahanJadi::class, 'menu_bahan')
                    ->withPivot('kebutuhan')
                    ->withTimestamps();
    }

    // Hitung stok otomatis dari bahan jadi
    public function getStokOtomatisAttribute(): int
    {
        $bahans = $this->bahanJadi;

        if ($bahans->isEmpty()) {
            return $this->stok; // fallback ke stok manual
        }

        $porsi = $bahans->map(function ($bahan) {
            if ($bahan->pivot->kebutuhan <= 0) return PHP_INT_MAX;
            return floor($bahan->stok / $bahan->pivot->kebutuhan);
        });

        return (int) $porsi->min();
    }
}