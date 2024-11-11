<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'specialization',
        'consultation_price',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function specialization() {
        return $this->belongsTo(ExpertSpecialization::class);
    }

}
