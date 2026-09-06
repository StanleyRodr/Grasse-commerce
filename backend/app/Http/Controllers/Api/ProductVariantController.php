<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): JsonResponse
    {
        $variant = $product->variants()->create($request->validate(['label' => ['required', 'string', 'max:40'], 'volume_ml' => ['required', 'integer', 'min:1'], 'price' => ['required', 'numeric', 'min:0'], 'stock' => ['required', 'integer', 'min:0']]));
        return response()->json(['data' => $variant], 201);
    }

    public function update(Request $request, ProductVariant $variant): JsonResponse
    {
        $variant->update($request->validate(['label' => ['sometimes', 'string', 'max:40'], 'volume_ml' => ['sometimes', 'integer', 'min:1'], 'price' => ['sometimes', 'numeric', 'min:0'], 'stock' => ['sometimes', 'integer', 'min:0']]));
        return response()->json(['data' => $variant->fresh()]);
    }

    public function destroy(ProductVariant $variant): JsonResponse
    {
        $variant->delete();
        return response()->json(['message' => 'Variante eliminada.']);
    }
}
