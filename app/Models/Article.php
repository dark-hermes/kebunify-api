<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'title',
        'picture',
        'content',
        'expert_id',
        'is_premium',
        'tags',
    ];

    public function expert(){
        return $this->belongsTo(Expert::class);
    }
=======
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'image_url'
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

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            } else {
                return asset($this->image);
            }
        } else {
            return asset('images/placeholders/image.webp');
        }
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }
>>>>>>> cec929c67adc22ebf556adb4f9a6fc6f274d6070
}
