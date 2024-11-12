<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $products = Product::all();
            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching products: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json([
                'message' => 'Product retrieved successfully',
                'data' => $product
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving product: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function random()
    {
        try {
            $product = Product::inRandomOrder()->first();
            if (!$product) {
                return response()->json([
                    'message' => 'No products found'
                ], 404);
            }
            return response()->json([
                'message' => 'Random product retrieved successfully',
                'data' => $product
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving random product: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve random product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $seller = Seller::where('user_id', Auth::id())->first();
            if (!$seller) {
                return response()->json([
                    'message' => 'User is not a seller'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|integer',
                'image_url' => 'nullable|url',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $productData = $request->all();
            $productData['user_id'] = Auth::id();

            $product = Product::create($productData);
            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
{
    try {
        $seller = Seller::where('user_id', Auth::id())->first();
        if (!$seller) {
            return response()->json([
                'message' => 'User is not a seller'
            ], 403);
        }

        $product = Product::findOrFail($id);

        if ($product->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'category_id' => 'sometimes|required|exists:categories,id',
            'stock' => 'sometimes|required|integer',
            'image_url' => 'sometimes|nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Product not found'
        ], 404);
    } catch (\Exception $e) {
        Log::error('Error updating product: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to update product',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function destroy($id)
    {
        try {
            $seller = Seller::where('user_id', Auth::id())->first();
            if (!$seller) {
                return response()->json([
                    'message' => 'User is not a seller'
                ], 403);
            }

            $product = Product::findOrFail($id);

            if ($product->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'Forbidden'
                ], 403);
            }

            $product->delete();
            return response()->json([
                'message' => 'Product deleted successfully'
            ], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getByCategory($category_id)
    {
        try {
            $products = Product::where('category_id', $category_id)->get();
            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching products by category: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->query('query');
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->get();
            return response()->json([
                'message' => 'Products retrieved successfully',
                'data' => $products
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching products: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to search products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRelated($id)
    {
        try {
            $product = Product::findOrFail($id);
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $id)
                ->get();
            return response()->json([
                'message' => 'Related products retrieved successfully',
                'data' => $relatedProducts
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching related products: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch related products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getReviews($id)
    {
        try {
            $product = Product::findOrFail($id);
            return response()->json([
                'message' => 'Reviews retrieved successfully',
                'data' => $product->reviews
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching product reviews: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}