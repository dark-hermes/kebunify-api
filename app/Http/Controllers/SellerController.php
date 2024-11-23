<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;
use Illuminate\Support\Facades\Log;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Load essential relationships only
            $query = Seller::with([
                'user.roles',
                'user:id,name,email,avatar'
            ]);

            // Filter by user_id
            if ($request->has('user_id')) {
                $query->where('user_id', $request->input('user_id'));
            }

            // Filter by store_name
            if ($request->has('store_name')) {
                $storeName = $request->input('store_name');
                $query->where('store_name', 'LIKE', "%{$storeName}%");
            }

            // Search in store_name, address, and user name
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('store_name', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('email', 'LIKE', "%{$search}%");
                      });
                });
            }

            // Execute query and get results
            $sellers = $query->get();

            return response()->json([
                'message' => 'Sellers retrieved successfully',
                'data' => $sellers
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sellers: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to fetch sellers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function promote(Request $request, $user_id)
    {
        try {
            $approvedDocuments = Document::where('user_id', $user_id)
                ->where('status', 'APPROVED')
                ->where('role_applied', 'seller')
                ->exists();

            if (!$approvedDocuments) {
                return response()->json([
                    'message' => 'User documents have not been approved'
                ], 403);
            }

            $seller = Seller::create([
                'store_name' => $request->store_name,
                'address' => $request->address,
                'avatar' => $request->avatar,
                'status' => $request->status,
                'user_id' => $user_id
            ]);

            return response()->json([
                'message' => 'User promoted to seller successfully',
                'data' => $seller
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error promoting user to seller: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to promote user to seller',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $seller = Seller::findOrFail($id);
            return response()->json([
                'message' => 'Seller retrieved successfully',
                'data' => $seller
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Seller not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error retrieving seller: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to retrieve seller',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $seller = Seller::findOrFail($id);
            $seller->update($request->only(['store_name', 'address', 'avatar', 'status']));

            return response()->json([
                'message' => 'Seller updated successfully',
                'data' => $seller
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Seller not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating seller: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to update seller',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $seller = Seller::findOrFail($id);
            $seller->delete();

            return response()->json([
                'message' => 'Seller deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Seller not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting seller: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete seller',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}