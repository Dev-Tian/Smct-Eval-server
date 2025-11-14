<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    //Create

    public function registerUser(Request $request)
    {
        $validate = $request->validate([
            'fname'                     => ['required', 'string', 'alpha'],
            'lname'                     => ['required', 'string', 'alpha'],
            'email'                     => ['required', Rule::unique('users', 'email'), 'email', 'string', 'lowercase'],
            'position_id'               => ['required', Rule::exists('positions', 'id')],
            'branch_id'                 => ['required', Rule::exists('branches', 'id')],
            'department_id'             => ['nullable', Rule::exists('departments', 'id')],
            'signature'                 => ['required'],
            'employee_id'               => ['required', Rule::unique('users', 'emp_id')],
            'username'                  => ['required', 'string', 'lowercase', Rule::unique('users', 'username')],
            'contact'                   => ['required', 'string'],
            'password'                  => ['required', 'string', 'min: 8', 'max:20']
        ]);

        //file handling | storing
        if ($request->file('signature')) {
            $signature = $request->file['signature'];

            $name = time() . '-' . $validate['username'] . '.' . $signature->getClientOriginalExtension();

            $path = $signature->storeAs('user-signatures', $name, 'public');
        } else {
            return response()->json([
                'message'       => 'Signature not found or invalid file.'
            ], 400);
        }

        $user = User::create([
            'fname'                     => $validate['fname'],
            'lname'                     => $validate['lname'],
            'email'                     => $validate['email'],
            'position_id'               => $validate['position_id'],
            'department_id'             => $validate['department_id'],
            'signature'                 => $path ?? null,
            'emp_id'                    => $validate['employee_id'],
            'username'                  => $validate['username'],
            'contact'                   => $validate['contact'],
            'password'                  => $validate['password']
        ]);

        $user->assignRole('employee');
        $user->branches()->sync($validate['branch_id']);

        return response()->json([
            "message"       => "Registered Successfully",
        ], 200);
    }


    //Auth
    public function userLogin(Request $request)
    {

        $request->validate([
            'username' => ['required', 'string', 'lowercase'],
            'password' => ['required', 'string'],
        ]);

        $user = User::whereAny(['username', 'email'], $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Username or email not found'
            ], 404);
        }

        $credentials = [
            'username' => !filter_var($request->username, FILTER_VALIDATE_EMAIL) ? $request->username : $user->username,
            'password' => $request->password
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                "status"    => false,
                "message"   => "Email and password do not match our records"
            ], 400);
        }

        $user  = Auth::user();

        $role = $user->getRoleNames();

        return response()->json([
            "role"    => $role,
            "status"  => true,
            "message" => "Login successful. Redirecting you to Dashboard"
        ], 200);
    }

    //Read
    public function getAllUsers()
    {
        $users  = User::with([
            'branches',
            'departments',
            'positions',
            'suspensions',
            'evaluations',
            'doesEvaluated',
            'roles'
        ])
            ->whereNot('id', Auth::id())
            ->get();

        return response()->json([
            'message'       => 'Users fetched successfully',
            'users'         => $users
        ], 200);
    }


    public function getAllPendingUsers(Request $request)
    {
        $search_filter = $request->input('search');
        $status_filter = $request->input('status');

        $pending_users  = User::with('positions', 'branches', 'departments', 'roles')
            ->whereNot('is_active', "active")
            ->whereNot('id', Auth::id())
            ->when(
                $status_filter,
                fn($status)
                =>
                $status->where('is_active', '=', $status_filter)
            )
            ->search($search_filter)
            ->get();

        return response()->json([
            'user_status'       => $status_filter,
            'message'      => 'ok',
            'users'        => $pending_users
        ], 200);
    }


    public function getAllActiveUsers(Request $request)
    {
        $role_filter = $request->input('role');
        $search_filter = $request->input('search');

        $users  = User::with('branches', 'departments', 'positions', 'roles')
            ->where('is_active', "active")
            ->whereNot('id', Auth::id())
            ->when(
                $role_filter,
                fn($role)
                =>
                $role->whereRelation('roles', 'id', $role_filter)
            )
            ->search($search_filter)
            ->get();

        return response()->json([
            'message'   => 'ok',
            'users'     => $users
        ], 200);
    }


    public function getCurrentUser()
    {
        $user = User::findOrFail(Auth::id())
            ->load(
                'branches',
                'departments',
                'positions',
                'suspensions',
                'evaluations',
                'doesEvaluated',
                'roles'
            );

        return response()->json([
            'data'  => $user
        ], 200);
    }


    public function showUser(User $user)
    {
        $shownUser = $user->load(
            'branches',
            'departments',
            'positions',
            'suspensions',
            'evaluations',
            'doesEvaluated',
            'roles'
        );
        return response()->json([
            'data'  =>  $shownUser
        ], 200);
    }


    public function getAllSuspendedUsers(Request $request)
    {
        $search_filter = $request->input('search');

        $sus_user  = User::with('branches', 'departments', 'positions', 'suspensions', 'roles')
            ->whereRelation('suspensions', 'is_done', false)
            ->where('suspension', true)
            ->search($search_filter)
            ->get();

        return response()->json([
            'suspended users'   => $sus_user,
            'message'           => 'Successfully fetch Suspended users'
        ], 200);
    }


    public function getAllReinstatedUsers(Request $request)
    {
        $search_filter = $request->input('search');

        $reinstated_users  = User::with('branches', 'departments', 'positions', 'suspensions', 'roles')
            ->whereRelation('suspensions', 'is_done', true)
            ->where('reinstated', true)
            ->search($search_filter)
            ->get();

        return response()->json([
            'reinstated users'   => $reinstated_users,
            'message'           => 'Successfully fetch Reinstated users'
        ], 200);
    }

    //get all branch-manager/head by auth Area Manager
    public function getAllEmployeeByAreaManagerAuth()
    {
        $areaManager = Auth::user();
        if (
            $areaManager->position_id !== 16
            &&
            empty($branchManager->department_id)
        ) {
            return response()->json([
                'message' => 'Authenticated user is not a Area Manager'
            ], 401);
        }

        $AreaManager_branches = $areaManager->branches()->pluck('branches.id');

        $branchHeads = User::with('branches', 'positions')
            ->whereHas(
                'branches',
                fn($query)
                =>
                $query->whereIn('branch_id', $AreaManager_branches)
            )
            ->whereIn('position_id', [35, 36, 37, 38]) // <--- all branch_manager/supervisor position id
            ->get();

        return response()->json([
            'AreaManager_branches_id'               =>   $AreaManager_branches,
            'Branch-manager/Head/Supervisor'        =>   $branchHeads,
        ], 200);
    }


    //get all employees by auth Branch Head
    public function getAllEmployeeByBranchManagerAuth()
    {
        $branchManager = Auth::user();
        if (
            (
                $branchManager->position_id !== 35 ||
                $branchManager->position_id !== 36 ||
                $branchManager->position_id !== 37 ||
                $branchManager->position_id !== 38
            )
            &&
            empty($branchManager->department_id)
        ) {
            return response()->json([
                'message' => 'Authenticated user is not a Area Manager'
            ], 401);
        }
        $branchManager_branches = $branchManager->branches()->pluck('branches.id');

        $employees = User::with('branches', 'positions')
            ->whereHas(
                'branches',
                fn($query)
                =>
                $query->whereIn('branch_id', $branchManager_branches)
            )
            ->whereNotIn('position_id', [16, 35, 36, 37, 38]) // <--- all branch_manager/supervisor and branch manager position id
            ->get();

        return response()->json([
            'branchManager_branches_id'               =>   $branchManager_branches,
            'employees'                               =>   $employees,
        ], 200);
    }


    public function updateUser(User $user, Request $request)
    {
        $validate = $request->validate([
            'fname'                     => ['required', 'string', 'alpha'],
            'lname'                     => ['required', 'string', 'alpha'],
            'email'                     => ['required', Rule::unique('users', 'email')->ignore($user->id), 'email', 'string', 'lowercase'],
            'position_id'               => ['required', Rule::exists('positions', 'id')],
            'branch_id'                 => ['required', Rule::exists('branches', 'id')],
            'department_id'             => ['nullable', Rule::exists('departments', 'id')],
            'username'                  => ['required', 'string', 'lowercase', Rule::unique('users', 'username')->ignore($user->id)],
            'contact'                   => ['required', 'string'],
            'roles'                     => ['required', Rule::exists('roles', 'id')],
            'password'                  => ['nullable', 'string', 'min: 8', 'max:20']
        ]);

        $user->syncRoles([$validate['roles']]);
        $user->branches()->sync([$validate['branch_id']]);

        $updateData = [
            'fname'                     => $validate['fname'],
            'lname'                     => $validate['lname'],
            'email'                     => $validate['email'],
            'position_id'               => $validate['position_id'],
            'department_id'             => $validate['department_id'],
            'username'                  => $validate['username'],
            'contact'                   => $validate['contact'],
        ];

        if ($request->password) {
            $updateData['password'] = $validate['password'];
        }

        $user->update($updateData);

        return response()->json([
            'message'   => 'Updated Successfully'
        ], 200);
    }

    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'file' => 'required'
        ]);

        // Early block if no file uploaded
        if (!$request->file('file')) {
            return response()->json([
                'message'       => 'Image not found or invalid file.'
            ], 400);
        }

        $avatar = $request->file['file'];
        $name = time() . '-' .  $user->username . '.' . $avatar->getClientOriginalExtension();
        $path = $avatar->storeAs('user-avatars', $name, 'public');

        if (Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update([
            'avatar' => $path ?? null
        ]);

        return response()->json([
            "status"    => true,
            "message"   => "Uploaded Successfully",
        ], 201);
    }

    public function updateProfileUserAuth(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $validated = $request->validate([
            'fname'                     => ['required', 'string', 'alpha'],
            'lname'                     => ['required', 'string', 'alpha'],
            'email'                     => ['required', Rule::unique('users', 'email')->ignore($user->id), 'email', 'string', 'lowercase'],
            'signature'                 => ['required'],
        ]);

        $items = [
            'fname'                     => $validated['fname'],
            'lname'                     => $validated['lname'],
            'email'                     => $validated['email'],
            'bio'                       => $request->bio ?? "",
        ];

        //file handling | storing
        if ($request->file('signature')) {
            $signature = $validated['signature'];
            $name = time() . '-' .  $user->username . '.' . $signature->getClientOriginalExtension();
            $path = $signature->storeAs('user-signatures', $name, 'public');

            if (Storage::disk('public')->exists($user->signature)) {
                Storage::disk('public')->delete($user->signature);
            }

            $items['signature'] = $path ?? null;
        }

        $user->update($items);

        return response()->json([
            "status"        => true,
            "message"       => "Uploaded Successfully",
        ], 201);
    }


    public function approveRegistration(User $user)
    {
        $user->update([
            'is_active'     =>  'active'
        ]);

        return response()->json([
            'message'       =>  'Approved'
        ],201);
    }


    public function rejectRegistration(User $user)
    {
        $user->update([
            'is_active'      =>  'declined'
        ]);

        return response()->json([
            'message'       =>  'Declined successfully'
        ], 201);
    }


    public function deleteUser(User $user)
    {
        $user->delete();

        return response()->json([
            'message'       => 'Deleted Successfully'
        ], 200);
    }
}
