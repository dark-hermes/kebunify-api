<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id', 
        'likes',
    ];

    public function writer()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'forum_tag', 'forum_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'forum_id');
    }
}

