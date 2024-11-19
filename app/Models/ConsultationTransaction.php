<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationTransaction extends Model
{
    protected $fillable = [
        'consultation_id',
        'payment_date',
        'payment_receipt',
        'snap_token',
        'amount',
    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
