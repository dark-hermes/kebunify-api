<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Exception;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $transaction = Transaction::where('transaction_number', 'like', "%{$request->search}%")
                ->orWhere('status', 'like', "%{$request->search}%")
                ->orWhere('payment_status', 'like', "%{$request->search}%")
                ->orderBy('created_at', 'desc')
                ->paginate($request->limit ?? 10);

            return response()->json([
                'message' => 'Transactions retrieved successfully',
                'data' => $transaction->load('items.product')
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve transactions', 'error' => $e->getMessage()], 400);
        }
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            return response()->json([
                'message' => 'Transaction retrieved successfully',
                'data' => $transaction->load('items.product')
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to retrieve transaction', 'error' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $totalAmount = 0;
            $items = [];
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product) {
                    throw new Exception("Product not found: {$item['product_id']}");
                }

                if ($product->stock < $item['quantity']) {
                    throw new Exception("Insufficient stock for product: {$product->name}");
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $items[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];
            }

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'transaction_number' => 'TRX-' . Str::random(10),
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $request->notes
            ]);

            foreach ($items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                $item['product']->decrement('stock', $item['quantity']);
            }

            return response()->json([
                'message' => 'Transaction created successfully',
                'data' => $transaction->load('items.product')
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Transaction failed', 'error' => $e->getMessage()], 400);
        }
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $data = [
                'status' => $request->status,
                'notes' => $request->input('notes', $transaction->notes)
            ];

            if ($request->status === 'cancelled' && $transaction->status !== 'cancelled') {
                $this->handleCancellation($transaction);
                $data['cancelled_at'] = now();
            }

            $transaction->update($data);

            return response()->json([
                'message' => 'Transaction status updated successfully',
                'data' => $transaction->fresh(['items.product', 'user'])
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update transaction status', 'error' => $e->getMessage()], 400);
        }
    }

    private function handleCancellation(Transaction $transaction)
    {
        try {
            foreach ($transaction->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        } catch (Exception $e) {
            throw new Exception("Failed to handle cancellation: " . $e->getMessage());
        }
    }

    public function updatePaymentStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid,failed,refunded',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $data = [
                'payment_status' => $request->payment_status,
                'notes' => $request->input('notes', $transaction->notes),
            ];

            if ($request->payment_status === 'paid' && !$transaction->paid_at) {
                $data['paid_at'] = now();
            }

            $transaction->update($data);

            return response()->json([
                'message' => 'Payment status updated successfully',
                'data' => $transaction->fresh(['items.product', 'user'])
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update payment status', 'error' => $e->getMessage()], 400);
        }
    }

    // public function destroy($id)
    // {
    //     $transaction = Transaction::findOrFail($id);

    //     // Check if the authenticated user is the owner of the transaction
    //     if ($transaction->user_id !== Auth::id() or Auth::user()->role !== 'admin') {
    //         return response()->json(['error' => 'Forbidden'], 403);
    //     }
        

    //     $transaction->delete();
    //     return response()->json(['message' => 'Transaction deleted successfully']);
    // }

    // public function getByUserId(Request $request)
    // {
    //     $transactions = Transaction::where('user_id', Auth::id())->get();
    //     return response()->json($transactions);
    // }

    // public function getBySellerId(Request $request)
    // {
    //     $transactions = Transaction::where('seller_id', Auth::id())->get();
    //     return response()->json($transactions);
    // }

    // public function getByStatus(Request $request, $status)
    // {
    //     $transactions = Transaction::where('status', $status)->get();
    //     return response()->json($transactions);
    // }

    // public function getItems ($id) {
    //     $transaction = Transaction::findOrFail($id);
    //     $items = $transaction->items;
    //     return response()->json($items);
    // }

    // public function addItems (Request $request, $id) {
    //     $transaction = Transaction::findOrFail($id);
    //     $transaction->items()->attach($request->items);
    //     return response()->json($transaction->items);
    // }

    // public function removeItems (Request $request, $id) {
    //     $transaction = Transaction::findOrFail($id);
    //     $transaction->items()->detach($request->items);
    //     return response()->json($transaction->items);
    // }

    // public function updateItems (Request $request, $id) {
    //     $transaction = Transaction::findOrFail($id);
    //     $transaction->items()->sync($request->items);
    //     return response()->json($transaction->items);
    // }


}
