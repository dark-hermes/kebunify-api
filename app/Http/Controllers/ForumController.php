<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ForumController extends Controller
{

    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);
            $search = $request->query('search');

            $forums = Forum::query()
                ->when($search, function ($query, $search) {
                    $query->where('title', 'like', '%' . $search . '%');
                })
                ->withCount('comments')
                ->with('writer:id,name,avatar')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'message' => 'OK',
                'data' => $forums->items(),
                'total' => $forums->total(),
                'current_page' => $forums->currentPage(),
                'per_page' => $forums->perPage(),
                'last_page' => $forums->lastPage(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function getUserForums(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);

            $userForums = Forum::where('user_id', Auth::id())
                ->withCount('comments')
                ->with('writer:id,name,avatar')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'message' => 'OK',
                'data' => $userForums->items(),
                'total' => $userForums->total(),
                'current_page' => $userForums->currentPage(),
                'per_page' => $userForums->perPage(),
                'last_page' => $userForums->lastPage(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function listPopular(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);

            $forums = Forum::orderBy('likes', 'desc')
                ->with('tags:id,name', 'writer:id,name')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'message' => 'OK',
                'data' => $forums->items(),
                'total' => $forums->total(),
                'current_page' => $forums->currentPage(),
                'per_page' => $forums->perPage(),
                'last_page' => $forums->lastPage(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function listLatest(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);

            $forums = Forum::orderBy('created_at', 'desc')
                ->with('tags:id,name', 'writer:id,name')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'message' => 'OK',
                'data' => $forums->items(),
                'total' => $forums->total(),
                'current_page' => $forums->currentPage(),
                'per_page' => $forums->perPage(),
                'last_page' => $forums->lastPage(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function filterByTag(Request $request, $tagId)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $page = $request->query('page', 1);

            $forums = Forum::whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            })
                ->with('tags:id,name', 'writer:id,name')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'message' => 'OK',
                'data' => $forums->items(),
                'total' => $forums->total(),
                'current_page' => $forums->currentPage(),
                'per_page' => $forums->perPage(),
                'last_page' => $forums->lastPage(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $forum = Forum::with('tags:id,name', 'writer:id,name', 'comments.author:id,name')
                ->findOrFail($id);

            return response()->json([
                'message' => 'OK',
                'data' => $forum,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Not Found.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 404);
        }
    }

    public function like($id)
    {
        try {
            $forum = Forum::findOrFail($id);

            if (!Auth::check()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $forum->increment('likes');

            return response()->json([
                'message' => 'Forum successfully liked.',
                'data' => [
                    'id' => $forum->id,
                    'likes' => $forum->likes,
                ],
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            $forum = Forum::create([
                'title' => $request->title,
                'user_id' => Auth::id(),
            ]);

            if ($request->has('tags')) {
                $forum->tags()->attach($request->tags);
            }

            return response()->json([
                'message' => 'Forum created successfully.',
                'data' => $forum->load('tags:id,name'),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        try {
            $forum = Forum::findOrFail($id);

            if ($forum->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            if ($request->has('title')) {
                $forum->title = $request->title;
            }

            if ($request->has('tags')) {
                $forum->tags()->sync($request->tags);
            }

            $forum->save();

            return response()->json([
                'message' => 'Forum updated successfully.',
                'data' => $forum->load('tags:id,name'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $forum = Forum::findOrFail($id);


            if ($forum->user_id !== Auth::id()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $forum->delete(); // Hapus forum

            return response()->json([
                'message' => 'Forum deleted successfully.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Internal Server Error.',
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }
}
