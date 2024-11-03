<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;

class ArticleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(PermissionMiddleware::using('create_article,sanctum'), only: ['store']),
            new Middleware(PermissionMiddleware::using('update_article,sanctum'), only: ['update']),
            new Middleware(PermissionMiddleware::using('delete_article,sanctum'), only: ['destroy']),
        ];
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $paginate = $request->query('paginate');


        try {
            $articles = Article::query()
                ->when($search, function ($query, $search) {
                    return $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%');
                });

            $articles = ! $paginate
                ? $articles->get()
                : $articles->paginate($paginate);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $articles,
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_published' => 'required|boolean',
            'is_premium' => 'required|boolean',
            'tags' => 'required|array',
        ]);

        try {
            $article = null;
            DB::transaction(function () use ($request, &$article) {
                $article = Article::create([
                    'expert_id' => Auth::user()->expert->id,
                    'title' => $request->title,
                    'content' => $request->content,
                    'image' => $request->image ? $request->image->store('articles') : null,
                    'is_published' => $request->is_published,
                    'is_premium' => $request->is_premium,
                ]);
                $article->tags()->attach($request->tags);
            });

            return response()->json([
                'message' => __('http-statuses.201'),
                'data' => $article,
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
            $article = Article::with('tags')->find($id);

            if (! $article) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $article,
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
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_published' => 'required|boolean',
            'is_premium' => 'required|boolean',
            'tags' => 'required|array',
        ]);

        try {
            $article = Article::find($id);

            if (! $article) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            DB::transaction(function () use ($request, $article) {
                $article->update([
                    'title' => $request->title,
                    'content' => $request->content,
                    'image' => $request->image ? $request->image->store('articles') : $article->image,
                    'is_published' => $request->is_published,
                    'is_premium' => $request->is_premium,
                ]);
                $article->tags()->sync($request->tags);
            });

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $article,
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
            $article = Article::find($id);

            if (! $article) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $article->delete();

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
