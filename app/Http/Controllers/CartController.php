<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $user = $request->user();

        if (empty($user->address)) {
            return response()->json([
                'message' => 'Address is required to make a transaction.'
            ], 400);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);

        $cartItem = CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $request->product_id],
            ['quantity' => $request->quantity]
        );

        return response()->json([
            'message' => 'Item added to cart successfully',
            'data' => $cartItem
        ]);
    }

    public function viewCart(Request $request)
    {
        $user = $request->user();

        if (empty($user->address)) {
            return response()->json([
                'message' => 'Address is required to make a transaction.'
            ], 400);
        }

        $cart = Cart::with('items.product')->where('user_id', Auth::id())->first();

        return response()->json([
            'message' => 'Cart retrieved successfully',
            'data' => $cart
        ]);
    }

    public function removeFromCart($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully'
        ]);
    }
}