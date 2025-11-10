<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\SuspensionController;
use App\Models\Suspension;

//public routes

//login and register
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'userLogin');
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
        Route::controller(UserController::class)->group(
            function () {
                Route::get('users', 'getAllUsers');
                Route::get('getAll_Active_users', 'getAllActiveUsers');
                Route::get('getAll_Pending_users', 'getAllPendingUsers');
                Route::get('getAllSuspendedUsers', 'getAllSuspendedUsers');
                Route::get('getAllReinstatedUsers', 'getAllReinstatedUsers');
                Route::get('show_user/{user}', 'showUser');
                Route::post('update_user/{user}', 'updateUser');
                Route::post('upload_avatar', 'uploadAvatar');
                Route::post('update_employee_auth', 'updateProfileUserAuth');
                Route::post('delete_user/{user}', 'deleteUser');
                Route::post('approveRegistration/{user}', 'approveRegistration');
                Route::post('rejectRegistration/{user}', 'rejectRegistration');
            }
        );

        Route::controller(SuspensionController::class)->group(
            function () {
                Route::post('suspend/{user}', 'store');
                Route::post('updateSuspension/{suspension}', 'update');
            }
        );

        Route::post('logout', function (Request $request) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['message' => 'Logged out successfully']);
        });
    }
);
