<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestOrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'name' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:180'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'regex:/^[0-9]{5}$/'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $order = DB::transaction(function () use ($validated): Order {
            $lines = [];
            foreach ($validated['items'] as $item) {
                $product = Product::query()->whereKey($item['product_id'])->lockForUpdate()->firstOrFail();
                $variant = ($item['variant_id'] ?? null) ? ProductVariant::query()->whereKey($item['variant_id'])->where('product_id', $product->id)->lockForUpdate()->firstOrFail() : null;
                $stock = $variant?->stock ?? $product->stock;
                abort_if($stock < $item['quantity'], 422, "No hay suficiente stock para {$product->name}.");
                if ($variant) $variant->decrement('stock', $item['quantity']); else $product->decrement('stock', $item['quantity']);
                $lines[] = ['product_id' => $product->id, 'variant_id' => $variant?->id, 'product_name' => $product->name, 'variant_label' => $variant?->label, 'unit_price' => $variant?->price ?? $product->price, 'quantity' => $item['quantity']];
            }
            $subtotal = round(collect($lines)->sum(fn (array $line) => $line['unit_price'] * $line['quantity']), 2);
            $shipping = $subtotal >= 1500 ? 0 : 150;
            $order = Order::create(['guest_email' => $validated['email'], 'guest_name' => $validated['name'], 'guest_address' => ['line1' => $validated['address'], 'city' => $validated['city'], 'state' => $validated['state'], 'postal_code' => $validated['postal_code']], 'status' => 'pending', 'subtotal' => $subtotal, 'shipping' => $shipping, 'total' => $subtotal + $shipping]);
            $order->items()->createMany($lines);
            return $order;
        });

        return response()->json(['data' => ['id' => $order->id, 'status' => $order->status, 'total' => $order->total]], 201);
    }
}
