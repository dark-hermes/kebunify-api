<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'message',
        'is_read',
        'user_id',
        'consultation_id',
    ];

    protected $appends = ['is_me'];

    protected $with = ['user'];


    public function getIsMeAttribute()
    {
        return $this->user_id === Auth::id();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
