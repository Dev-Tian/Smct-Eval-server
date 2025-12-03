<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\EmployeeDashboardController;
use App\Http\Controllers\Api\EvaluatorDashboardController;
use App\Http\Controllers\Api\HrDashboardController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\UsersEvaluationController;

//public routes

//login and register
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'userLogin');
    Route::post('register', 'registerUser');
});

Route::get('positions', [PositionController::class, 'index']);
Route::get('branches', [BranchController::class, 'index']);
Route::get('departments', [DepartmentController::class, 'index']);

//test
// Route::get('getTotalEmployeesBranch', [BranchController::class, 'getTotalEmployeesBranch']);


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
            'unreadNotifications'
        ]
    )
        ->loadCount('unreadNotifications as notification_counts');
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(
    function () {
        Route::controller(UserController::class)->group(
            function () {
                Route::get('getAllUsers', 'getAllUsers');
                Route::get('getAllActiveUsers', 'getAllActiveUsers');
                Route::get('getPendingRegistrations', 'getAllPendingUsers');
                Route::get('getAllBranchHeads', 'getAllBranchHeads');
                Route::get('getAllAreaManager', 'getAllAreaManager');
                Route::get('getAllEmployeeByAuth', 'getAllEmployeeByAuth');
                Route::get('showUser/{user}', 'showUser');
                Route::post('updateUser/{user}', 'updateUser');
                Route::post('uploadAvatar', 'uploadAvatar');
                Route::post('updateProfileUserAuth', 'updateProfileUserAuth');
                Route::post('addUser', 'store');
                Route::post('updateUserBranch/{user}', 'updateUserBranch');
                Route::post('removeUserBranches/{user}', 'removeUserBranches');
                Route::post('approveRegistration/{user}', 'approveRegistration');
                Route::post('rejectRegistration/{user}', 'rejectRegistration');
                Route::post('deleteUser/{user}', 'deleteUser');
            }
        );

        Route::controller(BranchController::class)->group(
            function () {
                Route::get('getTotalEmployeesBranch', 'getTotalEmployeesBranch');
                Route::get('branch/{branch}', 'show');
                Route::post('addBranch', 'store');
                Route::post('deleteBranch/{branch}', 'destroy');
            }
        );

        Route::controller(UsersEvaluationController::class)->group(
            function () {
                Route::get('allEvaluations', 'index');
                Route::get('getEvalAuthEvaluator', 'getEvalAuthEvaluator');
                Route::get('getMyEvalAuthEmployee', 'getMyEvalAuthEmployee');
                Route::get('submissions/{usersEvaluation}', 'show');
                Route::post('submit/{user}', 'store');
                Route::post('approvedByEmployee/{usersEvaluation}', 'approvedByEmployee');
                Route::post('deleteEval/{usersEvaluation}', 'destroy');
            }
        );

        Route::controller(DepartmentController::class)->group(
            function () {
                Route::get('getTotalEmployeesDepartments', 'getTotalEmployeesDepartments');
                Route::post('addDepartment', 'store');
                Route::post('deleteDepartment/{department}', 'destroy');
            }
        );

        Route::post('isReadNotification', [NotificationsController::class, 'isRead']);

        //Dashboards
        Route::get('adminDashboard', [AdminDashboardController::class, 'index']);
        Route::get('evaluatorDashboard', [EvaluatorDashboardController::class, 'index']);
        Route::get('hrDashboard', [HrDashboardController::class, 'index']);
        Route::get('employeeDashboard', [EmployeeDashboardController::class, 'index']);

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
