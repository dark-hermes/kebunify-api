<?php

namespace App\Http\Controllers;

use App\Http\Resources\ForumDetailResource;
use App\Http\Resources\ForumListResource;
use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    //
    public function index()
    {
        $forums = Forum::all();
        return ForumListResource::collection($forums);
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
            'author' => 'required|exists:users,id'
        ]);

        $forum = Forum::create([
            'title' => $validated['title'],
            'author' => $validated['author']
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
            'author' => 'sometimes|exists:users,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id'
        ]);

        $forum = Forum::findOrFail($id);

        if (isset($validated['title'])) {
            $forum->title = $validated['title'];
        }

        if (isset($validated['author'])) {
            $forum->author = $validated['author'];
        }

        $forum->save();

        if (isset($validated['tags'])) {
            $forum->tags()->sync($validated['tags']);
        }

        return new ForumDetailResource($forum->load('writer', 'tags', 'comments'));
    }

    public function destroy($id)
    {
        $forum = Forum::findOrFail($id);

        $forum->delete();

        return response()->json([
            'message' => 'Forum berhasil dihapus'
        ], 200);
    }
}
