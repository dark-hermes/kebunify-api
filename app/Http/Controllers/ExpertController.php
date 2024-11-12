<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\Document;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $limit = $request->query('limit') ?? 10;

        try {
            $experts = Expert::query()
                ->when($search, function ($query, $search) {
                    return $query->whereRelation('user', 'name', 'like', '%' . $search . '%')
                        ->orWhereRelation('specialization', 'name', 'like', '%' . $search . '%');
                })->with('user', 'specialization')->paginate($limit);

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
            $expert = Expert::where('id', $id)->with('user', 'specialization', 'educations', 'experiences')->first();

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
        $period = $request->query('period') ?? 'month';
        $category = $request->query('category') ?? null;
        $limit = $request->query('limit') ?? 5;

        try {
            $experts = Expert::with('user');
            if ($period == 'month') {
                $experts->whereMonth('created_at', now()->month);
            } elseif ($period == 'year') {
                $experts->whereYear('created_at', now()->year);
            } elseif ($period == 'week') {
                $experts->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            }

            if ($category) {
                $experts->whereRelation('specialization', 'name', $category);
            }

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $experts->orderBy('created_at', 'desc')->take($limit)->get(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function promote(Request $request, $user_id)
    {
        $request->validate([
            'expert_specialization_id' => 'required|exists:expert_specializations,id',
            'consulting_fee' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0|max:100',
        ]);

        try {
            $approvedDocuments = Document::where('user_id', $user_id)
                ->where('status', 'APPROVED')
                ->where('role_applied', 'expert')
                ->exists();

            if (!$approvedDocuments) {
                return response()->json(['message' => 'User does not have approved documents'], 400);
            }

            $expert = Expert::create([
                'user_id' => $user_id,
                'expert_specialization_id' => $request->expert_specialization_id,
                'consulting_fee' => $request->consulting_fee,
                'discount' => $request->discount,
            ]);

            return response()->json([
                'message' => 'User promoted to expert successfully',
                'data' => $expert,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'expert_specialization_id' => 'required|exists:expert_specializations,id',
            'start_year' => 'required|integer|min:1900|max:' . now()->year,
            'consulting_fee' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0|max:100',
            'bio' => 'nullable|string|max:512',
        ]);

        try {
            $expert = Expert::find($id);

            if (!$expert) {
                return response()->json(['message' => 'Expert not found'], 404);
            }

            $expert->update([
                'expert_specialization_id' => $request->expert_specialization_id,
                'start_year' => $request->start_year,
                'consulting_fee' => $request->consulting_fee,
                'discount' => $request->discount,
                'bio' => $request->bio,
            ]);

            return response()->json([
                'message' => 'Expert updated successfully',
                'data' => $expert->refresh(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $expert = Expert::find($id);

            if (!$expert) {
                return response()->json(['message' => 'Expert not found'], 404);
            }

            $expert->delete();

            return response()->json(['message' => 'Expert deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function switchStatus(string $id)
    {
        try {
            $expert = Expert::find($id);

            if (!$expert) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $expert->update([
                'is_active' => ! $expert->is_active,
            ]);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $expert,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}
