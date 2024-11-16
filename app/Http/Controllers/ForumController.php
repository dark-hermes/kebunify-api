<?php

namespace App\Http\Controllers;

use App\Http\Resources\ForumCommentResource;
use App\Http\Resources\ForumDetailResource;
use App\Http\Resources\ForumListResource;
use App\Models\Forum;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class ForumController extends Controller
{

    public function index($forumId)
    {
        $comments = ForumComment::where('forum_id', $forumId)
            ->whereNull('parent_id') 
            ->with(['author:id,name', 'replies.author:id,name']) 
            ->get();

        return ForumCommentResource::collection($comments);
    }


    public function show($id)
    {
        $forum = Forum::with('writer:id,name')->findOrFail($id);
        return new ForumDetailResource($forum);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $forum = Forum::create([
            'title' => $validated['title'],
            'author' => $request->user()->id,
        ]);

        if (!empty($validated['tags'])) {
            $forum->tags()->attach($validated['tags']);
        }

        return new ForumDetailResource($forum->load('writer:id,name', 'tags', 'comments'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $forum = Forum::findOrFail($id);

        if ($forum->author !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (isset($validated['title'])) {
            $forum->title = $validated['title'];
        }

        $forum->save();

        if (isset($validated['tags'])) {
            $forum->tags()->sync($validated['tags']);
        }

        return new ForumDetailResource($forum->load('writer', 'tags', 'comments'));
    }

    public function destroy(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);

        if ($forum->author !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $forum->delete();

        return response()->json([
            'message' => 'Forum berhasil dihapus'
        ], 200);
    }

    public function like($id)
    {
        $forum = Forum::findOrFail($id);
        $forum->increment('likes');

        return response()->json([
            'message' => 'Forum berhasil kamu sukai',
            'likes' => $forum->likes,
        ]);
    }

    public function home()
    {
        $latestForum = Forum::orderBy('created_at', 'desc')
            ->with(['writer:id,name', 'tags:id,name'])
            ->first();

        $popularForum = Forum::orderBy('likes', 'desc')
            ->with(['writer:id,name', 'tags:id,name'])
            ->first();

        return response()->json([
            'popular' => $popularForum ? new ForumListResource($popularForum) : null,
            'latest' => $latestForum ? new ForumListResource($latestForum) : null,
        ]);
    }
}
