<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertExperience extends Model
{
    protected $fillable = [
        'expert_id',
        'position',
        'company',
        'start_year',
        'end_year'
    ];

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }

    public function getDurationAttribute()
    {
        return $this->start_year . ' - ' . ($this->end_year ?? 'Sekarang');
    }
}
