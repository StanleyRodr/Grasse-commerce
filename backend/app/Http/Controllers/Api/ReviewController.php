<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function mine(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->reviews()->with('product:id,name,house,image')->latest()->get()]);
    }

    public function index(Product $product): JsonResponse
    {
        return response()->json(['data' => Review::with('user:id,name')->where('product_id', $product->id)->latest()->paginate(10)]);
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate(['rating' => ['required', 'integer', 'min:1', 'max:5'], 'comment' => ['required', 'string', 'min:10', 'max:2000']]);
        $hasDeliveredPurchase = $request->user()->orders()->where('status', 'delivered')->whereHas('items', fn ($query) => $query->where('product_id', $product->id))->exists();
        abort_unless($hasDeliveredPurchase, 403, 'Solo compradores con pedido entregado pueden reseñar este producto.');

        $review = Review::updateOrCreate(['user_id' => $request->user()->id, 'product_id' => $product->id], [...$validated, 'verified_purchase' => true]);
        return response()->json(['data' => $review->load('user:id,name')], $review->wasRecentlyCreated ? 201 : 200);
    }
}
