<?php

<<<<<<< Updated upstream
=======
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
>>>>>>> Stashed changes
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
    Route::post('register', 'registerUser');
});

Route::get('positions', [PositionController::class, 'index']);
Route::get('branches', [BranchController::class, 'index']);
Route::get('departments', [DepartmentController::class, 'index']);

<<<<<<< Updated upstream
=======
//test
// Route::get('getAllYears', [UsersEvaluationController::class, 'getAllYears']);

>>>>>>> Stashed changes

//sanctum routes
Route::get('/profile', function (Request $request) {
    return $request->user()->load(['roles', 'departments', 'branches', 'positions']);
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(
    function () {
<<<<<<< Updated upstream
        Route::controller(UserController::class)->group(function () {
            Route::get('users', 'getAllUsers');
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
=======
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
                Route::get('getAllYears', 'getAllYears');
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
>>>>>>> Stashed changes

        Route::post('logout', function (Request $request) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return response()->json(['message' => 'Logged out successfully']);
        });
    }
);
