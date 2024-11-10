<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpertEducation;

class ExpertEducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $expertId)
    {
        try {
            $expert = ExpertEducation::where('expert_id', $expertId)->get();
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
            'degree' => 'required|string',
            'institution' => 'required|string',
            'graduation_year' => 'required|date_format:Y',
            'field_of_study' => 'required|string',
        ]);

        try {
            $expert = ExpertEducation::create([
                'expert_id' => $expertId,
                'degree' => $request->degree,
                'institution' => $request->institution,
                'graduation_year' => $request->graduation_year,
                'field_of_study' => $request->field_of_study,
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
            $expert = ExpertEducation::find($id);

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
            'degree' => 'required|string',
            'institution' => 'required|string',
            'graduation_year' => 'required|date_format:Y',
            'field_of_study' => 'required|string',
        ]);

        try {
            $expert = ExpertEducation::find($id);

            if (!$expert) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $expert->update([
                'degree' => $request->degree,
                'institution' => $request->institution,
                'graduation_year' => $request->graduation_year,
                'field_of_study' => $request->field_of_study,
            ]);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $expert->refresh(),
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
    public function destroy(string $expertId, string $id)
    {
        try {
            $expert = ExpertEducation::find($id);

            if (!$expert) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $expert->delete();

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
