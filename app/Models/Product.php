<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Models\User;
use App\Models\Reviews;
use App\Models\Category;
use App\Models\Seller;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'category_id',
        'stock',
        'total_sales',
        'image_url',
        'user_id',
        'review_id',
    ];

    protected $with = ['category', 'reviews', 'user'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessor to rename `user_id` to `seller_id` in JSON response
    protected $appends = ['seller_id'];

    public function getSellerIdAttribute()
    {
        return $this->attributes['user_id'];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}