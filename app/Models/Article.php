<?php

namespace App\Models;

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
}
