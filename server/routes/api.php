<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PositionController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/profile', function (Request $request) {
    return $request->user()->load('roles');
})->middleware('auth:sanctum');

//public routes
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'user_login');
    Route::post('register', 'register_user');
});

Route::get('positions', [PositionController::class, 'index']);
Route::get('branches', [BranchController::class, 'index']);
Route::get('departments', [DepartmentController::class, 'index']);

Route::get('getAll_Active_users', [UserController::class, 'getAll_Active_users']);
Route::get('getAll_Pending_users', [UserController::class, 'getAll_Pending_users']);

//sanctum routes
Route::middleware('auth:sanctum')->group(
    function () {
        Route::get('users', [UserController::class, 'getAllUsers']);
        Route::get('show_user', [UserController::class, 'show_user']);
        Route::get('update', [UserController::class, 'update_user']);
    }
);
