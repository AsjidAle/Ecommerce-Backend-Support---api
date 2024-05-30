<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CouponProductController;
use App\Http\Controllers\CouponTotalController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\OfferProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('user', UserController::class);
    Route::resource('brand', BrandController::class);
    Route::resource('couponproduct', CouponProductController::class);
    Route::resource('cart', CartController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('subcategory', SubCategoryController::class);
    Route::resource('coupontotal', CouponTotalController::class);
    Route::resource('offer', OfferController::class);
    Route::resource('offerproduct', OfferProductController::class);
    Route::resource('order', OrderController::class);
    Route::resource('product', ProductController::class);
    Route::resource('product', ReviewController::class);
    Route::post('file', [FileController::class, 'store']); //upload
});
