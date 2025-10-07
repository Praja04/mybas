<?php

namespace App\Http\Controllers\Ite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ite\Order;

class OrderController extends Controller
{
    public function index()
    {
        return view('ite.orders.order-list');
    }

    public function all()
    {
        $orders = Order::all();
        foreach($orders as $order) {
            if($order->items != null) {
                foreach($order->items as $item) {
                    $item->material;
                    $item->io;
                }
            }
            $order->project;
        }
        return response()->json([
            'success' => 1,
            'data' => $orders,
            'message' => 'Get orders succeed'
        ]);
    }
}
