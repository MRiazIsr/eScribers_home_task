<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function index(): JsonResponse
    {
        try {
            $products = Product::all();

            return response()->json(ProductResource::collection($products));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getById($id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            return response()->json(new ProductResource($product));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'string|max:255',
                'description' => 'string|max:255',
                'price' => 'numeric|min:0',
                'availability' => 'boolean',
            ]);

            $product->fill($validatedData);
            $product->save();

            return response()->json(new ProductResource($product));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            $product->delete();

            return response()->json(['message' => 'Product deleted']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

