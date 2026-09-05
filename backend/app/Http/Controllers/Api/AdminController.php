<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function overview(): JsonResponse
    {
        return response()->json([
            'data' => [
                'users' => User::query()->count(),
                'products' => Product::query()->count(),
            ],
        ]);
    }
}
