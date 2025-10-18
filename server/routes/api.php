<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PositionController;
use App\Models\Branch;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/profile', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//public routes
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'user_index');
    Route::post('login', 'login');
    Route::post('register', 'register_user');
    Route::put('update', 'update_user');
});

Route::get('positions',[PositionController::class,'index']);
Route::get('branches',[BranchController::class,'index']);
Route::get('departments',[DepartmentController::class,'index']);

Route::middleware('auth:sanctum')->group(
    function (){
        // Route::controller(UserController::class)->group(function () {
        //     Route::get('users', 'index');
        //     Route::post('login', 'login');
        //     Route::post('register', 'register');
        // });
    }
);


































































































