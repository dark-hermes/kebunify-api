<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Seller::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        return response()->json($query->get(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function promote(Request $request, $user_id)
    {
        $approvedDocuments = Document::where('user_id', $user_id)
            ->where('status', 'APPROVED')
            ->where('role_applied', 'seller')
            ->exists();

        if (!$approvedDocuments) {
            return response()->json(['error' => 'User documents have not been approved'], 403);
        }

        $seller = Seller::create([
            'store_name' => $request->store_name,
            'address' => $request->address,
            'avatar' => $request->avatar,
            'status' => $request->status,
            'user_id' => $user_id
        ]);

        return response()->json(['message' => 'User promoted to seller successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $seller = Seller::findOrFail($id);
        return response()->json($seller, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $seller = Seller::findOrFail($id);

        $seller->update($request->only(['store_name', 'address', 'avatar', 'status']));

        return response()->json(['message' => 'Seller updated successfully', 'seller' => $seller], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $seller = Seller::findOrFail($id);
        $seller->delete();

        return response()->json(['message' => 'Seller deleted successfully']);
    }
}