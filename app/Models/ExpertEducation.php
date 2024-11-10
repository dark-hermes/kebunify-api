<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertEducation extends Model
{
    protected $fillable = [
        'expert_id',
        'degree',
        'institution',
        'graduation_year',
        'field_of_study'
    ];

    public function expert()
    {
        return $this->belongsTo(Expert::class);
    }
}
