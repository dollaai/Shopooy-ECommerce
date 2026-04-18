<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'variant',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function getApiResponseAttribute()
    {
        return [
            'name' => $this->name,
            'variant' => json_decode($this->variant),
        ];
    }

    public function setVariantAttribute($value)
    {
        $this->attributes['variant'] = json_encode($value);
    } 

}
