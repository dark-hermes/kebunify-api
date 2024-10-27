<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class consultation extends Model
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;
    protected $fillable = ['topic', 
                            'status', 
                            'description',
                            'content_status_payment',
                            'user_id'];
    public function user() {
        return $this->belongsTo(User::class);
    }
}
