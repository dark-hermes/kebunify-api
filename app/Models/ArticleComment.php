<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'parent_id',
        'content',
    ];

    protected $appends = [
        'readable_created_at'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ArticleComment::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ArticleComment::class, 'parent_id');
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

}