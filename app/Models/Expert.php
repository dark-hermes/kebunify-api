<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expert extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $with = ['user', 'specialization'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->user->assignRole('expert');
        });

        static::deleted(function ($model) {
            $model->user->removeRole('expert');
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

    public function educations()
    {
        return $this->hasMany(ExpertEducation::class)->orderBy('graduation_year', 'desc');
    }

    public function experiences()
    {
        return $this->hasMany(ExpertExperience::class)->orderBy('start_year', 'desc');
    }
}
