<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\ProductApiController;
use App\Http\Controllers\OrderApiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [AuthApiController::class, 'register']);
Route::post('login', [AuthApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthApiController::class, 'logout']);
    Route::get('profile', [AuthApiController::class, 'profile']);
    Route::get('users/roles', [AuthApiController::class, 'getUsersWithRoles']);

    //Products API


    Route::get('products', [ProductApiController::class, 'index']);
    Route::get('products/{id}', [ProductApiController::class, 'show']);
    Route::post('products', [ProductApiController::class, 'store']);
    Route::put('products/{id}', [ProductApiController::class, 'update']);
    Route::delete('products/{id}', [ProductApiController::class, 'destroy']);
    Route::get('products/get/mine', [ProductApiController::class, 'getMyProducts']);
    Route::get('/products/ordered/order', [ProductApiController::class, 'getOrderedProducts']);
    Route::get('/products/search/search', [ProductApiController::class, 'search']);

    //Order API

    Route::post('/orders', [OrderApiController::class, 'store']);
    Route::get('/orders', [OrderApiController::class, 'index']);
    Route::get('/orders/with-my-products', [OrderApiController::class, 'ordersWithMyProducts']);
});