<?php

namespace App\Models\Products;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        // 'order_item_id',
        'product_id',
        'user_id',
        'star_seller',
        'star_courier',
        'variations',
        'description',
        'attachments',
        'show_username',
    ];

    protected $casts = [
        'star_seller' => 'integer',
        'star_courier' => 'integer',
        'attachments' => 'array',
        'show_username' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function getAttachmentsAttribute($value)
    {
        $attachments = json_decode($value, true);
        $attachments = array_map(function ($attachment) {
            return asset('storage/' . $attachment);
        }, $attachments);
        return $attachments;
    }

    public function getApiResponseAttribute()
    {
        return [
            'id' => $this->id,

            'star_seller' => $this->star_seller,
            'star_courier' => $this->star_courier,
            'variations' => $this->variations,
            'description' => $this->description,
            'attachments' => $this->attachments,
            'show_username' => $this->show_username,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'user_name' => $this->show_username ? $this->user->name : substr($this->user->name, 0, 1) . str_repeat('*', strlen($this->user->name) - 2) . substr($this->user->name, -1),
            'user_photo' => $this->show_username ? $this->user->photo : null,
        ];
    }
    
    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = json_encode($value);
    }
}
