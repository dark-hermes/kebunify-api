<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Consultation extends Model
{
    use HasFactory;
    protected $fillable = [
        'topic',
        'status',
        'description',
        'content_status_payment',
        'user_id',
        'expert_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
