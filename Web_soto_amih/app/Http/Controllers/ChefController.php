<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ChefController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.menu')
            ->whereIn('status', ['pending', 'proses'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'asc')
            ->get();

        // MENGARAH KE FOLDER BARU: chef/pesanan/index.blade.php
        return view('chef.pesanan.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan diperbarui!');
    }
}