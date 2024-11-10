<?php

namespace App\Models;

use App\Models\Expert;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExpertSpecialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function experts(): HasMany
    {
        return $this->hasMany(Expert::class);
    }
}
