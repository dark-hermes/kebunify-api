<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertRating extends Model
{
    protected $fillable = [
        'user_id',
        'expert_id',
        'consultation_id',
        'rating',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expert()
    {
        return $this->belongsTo(User::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
