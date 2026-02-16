<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $orders = Order::with('user')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return inertia('Orders/Index', [
            'data' => $orders,
            'filters' => [
                'status' => $status
            ]
        ]);
    }
}
