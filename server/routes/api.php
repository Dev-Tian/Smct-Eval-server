<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(
    function (){
        // Route::controller(UserController::class)->group(function () {
        //     Route::get('users', 'index');
        //     Route::post('login', 'login');
        //     Route::post('register', 'register');
        // });
    }
);

Route::controller(UserController::class)->group(function () {
    Route::get('users', 'index');
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::put('update', 'update_user');
});
































































































