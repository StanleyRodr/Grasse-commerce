<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => Wishlist::with('product')->where('user_id', $request->user()->id)->get()->map(fn (Wishlist $item) => $this->transform($item))->values()]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(['product_id' => ['required', 'integer', 'exists:products,id']]);
        $item = Wishlist::firstOrCreate(['user_id' => $request->user()->id, 'product_id' => $validated['product_id']])->load('product');

        return response()->json(['data' => $this->transform($item)], 201);
    }

    public function destroy(Request $request, int $productId): JsonResponse
    {
        Wishlist::where('user_id', $request->user()->id)->where('product_id', $productId)->delete();

        return response()->json(['message' => 'Producto eliminado de favoritos.']);
    }

    private function transform(Wishlist $item): array
    {
        return [
            'id' => $item->id,
            'product' => [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'house' => $item->product->house,
                'price' => $item->product->price,
                'image' => $item->product->image,
            ],
        ];
    }
}
