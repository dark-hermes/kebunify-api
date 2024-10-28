<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Expert::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->input('user_id'));}

        if($request -> has('search')) {
            $searh = $request->input('search');
            $query->where('specialization','LIKE',"%{$searh}%");
        }
        return response()->json($query->get(),200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function leaderboard(Request $request)
    {
        $period = $request->input('period');
        $query = Expert::with('user');

        if ($period == 'month') {
            $query->whereMonth('created_at', now()->month);
        } elseif ($period == 'year') {
            $query->whereYear('created_at', now()->year);
        } elseif ($period == 'week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        if($request -> has('category')) {
            $searh = $request->input('category');
            $query->where('specialization','LIKE',"%{$searh}%");
        }


        return response()->json($query->orderBy('created_at','desc')->get(),200);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function promote(Request $request, $user_id)
    {
        $expert = Expert::create([
            'specialization' => $request->specialization,
            'consultation_price' => $request->consultation_price,
            'user_id' => $user_id
        ]);

        return response()->json(['message' => 'User promoted to expert successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expert = Expert::findOrFail($id);
        return response()->json($expert, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expert = Expert::findOrFail($id);

        $expert->update($request->only(['specialization', 'consultation_price']));

        return response()->json(['message' => 'Expert updated successfully', 'expert' => $expert], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expert = Expert::findOrFail($id);
        $expert->delete();

        return response()->json(['message' =>'Expert deleted successfully']);
    }
}