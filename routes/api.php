<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/slider', [HomeController::class, 'getSlider']);
Route::get('/category', [HomeController::class, 'getCategory']);


Route::post('auth-google', [AuthenticationController::class, 'authGoogle']);
Route::post('register', [AuthenticationController::class, 'register']);
Route::post('verify-register', [AuthenticationController::class, 'verifyRegister']);
Route::post('verify-otp', [AuthenticationController::class, 'verifyOtp']);
Route::post('resend-otp', [AuthenticationController::class, 'resendOtp']);

Route::prefix('forgot-password/')->group(function () {
    Route::post('request', [ForgotPasswordController::class, 'request']);
    Route::post('resend-otp', [ForgotPasswordController::class, 'resendOtp']);
    Route::post('check-otp', [ForgotPasswordController::class, 'verifyOtp']);
    Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);
});

Route::post('login', [AuthenticationController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'getProfile']);
    Route::patch('/profile/update', [ProfileController::class, 'updateProfile']);
    Route::apiResource('/address', AddressController::class);
    Route::post('/address/{uuid}/set-default', [AddressController::class, 'setDefault']);

    Route::get('province', [AddressController::class, 'getProvince']);
    Route::get('city', [AddressController::class, 'getCity']);
    Route::prefix('/')->group(function () {
        Route::get('/', [HomeController::class, 'getIndex'])->name('home');
    
        // product
        Route::prefix('product')->name('product.')->group(function () {
            Route::get('/', [ProductController::class, 'getProduct'])->name('index');
            Route::get('/{slug}', [ProductController::class, 'getProductDetail'])->name('show');
            Route::get('/{slug}/reviews', [ProductController::class, 'getProductReview'])->name('reviews');
        });
    
        Route::get('seller/{username}', [ProductController::class, 'getSellerDetail'])->name('seller');
    
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'getCart'])->name('index');
            Route::post('/', [CartController::class, 'addToCart'])->name('add');
            Route::patch('/{uuid}', [CartController::class, 'updateCartItem'])->name('update');
            Route::delete('/{uuid}', [CartController::class, 'removeCartItem'])->name('remove');
            
            
            Route::get('/get-voucher', [CartController::class, 'getVoucher'])->name('get-voucher');
            Route::post('/apply-voucher', [CartController::class, 'applyVoucher'])->name('apply-voucher');
            Route::post('/remove-voucher', [CartController::class, 'removeVoucher'])->name('remove-voucher');
        });

    });
});

