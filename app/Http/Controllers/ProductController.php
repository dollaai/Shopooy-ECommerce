<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Products\Product;
use App\Models\User;
use App\ResponseFormatter;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProduct(Request $request)
    {
        $product = Product::query();
        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            $product->where('category_id', $category->id);
        }
        if ($request->has('seller')) {
            $seller = User::where('username', $request->seller)->first();
            $product->where('seller_id', $seller->id);
        }
        if($request->has('search')) {
            $product->where('name', 'like', '%' . $request->search . '%');
        }

        if($request->minimum_price) {
            $product->whereRaw('IF(price_sale > 0, price_sale, price) >= ?', $request->minimum_price);
        }

        if($request->maximum_price) {
            $product->whereRaw('IF(price_sale > 0, price_sale, price) <= ?', $request->maximum_price);
        }

        if($request->sorting_price) {
            $type = $request->sorting_price == 'asc' ? 'ASC' : 'DESC';
            $product->orderByRaw('IF(price_sale > 0, price_sale, price) ' . $type);
        } else {
            $product->orderBy('id', 'DESC');
        }

        if($request->categories && is_array($request->categories)) {
            $product->whereHas('category', fn($q) => $q->whereIn('slug', $request->categories));
        }


        $products = $product->paginate($request->per_page ?? 10);
        return ResponseFormatter::success($products->through(fn($item) => $item->api_response_excerpt));
        
    }

    public function getProductDetail($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return ResponseFormatter::success($product->api_response);
    }

    public function getProductReview($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $reviews = $product->reviews();

        if (request()->has('rating')) {
            $reviews->where('star_seller', request()->rating);
        }

        if(request()->has('with_attachments')) {
            $reviews->whereNotNull('attachments');
        }

        if(request()->has('with_description')) {
            $reviews->whereNotNull('description');
        }

        $reviews = $reviews->paginate(request()->per_page ?? 10);
        return ResponseFormatter::success($reviews->through(fn($item) => $item->api_response));
    }

    public function getSellerDetail($username)
    {
        $seller = User::where('username', $username)->firstOrFail();
        return ResponseFormatter::success($seller->api_response_as_seller);
    }
}
