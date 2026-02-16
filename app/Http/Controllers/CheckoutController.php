<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function address(Request $request)
    {
        return Inertia::render('Checkout/Address');
    }
    public function storeAddress(Request $request)
    {

        $data = $request->validate([
            'address_line' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer',
            'cart.*.name' => 'required|string',
            'cart.*.price' => 'required|numeric',
            'cart.*.quantity' => 'required|integer|min:1',
        ]);

        $total = collect($data['cart'])->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $order = Order::create([
            'user_id' => Auth::id(),
            'address_line' => $data['address_line'],
            'city' => $data['city'],
            'postal_code' => $data['postal_code'],
            'country' => $data['country'],
            'status' => 'pending',
            'total' => $total,
        ]);

        foreach ($data['cart'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return back()->with('orderId', $order->id);
    }
    public function pay(Request $request)
    {
        $order = Order::with('items.book')->findOrFail($request->order);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $lineItems = $order->items->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item->book->name ?? 'Producto',
                    ],
                    'unit_amount' => intval($item->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        $session = \Stripe\Checkout\Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
        ]);

        $order->update([
            'stripe_session_id' => $session->id,
        ]);
        return redirect($session->url);
    }


    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            abort(404, 'Session ID missing');
        }

        $order = Order::where('stripe_session_id', $sessionId)->firstOrFail();

        $order->update([
            'status' => 'paid',
        ]);

        return inertia('Checkout/Success', [
            'orderId' => $order->id,
        ]);
    }
    public function cancel()
    {
        return inertia('Checkout/Cancel');
    }
}
