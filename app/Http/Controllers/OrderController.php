<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $orders = Order::all();

            return response()->json(OrderResource::collection($orders));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|numeric|exists:users,id',
                'product_id' => 'required|numeric|exists:products,id',
            ]);

            $order = DB::transaction(function () use ($validatedData) {
                $order = new Order([
                    'user_id' => $validatedData['user_id'],
                    'product_id' => $validatedData['product_id']
                ]);

                $order->save();

                return $order;
            });

            return response()->json(new OrderResource($order), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            $validatedData = $request->validate([
                'status' => 'string|in:pending,processing,completed',
                'shipping_address' => 'string',
                'billing_address' => 'string',
                'payment_method' => 'string',
            ]);

            if (isset($validatedData['status'])) {
                $order->status = $validatedData['status'];
            }

            if (isset($validatedData['shipping_address'])) {
                $order->shipping_address = $validatedData['shipping_address'];
            }

            if (isset($validatedData['billing_address'])) {
                $order->billing_address = $validatedData['billing_address'];
            }

            if (isset($validatedData['payment_method'])) {
                $order->payment_method = $validatedData['payment_method'];
            }

            $order->save();

            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

