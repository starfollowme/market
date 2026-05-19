<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Dashboard stats
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'total_users'    => User::where('role', 'customer')->count(),
                'total_orders'   => Order::count(),
                'total_revenue'  => Order::where('status', 'paid')->sum('total_amount'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_products' => Product::where('is_active', true)->count(),
            ],
        ]);
    }

    // List semua order
    public function orders(Request $request): JsonResponse
    {
        $status = $request->query('status');

        $orders = Order::with(['user', 'items', 'payment'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->get()
            ->map(fn($o) => [
                'id'            => $o->id,
                'order_number'  => $o->order_number,
                'status'        => $o->status,
                'total_amount'  => $o->total_amount,
                'customer_name' => $o->customer_name,
                'payment_method'=> $o->payment_method,
                'created_at'    => $o->created_at->toISOString(),
                'user'          => ['id' => $o->user->id, 'name' => $o->user->name, 'email' => $o->user->email],
                'items_count'   => $o->items->count(),
                'payment_status'=> $o->payment?->status,
            ]);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    // Update status order
    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,completed,cancelled',
        ]);

        $order->update(['status' => $data['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status order diperbarui.',
            'data'    => ['id' => $order->id, 'status' => $order->status],
        ]);
    }

    // List semua user
    public function users(): JsonResponse
    {
        $users = User::where('role', 'customer')
            ->withCount('orders')
            ->latest()
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'name'         => $u->name,
                'email'        => $u->email,
                'phone'        => $u->phone,
                'orders_count' => $u->orders_count,
                'created_at'   => $u->created_at->toISOString(),
            ]);

        return response()->json(['success' => true, 'data' => $users]);
    }

    // CRUD Product
    public function storeProduct(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image_url'   => 'nullable|url',
            'is_active'   => 'boolean',
        ]);

        $product = Product::create($data);

        return response()->json(['success' => true, 'message' => 'Produk ditambahkan.', 'data' => $product], 201);
    }

    public function updateProduct(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'image_url'   => 'sometimes|nullable|url',
            'is_active'   => 'sometimes|boolean',
        ]);

        $product->update($data);

        return response()->json(['success' => true, 'message' => 'Produk diperbarui.', 'data' => $product]);
    }

    public function deleteProduct(Product $product): JsonResponse
    {
        $product->update(['is_active' => false]);

        return response()->json(['success' => true, 'message' => 'Produk dinonaktifkan.']);
    }
}
