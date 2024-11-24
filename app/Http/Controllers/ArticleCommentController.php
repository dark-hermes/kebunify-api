<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Models\ArticleComment;
use Illuminate\Support\Facades\Auth;

class ArticleCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $article_id)
    {
        try {
            $article = Article::find($article_id);

            if (!$article) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $comments = $article->comments()
                // ->whereNull('parent_id')
                ->with('user', 'children.user')
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $id, Request $request)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:article_comments,id',
            'content' => 'required|string',
        ]);

        try {
            $articleComment = ArticleComment::create([
                'article_id' => $id,
                'user_id' => Auth::id(),
                'parent_id' => $request->input('parent_id'),
                'content' => $request->input('content'),
            ]);
            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $articleComment->refresh(),
            ]);
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try{
        $comment = ArticleComment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Comment not found'
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Failed to delete comment',
            'error' => $e->getMessage()
        ], 500);
    }

    }
}