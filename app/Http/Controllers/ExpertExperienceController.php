<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpertExperience;
use Illuminate\Support\Facades\Auth;

class ExpertExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $expertId)
    {
        try {
            $expert = ExpertExperience::where('expert_id', $expertId)->get();
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $expertId)
    {
        $request->validate([
            'position' => 'required|string',
            'company' => 'required|string',
            'start_year' => 'required|date_format:Y',
            'end_year' => 'nullable|date_format:Y',
            'description' => 'nullable|string',
        ]);

        try {
            $expert = ExpertExperience::create([
                'expert_id' => $expertId,
                'position' => $request->position,
                'company' => $request->company,
                'start_year' => $request->start_year,
                'end_year' => $request->end_year,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => __('http-statuses.201'),
                'data' => $expert,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function storeAuth(Request $request)
    {
        $request->validate([
            'position' => 'required|string',
            'company' => 'required|string',
            'start_year' => 'required|date_format:Y',
            'end_year' => 'nullable|date_format:Y',
            'description' => 'nullable|string',
        ]);

        try {
            $expert = ExpertExperience::create([
                'expert_id' => Auth::user()->expert->id,
                'position' => $request->position,
                'company' => $request->company,
                'start_year' => $request->start_year,
                'end_year' => $request->end_year,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => __('http-statuses.201'),
                'data' => $expert,
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
    public function show(string $expertId, string $id)
    {
        try {
            $expert = ExpertExperience::where('expert_id', $expertId)->find($id);

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $expertId, string $id)
    {
        $request->validate([
            'position' => 'required|string',
            'company' => 'required|string',
            'start_year' => 'required|date_format:Y',
            'end_year' => 'nullable|date_format:Y',
            'description' => 'nullable|string',
        ]);

        try {
            $expert = ExpertExperience::where('expert_id', $expertId)->find($id);

            if (!$expert) {
                return response()->json(['message' => 'Expert experience not found'], 404);
            }

            $expert->update([
                'position' => $request->position,
                'company' => $request->company,
                'start_year' => $request->start_year,
                'end_year' => $request->end_year,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Expert experience updated successfully',
                'data' => $expert->refresh(),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function updateAuth(Request $request, string $id)
    {
        $request->validate([
            'position' => 'required|string',
            'company' => 'required|string',
            'start_year' => 'required|date_format:Y',
            'end_year' => 'nullable|date_format:Y',
            'description' => 'nullable|string',
        ]);

        try {
            $expert = ExpertExperience::where('expert_id', Auth::user()->expert->id)->find($id);

            if (!$expert) {
                return response()->json(['message' => 'Expert experience not found'], 404);
            }

            $expert->update([
                'position' => $request->position,
                'company' => $request->company,
                'start_year' => $request->start_year,
                'end_year' => $request->end_year,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Expert experience updated successfully',
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
    public function destroy(string $expertId, string $id)
    {
        try {
            $expert = ExpertExperience::where('expert_id', $expertId)->find($id);

            if (!$expert) {
                return response()->json(['message' => 'Expert experience not found'], 404);
            }

            $expert->delete();

            return response()->json([
                'message' => 'Expert experience deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function destroyAuth(string $id)
    {
        try {
            $expert = ExpertExperience::where('expert_id', Auth::user()->expert->id)->find($id);

            if (!$expert) {
                return response()->json(['message' => 'Expert experience not found'], 404);
            }

            $expert->delete();

            return response()->json([
                'message' => 'Expert experience deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}
