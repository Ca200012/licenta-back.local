<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\UsersController;

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
