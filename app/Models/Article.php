<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_id',
        'title',
        'content',
        'image',
        'is_published',
        'is_premium',
    ];

    protected $appends = [
        'readable_created_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->tags()->detach();
        });
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }
}
