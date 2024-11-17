<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ForumTag extends Pivot
{
    use HasFactory;

    protected $fillable = ['forum_id', 'tag_id'];
}
