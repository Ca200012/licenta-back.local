<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\StructureProviderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});

Route::controller(UsersController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::get('user', 'getUserData');
    Route::put('user', 'updateUserData');
});

Route::controller(ResourcesController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::get('counties', 'getCounties');
    Route::post('cities', 'getCitiesBySearch');
});

Route::controller(AddressController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::post('address', 'postAddress');
    Route::get('address', 'getAddresses');
    Route::get('address/{address_id}', 'getAddressData');
});

Route::controller(StructureProviderController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::get('category/{gender_id}', 'getCategories');
    Route::get('subcategory/{category_id}', 'getSubCategories');
    Route::get('articletype/{subcategory_id}', 'getArticleTypes');
});

Route::controller(ArticleController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::get('article/{gender?}/{category?}/{subcategory?}/{articletype?}/{sort?}', 'getArticles');
    Route::get('articledata/{article_id}', 'getArticleData');
});

Route::controller(CartsController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::post('add', 'addToCart');
    Route::post('remove', 'removeFromCart');
    Route::get('cartarticles', 'getArticlesFromCart');
    Route::get('begin-checkout', 'beginCheckout');
});

Route::controller(OrdersController::class)->middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::post('addorder', 'addOrder');
    Route::get('orders', 'getOrders');
    Route::get('orderdata/{order_id}', 'getOrderDetails');
});
