<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginRequestApi;
use App\Http\Controllers\Api\OrderControllerApi;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/auth/token',[LoginRequestApi::class, 'authRequest'])->name('auth.token');
Route::middleware('auth:api')->get('/order/list',[OrderControllerApi::class, 'listOrder'])->name('order.list');
Route::middleware('auth:api')->get('/order/detail/{id}',[OrderControllerApi::class, 'detailOrder'])->name('order.detail');
Route::middleware('auth:api')->post('/order/new',[OrderControllerApi::class, 'newOrder'])->name('order.new');
