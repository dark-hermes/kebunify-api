<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class TransactionController extends Controller
{
public function index(Request $request)
{
    try {
        $user = $request->user();

        // Check if the user is a seller
        $isSeller = $user->roles()->where('name', 'seller')->exists();

        $query = Transaction::with([
            'items.product.category',
            'items.product.reviews.user.roles',
            'items.product.user',
            'user'
        ]);

        if ($isSeller) {
            // Filter transactions based on the products owned by the seller
            $query->whereHas('items.product', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } else {
            // Filter by authenticated user
            $query->where('user_id', $user->id);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('payment_status', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter - validate allowed values 
        if ($request->filled('status')) {
            $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];
            $statuses = array_intersect(explode(',', $request->status), $allowedStatuses);
            $query->whereIn('status', $statuses);
        }

        // Payment status filter - validate allowed values
        if ($request->filled('payment_status')) {
            $allowedPaymentStatuses = ['unpaid', 'paid', 'failed', 'refunded'];
            $paymentStatuses = array_intersect(explode(',', $request->payment_status), $allowedPaymentStatuses);
            $query->whereIn('payment_status', $paymentStatuses);
        }

        $perPage = min($request->limit ?? 10, 100);
        $transactions = $query->orderBy('created_at', 'desc')
                            ->paginate($perPage);

        return response()->json([
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions
        ]);
    } catch (Exception $e) {
        Log::error('Error fetching transactions: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to retrieve transactions',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function show($id)
{
    try {
        $transaction = Transaction::with([
            'items.product.category',
            'items.product.reviews.user.roles',
            'items.product.user',
            'user'
        ])->findOrFail($id);

        return response()->json([
            'message' => 'Transaction retrieved successfully',
            'data' => $transaction
        ]);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Transaction not found'
        ], 404);
    } catch (Exception $e) {
        Log::error('Error retrieving transaction: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to retrieve transaction',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function store(Request $request)
{
    $user = $request->user();

    if (empty($user->address)) {
        return response()->json([
            'message' => 'Address is required to make a transaction.'
        ], 400);
    }

    $request->validate([
        'items' => 'nullable|array|min:1',
        'items.*.product_id' => 'required_with:items|exists:products,id',
        'items.*.quantity' => 'required_with:items|integer|min:1',
        'notes' => 'nullable|string|max:500'
    ]);

    try {
        $itemsGroupedByStore = [];
        $totalAmount = 0;

        if ($request->filled('items')) {
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product) {
                    throw new Exception("Product not found: {$item['product_id']}");
                }

                if ($product->stock < $item['quantity']) {
                    throw new Exception("Insufficient stock for product: {$product->name}");
                }

                $subtotal = $product->price * $item['quantity'];
                $storeId = $product->user_id; // Assuming user_id is the store ID

                $itemsGroupedByStore[$storeId][] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ];

                $totalAmount += $subtotal;
            }
        }

        // Create a single transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'transaction_number' => 'TRX-' . strtoupper(Str::random(10)),
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'notes' => $request->notes,
            'address' => $user->address
        ]);

        foreach ($itemsGroupedByStore as $storeId => $items) {
            foreach ($items as $item) {
                $transaction->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal']
                ]);

                // Increment the total sales count for the product
                $item['product']->increment('total_sales', $item['quantity']);
            }
        }

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => $transaction
        ]);
    } catch (Exception $e) {
        Log::error('Error creating transaction: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to create transaction',
            'error' => $e->getMessage()
        ], 500);
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
