<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanHarian extends Model
{
    protected $table = 'laporan_harian';

    protected $fillable = [
        'tanggal',
        'pendapatan_kotor',
        'total_pesanan',
        'modal_harian',
        'laba_bersih',
        'catatan',
        'kasir_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}