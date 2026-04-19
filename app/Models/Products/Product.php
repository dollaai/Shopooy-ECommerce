<?php

namespace App\Models\Products;

use App\Models\Category;
use App\Models\Products\Image;
use App\Models\Products\Review;
use App\Models\Products\Variation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'uuid',
        'seller_id',
        'slug',
        'name',
        'price',
        'price_sale',
        'stock',
        'category_id',
        'description',
        'weight',
        'length',
        'width',
        'height',
        'video',
    ];

    // casting secara otomatis merubah tipe data model saat diambil dari database yang telah ditentukan 
    protected $casts = [
        'price' => 'float',
        'price_sale' => 'float',
        'stock' => 'integer',
        'weight' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function variations()
    {
        return $this->hasMany(Variation::class);
    }
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getRatingAttribute()
    {
        return round($this->reviews->avg('start_seller'),2);
    }
    public function getRatingCountAttribute()
    {
        return (float) $this->reviews->count();
    }
    public function getPriceDiscountPercentAttribute()
    {
        if (is_null($this->price_sale)) return null;
        return (float) round(($this->price - $this->price_sale) / $this->price * 100, 2);
    }
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('d F Y');
    }
    public function getVideoUrlAttribute()
    {
        return $this->video ? asset('storage/' . $this->video) : null;
    }
    public function getSalesCountAttribute()
    {
        return 0;
    }
    public function getApiResponseExcerptAttribute()
    {
        return [
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => $this->price,
            'price_sale' => $this->price_sale ?: null,
            'price_discount_percentage' => $this->getPriceDiscountPercentAttribute(),
            'sale_count' => $this->sales_count,
            'image_url' => $this->images->first()->image_url,
            'stock' => $this->stock,
        ];
    }

    public function getApiResponseAttribute()
    {
        return [
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => $this->price,
            'price_sale' => $this->price_sale ?: null,
            'price_discount_percentage' => $this->getPriceDiscountPercentAttribute(),
            'sale_count' => $this->getSalesCountAttribute(),
            'category' => $this->category?->getApiResponseWithParentAttribute(),
            'stock' => $this->stock,
            'description' => $this->description,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'video' => $this->video,
            'seller' => $this->seller?->getApiResponseAsSellerAttribute(),
            'images' => $this->images->map(fn ($image) => $image->image_url),
            'variations' => $this->variations->map(fn ($variation) => $variation->getApiResponseAttribute()),
            'reviews' => [
                '5' => $this->reviews->where('star_seller', 5)->count(),
                '4' => $this->reviews->where('star_seller', 4)->count(),
                '3' => $this->reviews->where('star_seller', 3)->count(),
                '2' => $this->reviews->where('star_seller', 2)->count(),
                '1' => $this->reviews->where('star_seller', 1)->count(),
                'with_attachment' => $this->reviews->whereNotNull('attachments')->count(),
                'with_description' => $this->reviews->whereNotNull('description')->count(),
            ],
             'other_product' => $this->seller->products()->where('id', '!=', $this->id)->limit(6)->get()->map(function ($product) {
                return $product->getApiResponseExcerptAttribute();
            }),
        ];
    }

    public function getApiResponseSellerAttribute()
    {
        return [
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'name' => $this->name,
            'price' => $this->price,
            'price_sale' => $this->price_sale ?: null,
            'price_discount_percentage' => $this->getPriceDiscountPercentAttribute(),
            'sale_count' => $this->getSalesCountAttribute(),
            'stock' => $this->stock,
            'category' => $this->category?->getApiResponseWithParentAttribute(),
            'description' => $this->description,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'video' => $this->video,
            'images' => $this->images->map(fn ($image) => $image->image_url),
            'variations' => $this->variations->map(fn ($variation) => $variation->getApiResponseAttribute()),
        ];
    }
}
