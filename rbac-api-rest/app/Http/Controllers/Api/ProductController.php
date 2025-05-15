<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request): JsonResponse
    {
        if (!$request->user()->can('view-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to view products!",
            ], 403);
        }

        $product = Product::paginate(10);

        return response()->json([
            "success" => true,
            "product" => $product,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request) : JsonResponse
    {
        if (!$request->user()->can('create-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to create products!",
            ]);
        }

        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric",
            "sku" => "required|string",
            "category" => "required|string",
            "is_active" => "required|boolean",
        ]);

        $product = Product::create($validated);

        return response()->json([
            "success" => true,
            "message" => "Product created successfully!",
            "product" => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Product $product) : JsonResponse
    {
        if (!$request->user()->can('update-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to update products!",
            ], 403);
        }

        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric",
            "sku" => "required|string",
            "category" => "required|string",
            "is_active" => "required|boolean",
        ]);

        $product->update($validated);

        return response()->json([
            "success" => true,
            "message" => "Product updated successfully!",
            "product" => $product,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(Request $request, Product $product) : JsonResponse
    {
        if (!$request->user()->can('delete-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to delete products!",
            ], 403);
        }

        $product->delete();

        return response()->json([
            "success" => true,
            "message" => "Product deleted successfully!",
        ]);
    }

    public function getProduct(Request $request, Product $product): JsonResponse {
        if (!$request->user()->can('view-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to view products!",
            ], 403);
        }

        return response()->json([
            "success" => true,
            "product" => $product,
        ]);
    }
}
