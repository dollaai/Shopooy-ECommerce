<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $sliders = [
            'dummy/banner/banner-1.png',
            'dummy/banner/banner-2.png',
            'dummy/banner/banner-3.png',
            'dummy/banner/banner-4.png',
        ];
        foreach($sliders as $slider) {
            Slider::create([
                'image' => $slider
            ]);
        }
    }
}
