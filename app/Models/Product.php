<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $with = ['category', 'reviews.user', 'seller.user'];

    protected $appends = ['seller_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'user_id');
    }

    public function getSellerIdAttribute()
    {
        return $this->attributes['user_id'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}