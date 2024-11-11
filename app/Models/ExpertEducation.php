<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpertEducation extends Model
{
    use HasFactory;

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
