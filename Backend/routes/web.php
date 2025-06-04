<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('/register', [AuthApiController::class, 'showRegisterForm'])->name('register');
Route::get('/login', [AuthApiController::class, 'showLoginForm'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/products', function () {
        return view('products.index'); 
    })->name('products');

    Route::get('/orders', function () {
        return view('orders.index'); 
    })->name('orders');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});




