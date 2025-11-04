<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FavController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('client', ClientController::class)->except(['index', 'store']);

    Route::get('products/{id}', [ProductController::class, 'show']);

    Route::post('client/{client}/favs', [FavController::class, 'store']);
    Route::get('client/{client}/favs', [FavController::class, 'index']);
    Route::get('client/{client}/favs/{fav}', [FavController::class, 'show']);
    Route::delete('client/{client}/favs/{external_product_id}', [FavController::class, 'destroy']);
});
