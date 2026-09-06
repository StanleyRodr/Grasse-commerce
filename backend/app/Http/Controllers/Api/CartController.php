<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->payload($this->cart($request))]);
    }

    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $this->cart($request);
        DB::transaction(function () use ($cart, $validated): void {
            $item = $cart->items()->firstOrNew(['product_id' => $validated['product_id']]);
            $item->quantity = min(99, ($item->quantity ?? 0) + ($validated['quantity'] ?? 1));
            $item->save();
        });

        return response()->json(['data' => $this->payload($cart->fresh())], 201);
    }

    public function replace(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $this->cart($request);
        DB::transaction(function () use ($cart, $validated): void {
            $cart->items()->delete();
            foreach ($validated['items'] as $item) {
                $cart->items()->create($item);
            }
        });

        return response()->json(['data' => $this->payload($cart->fresh())]);
    }

    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 404);
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $cartItem->update($validated);

        return response()->json(['data' => $this->payload($this->cart($request))]);
    }

    public function remove(Request $request, CartItem $cartItem): JsonResponse
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 404);
        $cartItem->delete();

        return response()->json(['data' => $this->payload($this->cart($request))]);
    }

    private function cart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id])->load('items.product');
    }

    private function payload(Cart $cart): array
    {
        $items = $cart->items->map(fn (CartItem $item) => [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'product' => [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'house' => $item->product->house,
                'price' => $item->product->price,
                'image' => $item->product->image,
            ],
            'lineTotal' => round($item->quantity * $item->product->price, 2),
        ])->values();

        $subtotal = round($items->sum('lineTotal'), 2);

        return [
            'id' => $cart->id,
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $subtotal === 0 || $subtotal >= 1500 ? 0 : 150,
            'total' => $subtotal === 0 || $subtotal >= 1500 ? $subtotal : $subtotal + 150,
        ];
    }
}
