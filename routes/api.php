<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\RecommendationsController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\StructureProviderController;
use App\Http\Controllers\PasswordResetsController;

Route::middleware(['xss.sanitize', 'cors'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('logout', 'logout');
        Route::post('refresh', 'refresh');
    });

    Route::controller(UsersController::class)->group(function () {
        Route::get('user', 'getUserData');
        Route::put('user', 'updateUserData');
        Route::post('message', 'sendMessageReceivedEmail');
    });

    Route::controller(PasswordResetsController::class)->group(function () {
        Route::post('check-email', 'checkEmailAndGenerateCode');
        Route::post('check-code', 'checkResetCode');
        Route::post('reset-password', 'resetPassword');
    });

    Route::controller(ResourcesController::class)->group(function () {
        Route::get('counties', 'getCounties');
        Route::post('cities', 'getCitiesBySearch');
    });

    Route::controller(AddressController::class)->group(function () {
        Route::post('address', 'postAddress');
        Route::get('address', 'getAddresses');
        Route::delete('address/{address_id}', 'deleteAddress');
    });

    Route::controller(StructureProviderController::class)->group(function () {
        Route::get('category/{gender_id}', 'getCategories');
        Route::get('subcategory/{category_id}', 'getSubCategories');
        Route::get('articletype/{subcategory_id}', 'getArticleTypes');
    });

    Route::controller(ArticleController::class)->group(function () {
        Route::get('article/{gender?}/{category?}/{subcategory?}/{articletype?}/{sort?}', 'getArticles');
        Route::get('articledata/{article_id}', 'getArticleData');
        Route::post('viewedarticle', 'addViewedArticle');
        Route::get('getviewedarticles', 'getViewedArticles');
        Route::post('can-purchase', 'checkIfCanPurchase');
    });

    Route::controller(CartsController::class)->group(function () {
        Route::post('add', 'addToCart');
        Route::post('remove', 'removeFromCart');
        Route::post('cartarticles', 'getArticlesFromCart');
        Route::get('begin-checkout', 'beginCheckout');
        Route::post('store-articles-from-ls', 'storeArticlesFromLs');
    });

    Route::controller(OrdersController::class)->group(function () {
        Route::post('addorder', 'addOrder');
        Route::get('orders', 'getOrders');
        Route::get('orderdata/{order_id}', 'getOrderDetails');
        Route::get('cancel/{order_id}', 'cancelOrder');
    });

    Route::controller(RecommendationsController::class)->group(function () {
        Route::get('/recommendations', 'getRecommendations');
    });
});
