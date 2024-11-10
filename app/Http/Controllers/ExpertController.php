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
        $search = $request->query('search');
        $paginate = $request->query('paginate') ?? 10;

        try {
            $experts = Expert::query()
                ->when($search, function ($query, $search) {
                    return $query->whereRelation('user', 'name', 'like', '%' . $search . '%')
                        ->orWhereRelation('specialization', 'name', 'like', '%' . $search . '%');
                })->with('user', 'specialization');

            $experts = ! $paginate
                ? $experts->get()
                : $experts->paginate($paginate);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $experts,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id)
    {
        try {
            $expert = Expert::with('user', 'specialization')->find($id);

            if (!$expert) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $expert,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.404'),
            ], 404);
        }
    }
    
    
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
        $approvedDocuments = Document::where('user_id', $user_id)
            ->where('status', 'APPORVED')
            ->where('role_applied', 'expert')
            ->exists();

        $expert = Expert::create([
            'specialization' => $request->specialization,
            'consultation_price' => $request->consultation_price,
            'user_id' => $user_id
        ]);

        return response()->json(['message' => 'User promoted to expert successfully'], 200);
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
