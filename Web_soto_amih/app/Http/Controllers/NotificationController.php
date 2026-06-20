<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Ambil notif untuk user yang sedang login (polling)
     */
    public function fetch()
    {
        $user = auth()->user();

        $notifs = Notification::where('untuk_user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'judul'      => $n->judul,
                'pesan'      => $n->pesan,
                'ikon'       => $n->ikon,
                'warna'      => $n->warna,
                'is_read'    => $n->is_read,
                'waktu'      => $n->created_at->setTimezone('Asia/Jakarta')->diffForHumans(),
                'order_id'   => $n->order_id,
            ]);

        $unread = Notification::where('untuk_user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifs,
            'unread'        => $unread,
        ]);
    }

    /**
     * Tandai satu notif sebagai sudah dibaca
     */
    public function markRead($id)
    {
        Notification::where('id', $id)
            ->where('untuk_user_id', auth()->id())
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notif sebagai sudah dibaca
     */
    public function markAllRead()
    {
        Notification::where('untuk_user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}