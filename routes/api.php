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
    Route::post('login', 'userLogin');
    Route::get('users', 'getAllUsers');
    Route::post('register', 'registerUser');
});

Route::get('positions', [PositionController::class, 'index']);
Route::get('branches', [BranchController::class, 'index']);
Route::get('departments', [DepartmentController::class, 'index']);


//sanctum routes
Route::get('/profile', function (Request $request) {
    return $request->user()->load(['roles', 'departments', 'branches', 'positions']);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(
    function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('getAll_Active_users', 'getAllActiveUsers');
            Route::get('getAll_Pending_users', 'getAllPendingUsers');
            Route::get('show_user/{id}', 'showUser');
            Route::post('update_user/{id}', 'updateUser');
            Route::post('upload_avatar', 'uploadAvatar');
            Route::post('update_employee_auth', 'updateUserAuth');
            Route::post('delete_user/{id}', 'deleteUser');
            Route::post('approveRegistration/{id}', 'approveRegistration');
            Route::post('rejectRegistration/{id}', 'rejectRegistration');
        });

        Route::post('logout', function (Request $request) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['message' => 'Logged out successfully']);
        });
    }
);
