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
        'status',
    ];

    protected $appends = [
        'status_label',
    ];

    public function getStatusLabelAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return 'Menunggu Pembayaran';
            case 'success':
                return 'Pembayaran Berhasil';
            case 'failed':
                return 'Pembayaran Gagal';
            default:
                return 'Menunggu Pembayaran';
        }
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }
}
