<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Forum extends Model
{
    use HasFactory;

    protected $table = 'forum';

    protected $fillable = [
        'title',
        'author',
        'likes',
    ];


    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->translatedFormat('j F');
    }

    public function writer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'forum_tag');
    }


    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'forum_id');
    }
}
