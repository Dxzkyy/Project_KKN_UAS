<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'judul',
        'pesan',
        'ikon',
        'warna',
        'untuk_role',
        'untuk_user_id',
        'dari_user_id',
        'order_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function dariUser()
    {
        return $this->belongsTo(User::class, 'dari_user_id');
    }

    public function untukUser()
    {
        return $this->belongsTo(User::class, 'untuk_user_id');
    }

    /**
     * Kirim notif ke semua user dengan role tertentu
     */
    public static function kirimKeRole(string $role, array $data): void
    {
        $users = User::where('role', $role)->get();
        foreach ($users as $user) {
            self::create(array_merge($data, [
                'untuk_role'    => $role,
                'untuk_user_id' => $user->id,
            ]));
        }
    }
}