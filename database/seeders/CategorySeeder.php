<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $categories = [
            [
                'name' => 'Elektronik',
                'icon' => 'dummy/category/Image.jpg',
                'childs' => ['Microwave', 'TV'],
            ],
            [
                'name' => 'Fashion Pria',
                'icon' => 'dummy/category/Image-5.jpg',
                'childs' => ['Kemeja', 'Jas'],
            ],
            [
                'name' => 'Fashion Wanita',
                'icon' => 'dummy/category/Image-7.jpg',
                'childs' => ['Sepatu', 'tas', 'Dress'],
            ],
            [
                'name' => 'Handphone',
                'icon' => 'dummy/category/Image-4.jpg',
                'childs' => ['Handphone', 'Anti Gores'],
            ],
            [
                'name' => 'Komputer & Laptop',
                'icon' => 'dummy/category/Image-2.jpg',
                'childs' => ['Keyboard', 'Mouse'],
            ],
            [
                'name' => 'Makanan & Minuman',
                'icon' => 'dummy/category/Image-1.jpg',
                'childs' => ['Makanan', 'Minuman'],
            ],
        ];

        foreach ($categories as $category) {
            $parent = Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
                'description' => null,
            ]);
           foreach ($category['childs'] as $child) {
              $parent->childs()->create([
                    'name' => $child,
                    'slug' => Str::slug($child),
              ]);
           }
            
        }
    }
}
