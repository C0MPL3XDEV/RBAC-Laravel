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
        // Check if the user have the permission to view products
        if (!$request->user()->can('view-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to view products!",
            ], 403);
        }

        $product = Product::paginate(10); // Return paginated products

        return response()->json([
            "success" => true,
            "product" => $product, // Return products response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request) : JsonResponse
    {
        // Check if the user have the permission to create a product
        if (!$request->user()->can('create-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to create products!",
            ]);
        }

        // Validate data to create a product
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric",
            "sku" => "required|string",
            "category" => "required|string",
            "is_active" => "required|boolean",
        ]);

        $product = Product::create($validated); // Create product with validated data

        return response()->json([ // Return response with product data created
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
        // The $product parameter is automatically injected by Laravel's route model binding,
        // and contains the Product model instance corresponding to the ID provided in the route.

        // Check if the user have the permission to update a product
        if (!$request->user()->can('update-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to update products!",
            ], 403);
        }

        // Validated data to update a product
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "required|string",
            "price" => "required|numeric",
            "sku" => "required|string",
            "category" => "required|string",
            "is_active" => "required|boolean",
        ]);

        $product->update($validated); // Update the product with validated data

        return response()->json([ // Return response with product data updated
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
        // The $product parameter is automatically injected by Laravel's route model binding,
        // and contains the Product model instance corresponding to the ID provided in the route.

        // Check if the user have the permission to delete the product
        if (!$request->user()->can('delete-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to delete products!",
            ], 403);
        }

        $product->delete(); // Delete the product

        return response()->json([
            "success" => true,
            "message" => "Product deleted successfully!",
        ]);
    }

    /**
     * GET a specific product by ID
     */
    public function getProductById(Request $request, Product $product): JsonResponse {

        // The $product parameter is automatically injected by Laravel's route model binding,
        // and contains the Product model instance corresponding to the ID provided in the route.

        // Check if the product have the permission to view a product
        if (!$request->user()->can('view-products')) {
            return response()->json([
                "success" => false,
                "message" => "You don't have permission to view products!",
            ], 403);
        }

        return response()->json([ // Return the response with product data
            "success" => true,
            "product" => $product,
        ]);
    }
}
