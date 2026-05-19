<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items', 'payment'])
            ->latest()
            ->get()
            ->map(fn($o) => $this->orderResource($o));

        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
        }

        $order->load(['items', 'payment', 'user']);

        return response()->json(['success' => true, 'data' => $this->orderResource($order)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method'   => 'required|in:transfer,cod,ewallet',
            'notes'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($data, $request) {
            $totalAmount = 0;
            $orderItems  = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if (! $product->is_active) {
                    return response()->json(['success' => false, 'message' => "Produk {$product->name} tidak tersedia."], 422);
                }

                if ($product->stock < $item['quantity']) {
                    return response()->json(['success' => false, 'message' => "Stok {$product->name} tidak cukup."], 422);
                }

                $subtotal      = $product->price * $item['quantity'];
                $totalAmount  += $subtotal;
                $orderItems[]  = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $product->price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $subtotal,
                ];

                // Kurangi stok
                $product->decrement('stock', $item['quantity']);
            }

            $order = Order::create([
                'user_id'          => $request->user()->id,
                'order_number'     => Order::generateNumber(),
                'status'           => 'pending',
                'total_amount'     => $totalAmount,
                'customer_name'    => $data['customer_name'],
                'customer_phone'   => $data['customer_phone'],
                'shipping_address' => $data['shipping_address'],
                'payment_method'   => $data['payment_method'],
                'notes'            => $data['notes'] ?? null,
            ]);

            $order->items()->createMany($orderItems);

            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $data['payment_method'],
                'amount'         => $totalAmount,
                'status'         => 'pending',
            ]);

            $order->load(['items', 'payment']);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'data'    => $this->orderResource($order),
            ], 201);
        });
    }

    public function pay(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Order sudah diproses.'], 422);
        }

        $now = now();

        $order->update([
            'status'  => 'paid',
            'paid_at' => $now,
        ]);

        $order->payment()->update([
            'status'         => 'success',
            'transaction_id' => Payment::generateTransactionId(),
            'paid_at'        => $now,
        ]);

        $order->load(['items', 'payment']);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil.',
            'data'    => $this->orderResource($order),
        ]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403);
        }

        if (! in_array($order->status, ['pending'])) {
            return response()->json(['success' => false, 'message' => 'Order tidak bisa dibatalkan.'], 422);
        }

        // Kembalikan stok
        foreach ($order->items as $item) {
            $item->product?->increment('stock', $item->quantity);
        }

        $order->update(['status' => 'cancelled']);
        $order->payment()->update(['status' => 'failed']);

        return response()->json(['success' => true, 'message' => 'Pesanan dibatalkan.']);
    }

    private function orderResource(Order $order): array
    {
        return [
            'id'               => $order->id,
            'order_number'     => $order->order_number,
            'status'           => $order->status,
            'total_amount'     => $order->total_amount,
            'customer_name'    => $order->customer_name,
            'customer_phone'   => $order->customer_phone,
            'shipping_address' => $order->shipping_address,
            'payment_method'   => $order->payment_method,
            'paid_at'          => $order->paid_at?->toISOString(),
            'notes'            => $order->notes,
            'created_at'       => $order->created_at->toISOString(),
            'items'            => $order->items->map(fn($i) => [
                'id'           => $i->id,
                'product_id'   => $i->product_id,
                'product_name' => $i->product_name,
                'price'        => $i->price,
                'quantity'     => $i->quantity,
                'subtotal'     => $i->subtotal,
            ])->toArray(),
            'payment' => $order->payment ? [
                'id'             => $order->payment->id,
                'method'         => $order->payment->payment_method,
                'amount'         => $order->payment->amount,
                'status'         => $order->payment->status,
                'transaction_id' => $order->payment->transaction_id,
                'paid_at'        => $order->payment->paid_at?->toISOString(),
            ] : null,
        ];
    }
}
