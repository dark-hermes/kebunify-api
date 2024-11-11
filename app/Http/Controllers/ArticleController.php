<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $query = Article::with('expert');

        if ($request->has('expert_id')) {
            $query->where('expert_id', $request->input('expert_id'));}

        if($request -> has('search')) {
            $searh = $request->input('search');
            $query->where('title','LIKE',"%{$searh}%")
                ->orWhere('tags','LIKE',"%{$searh}%")
                ->orWhere('content', 'LIKE',"%{$searh}%");
        }

        $articles = $query->get();

        // Modifikasi hasil untuk menambahkan URL gambar lengkap dan konversi tags ke array
        $articles->transform(function ($article) {
            // Tambahkan URL lengkap untuk gambar
            if ($article->picture) {
                $article->picture_url = asset('images/articles/' . $article->picture);
            } else {
                $article->picture_url = null;
            }

            // Ubah tags ke array jika disimpan sebagai JSON
            $article->tags = json_decode($article->tags, true);

            return $article;
        });

        return response()->json(['message' => 'Articles fetched successfully',
        'data' => $articles],200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $request->validate([
            'title' => 'required|string',
            'picture' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // 'picture' => 'nullable|string',
            'content' => 'required|string',
            'is_premium' => 'required|integer',
            'expert_id' => 'required|integer',
            'tags' => 'nullable ',
        ]);

        $fileName = null;

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images/articles'), $fileName);
        }

        $article = Article::create([
            'title' => $request->title,
            'picture' => $fileName,
            // 'picture' => $request->picture,
            'content' => $request->content,
            'is_premium' => $request->is_premium,
            'expert_id' => $request->expert_id,
            'tags' =>json_encode($request->tags),


        ]);

        return response()->json(['message' =>'Article created successfully'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $article = Article::findOrFail($id);

        // Tambahkan URL gambar lengkap
        if ($article->picture) {
            $article->picture_url = asset('images/articles/' . $article->picture);
        } else {
            $article->picture_url = null;
        }

        // Ubah tags ke array jika disimpan sebagai JSON
        $article->tags = json_decode($article->tags, true);

        return response()->json([
            'message' => 'Article fetched successfully',
            'data' => $article
        ], 200);
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
            // Validate incoming data
            // $request->validate([
            //     'title' => 'required',
            //     'content' => 'required',
            //     'is_premium' => 'required',
            //     'expert_id' => 'required',
            //     'tags' => 'nullable', // No validation for tags array, but you can add rules if needed
            // ]);

            try {

                $article = Article::findOrFail($id);

                //  Initialize variable for file name
                $fileName = $article->picture; // Default to the existing picture name

                // If a new picture is uploaded, handle the file
                if ($request->hasFile('picture')) {
                    //Delete the old picture if it exists
                    if ($article->picture && file_exists(public_path('images/articles/' . $article->picture))) {
                        @unlink(public_path('images/articles/' . $article->picture)); // Delete the old file
                    }

                    // Store the new picture
                    $file = $request->file('picture');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('images/articles'), $fileName);

                    // Update the picture field with the new file name
                    $article->picture = $fileName;
                }

                //Update the article fields with the new data
                $article->title = $request->title;
                $article->content = $request->content;
                $article->is_premium = $request->is_premium;
                $article->expert_id = $request->expert_id;
                $article->tags = $request->tags ? json_encode($request->tags) : json_encode([]); // Convert tags to JSON if it's an array

                //Save the updated article
                $article->save();

                // $article->update([
                //     'title' => $request->title,
                //     'picture'=> $fileName,
                //     'content' => $request->content,
                //     'is_premium' => $request->is_premium,
                //     'expert_id' => $request->expert_id,
                //     'tags' => json_encode($request->tags),
                // ]);

                // Return success response
                return response()->json([
                    'message' => 'Article updated successfully',
                    'article' => $article, // Include the updated article in the response
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'message' => __('http-statuses.500'),
                    'error' => config('app.debug') ? $th->getMessage() : null,
                ], 500);
            }
    }

    public function ubah(Request $request, string $id)
    {
        $ubah = Article::findOrFail($id);
        if ($image = $request->file('picture')) {
            $destinationPath = 'images/articles/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $dt['picture'] = $profileImage;
        }

        $dt = [
            'title' => $request['title'],
            'content' => $request['content'],
            'expert_id' => $request['expert_id'],
            'is_premium' => $request['is_premium'],
            'tags' => $request['tags'],

        ];
        $ubah->update($dt);
        return response()->json([
            'message' => 'Article updated successfully',
            'article' => $ubah, // Include the updated article in the response
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $article = Article::findOrFail($id);
        $file = public_path('images/articles').$article->picture;
        if (file_exists(public_path('images/articles/' . $article->picture))) {
            @unlink(public_path('images/articles/' . $article->picture)); // Delete the old file
        }
        $article->delete();

        return response()->json(['message' =>'Article deleted successfully']);
    }
}