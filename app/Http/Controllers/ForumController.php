<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    //
    public function index()
    {
        $forum = Forum::all();
        return response()->json($forum);
    }
}
