<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'occasion' => ['nullable', 'string', 'max:50'],
            'scent_family' => ['nullable', 'string', 'max:50'],
            'min_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'sort' => ['nullable', 'in:featured,price-low,price-high,rating'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        $query = Product::query();

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('house', 'like', "%{$search}%");
            });
        }

        foreach (['category', 'occasion', 'scent_family'] as $filter) {
            if (!empty($validated[$filter])) {
                $query->where($filter, $validated[$filter]);
            }
        }

        $query->when(isset($validated['min_rating']), fn ($builder) => $builder->where('rating', '>=', $validated['min_rating']))
            ->when(isset($validated['max_price']), fn ($builder) => $builder->where('price', '<=', $validated['max_price']));

        match ($validated['sort'] ?? 'featured') {
            'price-low' => $query->orderBy('price'),
            'price-high' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating')->orderByDesc('reviews_count'),
            default => $query->orderBy('id'),
        };

        $products = $query->paginate($validated['per_page'] ?? 12)->withQueryString();

        return response()->json([
            'data' => collect($products->items())->map(fn (Product $product) => $this->transform($product))->values(),
            'meta' => [
                'currentPage' => $products->currentPage(),
                'lastPage' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $this->transform($product)]);
    }

    private function transform(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'house' => $product->house,
            'category' => $product->category,
            'scentFamily' => $product->scent_family,
            'occasion' => $product->occasion,
            'price' => $product->price,
            'rating' => $product->rating,
            'reviews' => $product->reviews_count,
            'reviewCount' => $product->reviews_count,
            'image' => $product->image,
            'badge' => $product->badge,
            'description' => $product->description,
            'notes' => $product->notes ?? [],
        ];
    }
}
