<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'product_id',
        'image',
    ];

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
