<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;

class ForumTagController extends Controller
{
    /**
     * Fetch all tags.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $tags = Tag::all(['id', 'name']);

            return response()->json([
                'message' => 'Tags retrieved successfully',
                'data' => $tags,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching tags: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to fetch tags',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
