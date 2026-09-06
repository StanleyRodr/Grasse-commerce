<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function checkout(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_if($order->status !== 'pending', 422, 'Este pedido ya no puede pagarse.');

        $secret = config('services.stripe.secret');
        if (!$secret) return response()->json(['message' => 'Stripe no está configurado.'], 503);

        $session = (new StripeClient($secret))->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $order->load('items')->items->map(fn ($item) => [
                'price_data' => ['currency' => 'mxn', 'product_data' => ['name' => $item->product_name], 'unit_amount' => (int) round($item->unit_price * 100)],
                'quantity' => $item->quantity,
            ])->values()->all(),
            'metadata' => ['order_id' => (string) $order->id, 'user_id' => (string) $request->user()->id],
            'success_url' => rtrim(config('app.frontend_url'), '/') . '/checkout?payment=success&order=' . $order->id,
            'cancel_url' => rtrim(config('app.frontend_url'), '/') . '/checkout?payment=cancelled&order=' . $order->id,
        ]);

        return response()->json(['data' => ['id' => $session->id, 'url' => $session->url]]);
    }

    public function webhook(Request $request): JsonResponse
    {
        $secret = config('services.stripe.webhook_secret');
        if (!$secret) return response()->json(['message' => 'Stripe no está configurado.'], 503);

        try {
            $event = Webhook::constructEvent($request->getContent(), $request->header('Stripe-Signature', ''), $secret);
        } catch (\UnexpectedValueException|\Stripe\Exception\SignatureVerificationException) {
            return response()->json(['message' => 'Firma webhook inválida.'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $orderId = $event->data->object->metadata->order_id ?? null;
            if ($orderId) Order::whereKey($orderId)->where('status', 'pending')->update(['status' => 'processing']);
        }

        return response()->json(['received' => true]);
    }
}
