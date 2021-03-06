<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('customer', CustomerController::class)->only('index','store','destroy','show');
Route::post('/customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
Route::get('search/customers/{field}/{query}', [CustomerController::class, 'search']);