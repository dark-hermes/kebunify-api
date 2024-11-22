<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;


class Seller extends Model
{
    use HasFactory;

    protected $table = 'sellers';
    protected $fillable = [
        'store_name',
        'address',
        'avatar',
        'status',
        'user_id',
        'location',
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}

