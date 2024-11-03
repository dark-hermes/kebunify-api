<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_specialization_id',
        'user_id',
        'start_year',
        'consulting_fee',
        'discount',
        'bio',
        'is_active',
    ];

    protected $appends = [
        'fee_after_discount',
        'years_of_experience',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->user->syncRoles('expert');
        });

        static::updated(function ($model) {
            $oldUser = $model->getOriginal('user_id');
            $newUser = $model->user_id;

            if ($oldUser !== $newUser) {
                User::find($oldUser)->syncRoles('user');
                User::find($newUser)->syncRoles('expert');
            }
        });

        static::deleted(function ($model) {
            $model->user->syncRoles('user');
        });
    }

    public function getFeeAfterDiscountAttribute()
    {
        return $this->consulting_fee - ($this->consulting_fee * $this->discount / 100);
    }

    public function getYearsOfExperienceAttribute()
    {
        return now()->year - $this->start_year;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialization()
    {
        return $this->belongsTo(ExpertSpecialization::class, 'expert_specialization_id');
    }
}
