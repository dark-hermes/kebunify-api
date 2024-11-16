<?php

namespace App\Http\Controllers;

use App\Http\Resources\ForumCommentResource;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class ForumCommentController extends Controller
{
    public function index($forumId)
    {
        $comments = ForumComment::where('forum_id', $forumId)
            ->with('author:id,name', 'replies')
            ->get();

        return ForumCommentResource::collection($comments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
            'forum_id' => 'required|exists:forum,id',
            'parent_id' => 'nullable|exists:forum_comment,id',
        ]);

        if (!empty($validated['parent_id'])) {
            $parentComment = ForumComment::find($validated['parent_id']);
            if ($parentComment->parent_id !== null) {
                return response()->json(['message' => 'Replies can only reference main comments'], 400);
            }
        }

        $comment = ForumComment::create([
            'content' => $validated['content'],
            'forum_id' => $validated['forum_id'],
            'user_id' => $request->user()->id,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'data' => new ForumCommentResource($comment),
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500'
        ]);

        $comment = ForumComment::findOrFail($id);

        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->content = $validated['content'];
        $comment->save();

        return response()->json([
            'message' => 'Komentar berhasil diperbarui',
            'data' => new ForumCommentResource($comment)
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $comment = ForumComment::findOrFail($id);

        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Komentar berhasil dihapus'
        ], 200);
    }
}
