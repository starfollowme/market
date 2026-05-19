<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::query()
            ->active()
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        if (! $product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    public function health(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Market API is running.',
            'app' => config('app.name'),
        ]);
    }
}
