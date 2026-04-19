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
        if (str_starts_with($this->image, 'storage/')) {
            return asset('storage/' . $this->image);
        } else {
            return asset($this->image);
        }
        if (!$this->image) return null;
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
