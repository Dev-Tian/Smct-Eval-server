<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SuspensionController;
use Illuminate\Support\Facades\Auth;

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
    return $request->user()->load(
        [
            'roles',
            'departments',
            'branches',
            'positions',
            'evaluations',
            'doesEvaluated',
            'suspensions'
        ]
    );
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(
    function () {
        Route::controller(UserController::class)->group(
            function () {
                Route::get('getAllUsers', 'getAllUsers');
                Route::get('getAllActiveUsers', 'getAllActiveUsers');
                Route::get('getAllPendingUsers', 'getAllPendingUsers');
                Route::get('getAllSuspendedUsers', 'getAllSuspendedUsers');
                Route::get('getAllReinstatedUsers', 'getAllReinstatedUsers');
                Route::get('getAllEmployeeByAreaManagerAuth', 'getAllEmployeeByAreaManagerAuth');
                Route::get('getAllEmployeeByBranchManagerAuth', 'getAllEmployeeByBranchManagerAuth');
                Route::get('showUser/{user}', 'showUser');
                Route::post('updateUser/{user}', 'updateUser');
                Route::post('uploadAvatar', 'uploadAvatar');
                Route::post('updateProfileUserAuth', 'updateProfileUserAuth');
                Route::post('deleteUser/{user}', 'deleteUser');
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

        Route::get('getAllRoles', [RoleController::class, 'index']);

        Route::post('logout', function (Request $request) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Logged out successfully'
            ], 200);
        });
    }
);
