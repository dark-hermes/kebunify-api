<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumCommentController extends Controller
{
    public function index($forumId)
    {
        try {
            $comments = ForumComment::where('forum_id', $forumId)
                ->whereNull('parent_id')
                ->with(['replies', 'user:id,name'])
                ->get();

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $comments,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function store(Request $request, $forumId)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:forum_comments,id',
        ]);

        try {
            $comment = ForumComment::create([
                'forum_id' => $forumId,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'parent_id' => $request->parent_id,
            ]);

            return response()->json([
                'message' => __('http-statuses.201'),
                'data' => $comment->load('user:id,name', 'replies'),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        try {
            $comment = ForumComment::findOrFail($id);

            if ($comment->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $comment->update(['content' => $request->content]);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $comment,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $comment = ForumComment::findOrFail($id);

            if ($comment->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $comment->delete();

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
