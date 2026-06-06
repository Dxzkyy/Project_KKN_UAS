<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanJadi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bahan',
        'stok',
        'satuan',
    ];

    // Relasi ke menu melalui tabel pivot
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_bahan')
                    ->withPivot('kebutuhan')
                    ->withTimestamps();
    }
}