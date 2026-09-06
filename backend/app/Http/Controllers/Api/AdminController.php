<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function overview(): JsonResponse
    {
        return response()->json([
            'data' => [
                'users' => User::query()->count(),
                'products' => Product::query()->count(),
                'orders' => Order::query()->count(),
                'sales' => round(Order::query()->whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total'), 2),
                'averageOrder' => round(Order::query()->whereIn('status', ['processing', 'shipped', 'delivered'])->avg('total') ?? 0, 2),
            ],
        ]);
    }

    public function orders(): JsonResponse
    {
        return response()->json(['data' => Order::with('user:id,name,email', 'items')->latest()->paginate(20)]);
    }

    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate(['status' => ['required', 'in:pending,processing,shipped,delivered,cancelled']]);
        $order->update($validated);

        return response()->json(['data' => $order->fresh()->load('user:id,name,email', 'items')]);
    }
}
