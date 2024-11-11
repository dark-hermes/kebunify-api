<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use App\Models\Transaction;

class ReviewController extends Controller
{
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)->get();
        return response()->json($reviews);
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
    }
    public function update(Request $request, $id)
    {
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
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        if ($review->user_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $review->delete();
        return response()->json(null, 204);
    }
}
