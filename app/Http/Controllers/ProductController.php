<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::all();
        return response()->json($products);
    }
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function random()
    {
        $product = Product::inRandomOrder()->first();
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        if (!$seller) {
            return response()->json(['error' => 'User is not a seller'], 403);
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
            return response()->json($validator->errors(), 422);
        }

        $productData = $request->all();
        $productData['user_id'] = Auth::id(); // Add the authenticated user's ID

        $product = Product::create($productData);
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        if (!$seller) {
            return response()->json(['error' => 'User is not a seller'], 403);
        }

        $product = Product::findOrFail($id);

        // Check if the authenticated user is the owner of the product
        if ($product->user_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
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
            return response()->json($validator->errors(), 422);
        }

        $product->update($request->all());
        return response()->json($product);
    }

    public function destroy($id)
    {
        $seller = Seller::where('user_id', Auth::id())->first();
        if (!$seller) {
            return response()->json(['error' => 'User is not a seller'], 403);
        }

        $product = Product::findOrFail($id);

        // Check if the authenticated user is the owner of the product
        if ($product->user_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $product->delete();
        return response()->json(null, 204);
    }

    public function getByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)->get();
        return response()->json($products);
    }

    public function search(Request $request)
    {
        $query = $request->query('query');
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();
        return response()->json($products);
    }

    public function getRelated($id)
    {
        $product = Product::findOrFail($id);
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->get();
        return response()->json($relatedProducts);
    }

    public function getReviews($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product->reviews);
    }
}