<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\AddressController;
use Illuminate\Http\Request;



Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('register', [RegisterController::class, 'register'])->name('register');
Route::post('refresh', [RefreshTokenController::class, 'refresh'])->name('refresh');


Route::middleware('auth:sanctum')->group(function () {
    Route::group(['middleware' => ['user.middleware']], function () {
        Route::get('/users', function (Request $request) {
            return response()->json(['message' => 'Authenticated access granted.']);
        });

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('brands', BrandController::class);
        Route::get('addresses/all', [AddressController::class, 'listALL']);
    });

    Route::apiResource('addresses', AddressController::class);
    //normal user routes can be added here

});
