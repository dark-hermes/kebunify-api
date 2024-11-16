<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Exception;

class ReviewController extends Controller
{
    public function index($productId)
    {
        try {
            $reviews = Review::where('product_id', $productId)->get();
            return response()->json([
                'message' => 'Reviews retrieved successfully',
                'data' => $reviews
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve reviews', 'error' => $e->getMessage()], 400);
        }
    }

    public function show($id){
        try {
            $review = Review::findOrFail($id);
            return response()->json([
                'message' => 'Review retrieved successfully',
                'data' => $review
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve review',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function store(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            // Check if the user has a completed transaction for the specified product
            $hasCompletedTransaction = Transaction::where('user_id', Auth::id())
                ->whereHas('items', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })
                ->where('status', 'completed')
                ->exists();

            if (!$hasCompletedTransaction) {
                return response()->json(['error' => 'You can only review products from completed transactions'], 403);
            }

            $review = Review::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            return response()->json($review, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to create review', 'error' => $e->getMessage()], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $review = Review::findOrFail($id);

            if ($review->user_id !== Auth::id()) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $validator = Validator::make($request->all(), [
                'rating' => 'sometimes|required|integer|min:1|max:5',
                'comment' => 'sometimes|required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $review->update($request->all());
            return response()->json($review);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update review', 'error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $review = Review::findOrFail($id);

            if ($review->user_id !== Auth::id()) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            $review->delete();
            return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to delete review', 'error' => $e->getMessage()], 400);
        }
    }
}
