<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ExpertSpecialization;

class ExpertSpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $paginate = $request->query('paginate') ?? 10;

        try {
            $expertSpecializations = ExpertSpecialization::query()
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', '%' . $search . '%');
                });

            $expertSpecializations = $paginate
                ? $expertSpecializations->paginate($paginate)
                : $expertSpecializations->get();

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $expertSpecializations,
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:32',
        ]);

        try {
            $expertSpecialization = null;

            DB::transaction(function () use ($request, &$expertSpecialization) {
                $expertSpecialization = ExpertSpecialization::create([
                    'name' => $request->name,
                ]);
            });

            return response()->json([
                'message' => __('http-statuses.201'),
                'data' => $expertSpecialization,
            ], 201);
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
    public function show(string $id)
    {
        try {
            $expertSpecialization = ExpertSpecialization::find($id);

            if (! $expertSpecialization) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $expertSpecialization,
            ]);
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
            'name' => 'required|string|max:32',
        ]);

        try {
            $expertSpecialization = ExpertSpecialization::find($id);

            if (! $expertSpecialization) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            DB::transaction(function () use ($request, $expertSpecialization) {
                $expertSpecialization->update([
                    'name' => $request->name,
                ]);
            });

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $expertSpecialization,
            ]);
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
            $expertSpecialization = ExpertSpecialization::find($id);

            if (! $expertSpecialization) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            DB::transaction(function () use ($expertSpecialization) {
                $expertSpecialization->delete();
            });

            return response()->json([
                'message' => __('http-statuses.200'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}