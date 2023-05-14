<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromoCodeController;

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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/promo-code/create', [PromoCodeController::class, 'createPromoCode'])->middleware('permission:create promo code');
    Route::post('/promo-code/use', [PromoCodeController::class, 'usePromoCode'])->middleware('permission:use promo code');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
