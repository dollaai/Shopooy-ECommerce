<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use App\Models\Category;
use App\ResponseFormatter;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function getIndex() {
        return ResponseFormatter::success([
            'message' => 'Welcome to E-commerce API',
        ]);
    }
    public function getSlider() {
        $sliders = Slider::all();
        return ResponseFormatter::success($sliders->pluck('api_response'));
    }
    public function getCategory() {
        $categories = Category::whereNull('parent_id')->with(['childs'])->get();
        return ResponseFormatter::success($categories->pluck('api_response'));
    }
}
