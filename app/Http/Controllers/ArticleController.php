<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
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
        // //
        // $query = Article::with('expert');

        // if ($request->has('expert_id')) {
        //     $query->where('expert_id', $request->input('expert_id'));}

        // if($request -> has('search')) {
        //     $search = $request->input('search');
        //     $query->where('title','LIKE',"%{$search}%")
        //         ->orWhere('tags','LIKE',"%{$search}%")
        //         ->orWhere('content', 'LIKE',"%{$search}%");
        // }

        // $articles = $query->get();

        // $articles->transform(function ($article) {
        //     if ($article->picture) {
        //         $article->picture_url = asset('images/articles/' . $article->picture);
        //     } else {
        //         $article->picture_url = null;
        //     }


        //     $article->tags = json_decode($article->tags, true);

        //     return $article;
        // });

        // return response()->json(['message' => 'Articles fetched successfully',
        // 'data' => $articles],200);
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {

        $search = $request->query('search');
        $limit = $request->query('limit') ?? 9;


        try {
            $articles = Article::query()
                ->when($search, function ($query, $search) {
                    return $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%');
                });

            $articles = ! $limit
                ? $articles->get()
                : $articles->paginate($limit);

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

    public function list(Request $request)
    {
        $search = $request->query('search');

        try {
            $articles = Article::query()
                ->when($search, function ($query, $search) {
                    return $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%');
                })->get();

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

    public function getArticlesByExpert(Request $request, $id) {

        $search = $request->query('search');
        $paginate = $request->query('paginate');// Mendapatkan ID expert yang sedang login

        try {
            $articles = Article::query()
                ->where('expert_id', $id) // Menambahkan filter untuk expert yang sedang login
                ->when($search, function ($query, $search) {
                    return $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%');
                    });
                });

            $articles = !$paginate
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
        // $request->validate([
        //     'title' => 'required|string',
        //     'picture' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        //     // 'picture' => 'nullable|string',
        //     'content' => 'required|string',
        //     'is_premium' => 'required|integer',
        //     'expert_id' => 'required|integer',
        //     'tags' => 'nullable ',
        // ]);

        // $fileName = null;

        // if ($request->hasFile('picture')) {
        //     $file = $request->file('picture');
        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $file->move(public_path('images/articles'), $fileName);
        // }

        // $article = Article::create([
        //     'title' => $request->title,
        //     'picture' => $fileName,
        //     // 'picture' => $request->picture,
        //     'content' => $request->content,
        //     'is_premium' => $request->is_premium,
        //     'expert_id' => $request->expert_id,
        //     'tags' =>json_encode($request->tags),


        // ]);
        // return response()->json(['message' =>'Article created successfully'], 200);

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
                        'is_published' => $request->is_published,
                        'is_premium' => $request->is_premium,
                    ]);

                    if ($request->hasFile('image')) {
                        $image = $request->file('image');
                        $imageName = $article->id . '_image' . time() . '.' . $image->getClientOriginalExtension();
                        $image->storeAs('articles', $imageName, 'public');

                        $article->update([
                            'image' => 'storage/articles/' . $imageName,
                        ]);
                    }

                    foreach ($request->tags as $tag) {
                        $tag = Tag::firstOrCreate(['name' => $tag]);
                        $article->tags()->attach($tag->id);
                    }

                    // foreach (json_decode($request->tags, true) as $tag) {
                    //     if (!is_array($request->tags)) {
                    //         return response()->json(['message' => 'Tags must be an array'], 422);
                    //     }
                    //     $tag = Tag::firstOrCreate(['name' => $tag]);
                    //     $article->tags()->attach($tag->id);
                    // }
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
        // $article = Article::findOrFail($id);


        // if ($article->picture) {
        //     $article->picture_url = asset('images/articles/' . $article->picture);
        // } else {
        //     $article->picture_url = null;
        // }

        // $article->tags = json_decode($article->tags, true);

        // return response()->json([
        //     'message' => 'Article fetched successfully',
        //     'data' => $article
        // ], 200);
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(string $id)
    // {
        try {
            $article = Article::with('tags', 'expert')->find($id);

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
            // // Validate incoming data
            // $request->validate([
            //     'title' => 'required',
            //     'content' => 'required',
            //     'is_premium' => 'required',
            //     'expert_id' => 'required',
            //     'tags' => 'nullable', // No validation for tags array, but you can add rules if needed
            // ]);

            // try {

            //     $article = Article::findOrFail($id);

            //     //  Initialize variable for file name
            //     $fileName = $article->picture; // Default to the existing picture name

            //     // If a new picture is uploaded, handle the file
            //     if ($request->hasFile('picture')) {
            //         //Delete the old picture if it exists
            //         if ($article->picture && file_exists(public_path('images/articles/' . $article->picture))) {
            //             @unlink(public_path('images/articles/' . $article->picture)); // Delete the old file
            //         }

            //         // Store the new picture
            //         $file = $request->file('picture');
            //         $fileName = time() . '_' . $file->getClientOriginalName();
            //         $file->move(public_path('images/articles'), $fileName);

            //         // Update the picture field with the new file name
            //         $article->picture = $fileName;
            //     }

            //     //Update the article fields with the new data
            //     $article->title = $request->title;
            //     $article->content = $request->content;
            //     $article->is_premium = $request->is_premium;
            //     $article->expert_id = $request->expert_id;
            //     $article->tags = $request->tags ? json_encode($request->tags) : json_encode([]); // Convert tags to JSON if it's an array

            //     //Save the updated article
            //     $article->save();

            //     // $article->update([
            //     //     'title' => $request->title,
            //     //     'picture'=> $fileName,
            //     //     'content' => $request->content,
            //     //     'is_premium' => $request->is_premium,
            //     //     'expert_id' => $request->expert_id,
            //     //     'tags' => json_encode($request->tags),
            //     // ]);

            //     // Return success response
            //     return response()->json([
            //         'message' => 'Article updated successfully',
            //         'article' => $article, // Include the updated article in the response
            //     ], 200);
            // } catch (\Throwable $th) {
            //     return response()->json([
            //         'message' => __('http-statuses.500'),
            //         'error' => config('app.debug') ? $th->getMessage() : null,
            //     ], 500);
            // }

        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'is_published' => 'required|integer',
            'is_premium' => 'required|integer',
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
                    'is_published' => $request->is_published,
                    'is_premium' => $request->is_premium,
                ]);

                foreach ($request->tags as $tag) {
                    $tag = Tag::firstOrCreate(['name' => $tag]);
                    $tags[] = $tag->id;
                }

                $article->tags()->sync($tags);
            });

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $article->refresh(),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }



    public function uploadImage(Request $request, string $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $article = Article::find($id);

            if (! $article) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $image = $request->file('image');
            $imageName = $article->id . '_image' . time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('articles', $imageName, 'public');

            $article->update([
                'image' => 'storage/articles/' . $imageName,
            ]);

            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $article,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() :    null,
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        // $article = Article::findOrFail($id);
        // $file = public_path('images/articles').$article->picture;
        // if (file_exists(public_path('images/articles/' . $article->picture))) {
        //     @unlink(public_path('images/articles/' . $article->picture)); // Delete the old file
        // }
        // $article->delete();

        // return response()->json(['message' =>'Article deleted successfully']);

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

    public function deleteImage(string $id)
    {
        //
        // $article = Article::findOrFail($id);
        // $file = public_path('images/articles').$article->picture;
        // if (file_exists(public_path('images/articles/' . $article->picture))) {
        //     @unlink(public_path('images/articles/' . $article->picture)); // Delete the old file
        // }
        // $article->delete();

        // return response()->json(['message' =>'Article deleted successfully']);

        try {
            $article = Article::find($id);

            if (! $article) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $file = public_path('articles').$article->image;
            if (file_exists(public_path('articles' . $article->image))) {
                @unlink(public_path('articles' . $article->image)); // Delete the old file
            }

            $article->update([
                'image' => null,
            ]);

            return response()->json([
                'message' => __('responses.remove.success', ['resource' => __('resources.avatar')]),
                'data' => $article,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('responses.remove.failed', ['resource' => __('resources.avatar')]),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }

    }

    public function getTags()
    {
        try {
            $tags = Tag::all(); // Ambil semua tag dari database
            return response()->json([
                'message' => __('http-statuses.200'),
                'data' => $tags,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => __('http-statuses.500'),
                'error' => config('app.debug') ? $th->getMessage() : null,
            ], 500);
        }
    }

    public function publish($id)
    {
        try {
            $article = Article::findOrFail($id);

            if (!$article) {
                return response()->json([
                    'message' => __('http-statuses.404'),
                ], 404);
            }

            $article->update([
                'is_published' => ! $article->is_published,
            ]);

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

}
