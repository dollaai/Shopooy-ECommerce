<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Products\Image;
use App\Models\Products\Product;
use App\Models\Products\Review;
use App\Models\Products\Variation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::Transaction(function () {
            for ($productCount = 1; $productCount <= 10; $productCount++) {
                $payload = [
                    'name' => 'Product ' . $productCount,
                    'slug' => 'product-' . $productCount,
                    'category_id' => Category::whereNotNull('parent_id')->inRandomOrder()->first()->id,
                    'seller_id' => User::inRandomOrder()->first()->id,
                    'price' => rand(10000, 100000),
                    'stock' => rand(1, 100),
                    'description' => 'Description for Product ' . $productCount,
                    'weight' => rand(100, 1000),
                    'length' => rand(10, 100),
                    'width' => rand(10, 100),
                    'height' => rand(10, 100),
                    'video' => 'dummy/product/product.mp4',
                    'images' =>     [
                        'dummy/product/1.png',
                        'dummy/product/2.png',
                        'dummy/product/3.png',
                        'dummy/product/4.png',
                        'dummy/product/5.png',
                        'dummy/product/6.png',
                        'dummy/product/7.png',
                        'dummy/product/8.png',
                        'dummy/product/9.png',
                        'dummy/product/10.png',

                    ],
                    'variations' => [
                        [
                            'name' => 'Warna',
                            'variant' => ['Red', 'Green', 'Blue'],
                        ],
                        [
                            'name' => 'Size',
                            'variant' => ['S', 'M', 'L'],
                        ],
                    ],
                    'reviews' => [
                        [
                            // 'product_id' =>,
                            'user_id' => User::inRandomOrder()->first()->id,
                            'star_seller' => rand(1, 5),
                            'star_courier' => rand(1, 5),
                            'variations' => 'Warna:Red,Size:M',
                            'description' => 'Review for Product ' . $productCount,
                            'attachments' => [
                                'dummy/product/1.png',
                                'dummy/product/2.png',
                                'dummy/product/3.png',
                                'dummy/product/4.png',
                                'dummy/product/5.png',
                                'dummy/product/6.png',
                                'dummy/product/7.png',
                                'dummy/product/8.png',
                                'dummy/product/9.png',
                                'dummy/product/10.png',
                            ],
                            'show_username' => (bool) rand(0, 1),
                        ],
                        [
                            // 'product_id' =>,
                            'user_id' => User::inRandomOrder()->first()->id,
                            'star_seller' => rand(1, 5),
                            'star_courier' => rand(1, 5),
                            'variations' => 'Warna:Green,Size:L',
                            'description' => 'Review for Product ' . $productCount,
                            'attachments' => [
                                'dummy/product/1.png',
                                'dummy/product/2.png',
                                'dummy/product/3.png',
                                'dummy/product/4.png',
                                'dummy/product/5.png',
                                'dummy/product/6.png',
                                'dummy/product/7.png',
                                'dummy/product/8.png',
                                'dummy/product/9.png',
                                'dummy/product/10.png',
                            ],
                            'show_username' => (bool) rand(0, 1),
                        ],
                    ]
                ];

                $product = Product::create([
                    'name' => $payload['name'],
                    'slug' => $payload['slug'],
                    'category_id' => $payload['category_id'],
                    'seller_id' => $payload['seller_id'],
                    'price' => $payload['price'],
                    'stock' => $payload['stock'],
                    'description' => $payload['description'],
                    'weight' => $payload['weight'],
                    'length' => $payload['length'],
                    'width' => $payload['width'],
                    'height' => $payload['height'],
                    'video' => $payload['video'],
                ]);

                shuffle($payload['images']);
                foreach ($payload['images'] as $image) {
                    $product->images()->create([
                        'image' => $image,
                    ]);
                }
                shuffle($payload['variations']);
                foreach ($payload['variations'] as $variation) {
                    $product->variations()->create($variation);
                }

                shuffle($payload['reviews']);
                foreach ($payload['reviews'] as $review) {
                    $product->reviews()->create($review);
                }
            }
        });
    }
}
