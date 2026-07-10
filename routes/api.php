<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\EmployeeDashboardController;
use App\Http\Controllers\Api\EvaluatorDashboardController;
use App\Http\Controllers\Api\HrDashboardController;
use App\Http\Controllers\Api\MemorandumViolationController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\OtpController;
use App\Http\Controllers\Api\UpdateUsersEvaluationController;
use App\Http\Controllers\Api\UsersEvaluationController;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Auth;

//public routes

//login and register
Route::controller(UserController::class)->group(function () {
    Route::post('login', 'userLogin');
    Route::post('register', 'registerUser');
});

Route::post('forgot-password/send-otp', [OtpController::class, 'otpRequest']);
Route::post('forgot-password/verify-otp', [OtpController::class, 'otpVerify']);

Route::get('positions', [PositionController::class, 'index']);
Route::get('branches', [BranchController::class, 'index']);
Route::get('departments', [DepartmentController::class, 'index']);

//sanctum routes
Route::get('/profile', function (Request $request) {
   $user = $request->user()
        ->load(
            [
                'roles',
                'departments',
                'branch',
                'branches',
                'positions',
                'notifications' => function($q) { $q->latest()->limit(15); },
            ]
        );
    $counts = $user->notifications()->latest()->limit(15)->get();
    $user->notification_counts = $counts->where("read_at", null)->count();
    return new  ProfileResource($user);
})->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(
    function () {
        Route::controller(UserController::class)->group(
            function () {
                // Route::get('getAllUsers', 'getAllUsers');
                Route::get('getEvaluatorsByBranch/{user}', 'getEvaluatorsByBranch');
                Route::get('getAllActiveUsers', 'getAllActiveUsers');
                Route::get('getPendingRegistrations', 'getAllPendingUsers');
                Route::get('getAllBranchHeads', 'getAllBranchHeads');
                Route::get('getAllAreaManager', 'getAllAreaManager');
                Route::get('getAllEmployeeByAuth', 'getAllEmployeeByAuth');
                Route::get('showUser/{user}', 'showUser');
                Route::get('getAllSignatureReset', 'getAllSignatureRequest');
                Route::get('getSubordinate', 'getSubordinate');
                Route::get('getAllEvaluators', 'getAllEvaluators');
                Route::get('getAllEvaluatorAssignedEmployees/{user}', 'getAllEvaluatorAssignedEmployees');
                Route::get('getAllEvaluatorEmployees/{user}', 'getAllEvaluatorEmployees');
                Route::post('requestSignatureReset', 'requestSignatureReset');
                Route::post('bulkRegisterUser', 'bulkRegisterUser');
                Route::post('approvedSignatureReset/{user}', 'approvedSignatureReset');
                Route::post('rejectSignatureReset/{user}', 'rejectSignatureReset');
                Route::post('updateUser/{user}', 'updateUser');
                Route::post('uploadAvatar', 'uploadAvatar');
                Route::post('updateProfileUserAuth', 'updateProfileUserAuth');
                Route::post('addUser', 'store');
                Route::post('assignEmployees/{user}', 'assignEmployees');
                Route::post('saveApprovalFlow/{user}', 'assignApprovers');
                Route::post('updateUserBranch/{user}', 'updateUserBranch');
                Route::post('removeUserBranches/{user}', 'removeUserBranches');
                Route::post('approveRegistration/{user}', 'approveRegistration');
                Route::post('deleteUser/{user}', 'deleteUser');
            }
        );

        Route::controller(UpdateUsersEvaluationController::class)->group(
            function () {
                Route::post('BranchBasic/resubmit/{usersEvaluation}', 'BranchBasic');
                Route::post('BranchRankNFile/resubmit/{usersEvaluation}', 'BranchRankNFile');
                Route::post('HoBasic/resubmit/{usersEvaluation}', 'HoBasic');
                Route::post('HoRankNFile/resubmit/{usersEvaluation}', 'HoRankNFile');
                Route::post('BranchBasicAreaManager/resubmit/{usersEvaluation}', 'BranchBasicAreaManager');
            }
        );

        Route::controller(UsersEvaluationController::class)->group(
            function () {
                Route::get('allEvaluations', 'index');
                Route::get('getQuarters/{user}', 'getQuarters');
                Route::get('getEvalAuthEvaluator', 'getEvalAuthEvaluator');
                Route::get('getMyEvalAuthEmployee', 'getMyEvalAuthEmployee');
                Route::get('getAllYears', 'getAllYears');
                Route::get('submissions/{usersEvaluation}', 'show');
                Route::post('approvedByEmployee/{usersEvaluation}', 'approvedByEmployee');
                Route::post('deleteEval/{usersEvaluation}', 'destroy');
                Route::post('acceptApprovalEvaluation/{usersEvaluation}', 'approveEvaluation');
                Route::post('rejectApprovalEvaluation/{usersEvaluation}', 'rejectDraftEvaluation');
                //submissions
                //branch rank n file
                Route::post('BranchRankNFile/{user}', 'BranchRankNFile');
                //branch basic
                Route::post('BranchBasic/{user}', 'BranchBasic');
                //ho basic
                Route::post('HoBasic/{user}', 'HoBasic');
                //ho rank n file
                Route::post('HoRankNFile/{user}', 'HoRankNFile');
                // area managers
                Route::post('BranchBasicAreaManager/{user}', 'BranchBasicAreaManager');
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

        Route::controller(PositionController::class)->group(
            function () {
                Route::get('position/{position}', 'show');
                Route::post('addPosition', 'store');
                Route::post('updatePosition/{position}', 'update');
                Route::post('deletePosition/{position}', 'destroy');
            }
        );

        Route::controller(DepartmentController::class)->group(
            function () {
                Route::get('getTotalEmployeesDepartments', 'getTotalEmployeesDepartments');
                Route::post('addDepartment', 'store');
                Route::post('deleteDepartment/{department}', 'destroy');
            }
        );

        Route::controller(MemorandumViolationController::class)->group(
            function(){
                Route::get('memorandumViolations', 'index');
                Route::get('myMemorandumViolations', 'auth_index');
                Route::get('showUserMemorandumViolation/{id}', 'show_perUser');
                Route::post('addMemorandumViolation', 'store');
                Route::post('updateMemorandumViolation/{memorandumViolation}', 'update');
                Route::post('deleteMemorandumViolation/{memorandumViolation}', 'destroy');
            }
        );

        Route::controller(NotificationsController::class)->group(
            function () {
                Route::post('deleteNotification/{notification}', 'destroy');
                Route::post('isReadNotification/{notification}', 'isRead');
                Route::post('markAllAsRead', 'markAllAsRead');
            }
        );

        //Dashboards
        Route::get('adminDashboard', [AdminDashboardController::class, 'index']);
        Route::get('evaluatorDashboard', [EvaluatorDashboardController::class, 'index']);
        Route::get('hrDashboard', [HrDashboardController::class, 'index']);
        Route::get('employeeDashboard', [EmployeeDashboardController::class, 'index']);
        Route::get('employeeDashboard2/{user}', [EmployeeDashboardController::class, 'index2']);

        Route::get('getAllRoles', [RoleController::class, 'index']);

        Route::post('logout', function (Request $request)
        {
            Auth::guard("web")->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(
                [
                    'message' => 'Logged out successfully'
                ]
                ,200
            );
        });
    }
);

