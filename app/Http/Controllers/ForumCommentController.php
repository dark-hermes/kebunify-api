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
            ->with('author')
            ->with('replies')
            ->get();

        return ForumCommentResource::collection($comments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment_content' => 'required|string|max:500',
            'forum_id' => 'required|exists:forum,id',
            'user_id' => 'required|exists:users,id',
            'parent_id' => 'nullable|exists:forum_comment,id'
        ]);

        $comment = ForumComment::create([
            'comment_content' => $validated['comment_content'],
            'forum_id' => $validated['forum_id'],
            'user_id' => $validated['user_id'],
            'parent_id' => $validated['parent_id'] ?? null
        ]);

        return response()->json([
            'message' => 'Komentar berhasil ditambahkan',
            'data' => new ForumCommentResource($comment)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'comment_content' => 'required|string|max:500'
        ]);

        $comment = ForumComment::findOrFail($id);

        $comment->comment_content = $validated['comment_content'];
        $comment->save();

        return response()->json([
            'message' => 'Komentar berhasil diperbarui',
            'data' => new ForumCommentResource($comment)
        ], 200);
    }

    public function destroy($id)
    {
        $comment = ForumComment::findOrFail($id);

        $comment->delete();

        return response()->json([
            'message' => 'Komentar berhasil dihapus'
        ], 200);
    }
}
