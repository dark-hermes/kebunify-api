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
        'user_id',
        'expert_id',
        'status'
    ];

    protected $with = ['user', 'expert', 'transaction', 'rating'];

    protected $appends = [
        'status_label',
    ];

    public function getStatusLabelAttribute()
    {
        return $this->status === 'closed' ? 'Selesai' : 'Belum Selesai';
    }

    public function getIsPaidAttribute()
    {
        // return $this->transaction->payment_date !== null;
        // return $this->transaction->status === 'success';
        return true;
    }

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

    public function transaction()
    {
        return $this->hasOne(ConsultationTransaction::class);
    }

    public function rating()
    {
        return $this->hasOne(ExpertRating::class);
    }
}
