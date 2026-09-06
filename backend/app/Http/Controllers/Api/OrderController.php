<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->orders()->with('items', 'address')->latest()->paginate(10)]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        return response()->json(['data' => $order->load('items', 'address')]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(['address_id' => ['required', 'integer', 'exists:addresses,id']]);
        $address = $request->user()->addresses()->findOrFail($validated['address_id']);
        $cart = Cart::with('items.product', 'items.variant')->where('user_id', $request->user()->id)->first();
        abort_if(!$cart || $cart->items->isEmpty(), 422, 'El carrito está vacío.');

        $subtotal = round($cart->items->sum(fn ($item) => $item->quantity * ($item->variant?->price ?? $item->product->price)), 2);
        $shipping = $subtotal >= 1500 ? 0 : 150;
        $order = DB::transaction(function () use ($request, $address, $cart, $subtotal, $shipping): Order {
            foreach ($cart->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::query()->whereKey($item->variant_id)->lockForUpdate()->firstOrFail();
                    abort_if($variant->stock < $item->quantity, 422, 'No hay suficiente stock para una presentación seleccionada.');
                    $variant->decrement('stock', $item->quantity);
                } else {
                    $product = Product::query()->whereKey($item->product_id)->lockForUpdate()->firstOrFail();
                    abort_if($product->stock < $item->quantity, 422, "No hay suficiente stock para {$product->name}.");
                    $product->decrement('stock', $item->quantity);
                }
            }

            $order = $request->user()->orders()->create(['address_id' => $address->id, 'status' => 'pending', 'subtotal' => $subtotal, 'shipping' => $shipping, 'total' => $subtotal + $shipping]);
            foreach ($cart->items as $item) $order->items()->create(['product_id' => $item->product_id, 'variant_id' => $item->variant_id, 'product_name' => $item->product->name, 'variant_label' => $item->variant?->label, 'unit_price' => $item->variant?->price ?? $item->product->price, 'quantity' => $item->quantity]);
            $cart->items()->delete();
            return $order;
        });

        return response()->json(['data' => $order->load('items', 'address')], 201);
    }
}
