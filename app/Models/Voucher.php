<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'uuid',
        'seller_id',
        'code',
        'name',
        'voucher_type',
        'voucher_cashback_type',
        'is_public',
        'discount_cashback_value',
        'discount_cashback_max',
        'min_transaction',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'discount_cashback_value' => 'float',
        'discount_cashback_max' => 'float',
        'min_transaction' => 'float',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // public function orders()
    // {
    //     return $this->hasMany(Order::class);
    // }

    public function scopeActive($query)
    {
        $today = now()->toDateString();
        return $query->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function getApiResponseSellerAttribute()
    {
        return [
            'uuid' => $this->uuid,
            'code' => $this->code,
            'name' => $this->name,
            'used_count' => $this->used_count,
            'is_public' => $this->is_public,
            'voucher_type' => $this->voucher_type,
            'discount_cashback_type' => $this->voucher_cashback_type,
            'discount_cashback_value' => $this->discount_cashback_value,
            'discount_cashback_max' => $this->discount_cashback_max,
            'min_transaction' => $this->min_transaction,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'seller' => $this->seller ? $this->seller->api_response_as_seller : null,
        ];
    }

    public function getApiResponseAttribute()
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'seller_id' => $this->seller_id,
            'code' => $this->code,
            'name' => $this->name,
            'voucher_type' => $this->voucher_type,
            'voucher_cashback_type' => $this->voucher_cashback_type,
            'is_public' => $this->is_public,
            'discount_cashback_value' => $this->discount_cashback_value,
            'discount_cashback_max' => $this->discount_cashback_max,
            'min_transaction' => $this->min_transaction,
            'start_date' => $this->start_date->format('Y-m-d H:i:s'),
            'end_date' => $this->end_date->format('Y-m-d H:i:s'),
            'seller' => $this->seller ? $this->seller->api_response_as_seller : null,
        ];
    }
}
