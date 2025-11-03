<?php

use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DepartmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PositionController;
use App\Models\User;

// Route::get('/', function () {
//     return view('welcome');
// });

//public routes
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'user_login');
    Route::post('register', 'register_user');
});

Route::get('positions', [PositionController::class, 'index']);
Route::get('branches', [BranchController::class, 'index']);
Route::get('departments', [DepartmentController::class, 'index']);
// Route::get('getAll_Active_users', [UserController::class, 'getAll_Active_users']);


//sanctum routes
Route::get('/profile', function (Request $request) {
    return $request->user()->load(['roles', 'departments', 'branches', 'positions']);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(
    function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('users', 'getAllUsers');
            Route::get('getAll_Active_users', 'getAll_Active_users');
            Route::get('getAll_Pending_users', 'getAll_Pending_users');
            Route::get('getAll_rejected_users', 'getAll_rejected_users');
            Route::get('show_user/{id}', 'show_user');
            Route::post('update_user/{id}', 'update_user');
            Route::post('upload_avatar', 'upload_Avatar');
            Route::post('update_employee_auth', 'update_user_auth');
            Route::post('delete_user/{id}', 'delete_user');
        });

        Route::post('logout', function (Request $request) {
            auth()->guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['message' => 'Logged out successfully']);
        });
    }
);
