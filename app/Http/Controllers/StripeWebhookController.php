<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $signature,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $order = Order::where('stripe_session_id', $session->id)->first();

            if ($order && $order->status !== 'paid') {
                $order->update([
                    'status' => 'paid',
                    'stripe_payment_intent' => $session->payment_intent,
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
