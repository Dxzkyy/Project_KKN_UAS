<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalHarian extends Model
{
    protected $table = 'modal_harian';

    protected $fillable = [
        'tanggal',
        'nominal',
        'catatan',
        'owner_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}