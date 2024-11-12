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

    protected $appends = [
        'total_experts',
        'total_active_experts',
    ];

    public function experts(): HasMany
    {
        return $this->hasMany(Expert::class);
    }

    public function getTotalExpertsAttribute(): int
    {
        return $this->experts()->count();
    }

    public function getTotalActiveExpertsAttribute(): int
    {
        return $this->experts()->where('is_active', true)->count();
    }
}
