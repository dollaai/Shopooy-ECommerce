<?php

namespace App\Models\Cart;

use App\Models\Cart\Cart;
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_uuid',
        'variations',
        'quantity',
        'note',
    ];

    protected $casts = [
        'variations' => 'array',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_uuid', 'uuid');
    }
    public function recalculateTotal()
    {
        $subtotal = $this->items->sum('total');
        $this->total = $subtotal + $this->courier_price + $this->service_fee - $this->voucher_value;
        $this->total_payment = max(0, $this->total - $this->pay_with_coin);
        $this->save();
    }

    public function getVariationsAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setVariationsAttribute($value)
    {
        $this->attributes['variations'] = json_encode($value);
    }
    public function getTotalAttribute()
    {
        return ($this->product->price_sale ?? $this->product->price) * $this->quantity;
    }
    public function getApiResponseAttribute()
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product' => $this->product->getApiResponseExcerptAttribute(),
            'variations' => $this->variations,
            'quantity' => $this->quantity,
            'note' => $this->note,
            'total' => $this->total,
        ];
    }
}
