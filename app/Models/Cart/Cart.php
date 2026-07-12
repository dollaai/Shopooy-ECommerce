<?php

namespace App\Models\Cart;

use App\Models\Address;
use App\Models\Cart\CartItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'address_id',
        'courier',
        'courier_type',
        'courier_estimation',
        'courier_price',
        'voucher_id',
        'voucher_value',
        'voucher_cashback',
        'service_fee',
        'total',
        'pay_with_coin',
        'payment_method',
        'total_payment',
    ];

    protected $casts = [
        'courier_price' => 'float',
        'voucher_value' => 'float',
        'voucher_cashback' => 'float',
        'service_fee' => 'float',
        'total' => 'float',
        'pay_with_coin' => 'float',
        'total_payment' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function recalculateTotal()
    {
        $subtotal = $this->items->sum('total');
        $this->total = $subtotal + $this->courier_price + $this->service_fee - $this->voucher_value;
        $this->total_payment = max(0, $this->total - $this->pay_with_coin);
        $this->save();
    }
    public function getApiResponseAttribute()
    {
        $subtotal = $this->items->sum('total');
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'address_id' => $this->address_id,
            'courier' => $this->courier,
            'courier_type' => $this->courier_type,
            'courier_estimation' => $this->courier_estimation,
            'courier_price' => $this->courier_price,
            'subtotal' => $subtotal,
            'voucher_id' => null, // Assuming you want to return null for voucher_id in the API response,
            'voucher_value' => $this->voucher_value,
            'voucher_cashback' => $this->voucher_cashback,
            'service_fee' => $this->service_fee,
            'total' => $this->total,
            'pay_with_coin' => $this->pay_with_coin,
            'payment_method' => $this->payment_method,
            'total_payment' => $this->total_payment,
        ];
    }

   
}
