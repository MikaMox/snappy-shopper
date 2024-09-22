<?php

use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/shop', [ShopController::class, 'create']);
Route::get('/shop/{id}', [ShopController::class, 'show']);
Route::get('/nearest/{postcode}/{distance?}', [ShopController::class, 'nearest']);
Route::get('/delivers/{postcode}', [ShopController::class, 'deliverTo']);