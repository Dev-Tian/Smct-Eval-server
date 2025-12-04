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
        if ($request->hasFile('signature')) {
            $signature = $request->file('signature');

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

    public function store(Request $request)
    {
        $validate = $request->validate([
            'fname'                     => ['required', 'string', 'alpha'],
            'lname'                     => ['required', 'string', 'alpha'],
            'email'                     => ['required', Rule::unique('users', 'email'), 'email', 'string', 'lowercase'],
            'position_id'               => ['required', Rule::exists('positions', 'id')],
            'branch_id'                 => ['required', Rule::exists('branches', 'id')],
            'department_id'             => ['nullable', Rule::exists('departments', 'id')],
            'employee_id'               => ['required', Rule::unique('users', 'emp_id')],
            'username'                  => ['required', 'string', 'lowercase', Rule::unique('users', 'username')],
            'contact'                   => ['required', 'string'],
            'password'                  => ['required', 'string', 'min: 8', 'max:20'],
            'role_id'                   => ['required', Rule::exists('roles', 'id')]
        ]);

        $user = User::create([
            'fname'                     => $validate['fname'],
            'lname'                     => $validate['lname'],
            'email'                     => $validate['email'],
            'position_id'               => $validate['position_id'],
            'department_id'             => $validate['department_id'] ?? null,
            'emp_id'                    => $validate['employee_id'],
            'username'                  => $validate['username'],
            'contact'                   => $validate['contact'],
            'password'                  => $validate['password'],
            'is_active'                 => 'active'
        ]);

        $user->assignRole($validate['role_id']);
        $user->branches()->sync($validate['branch_id']);

        return response()->json([
            "message"       => "Registered Successfully",
        ], 200);
    }


    //Auth
    public function userLogin(Request $request)
    {

        $request->validate([
            'email' => ['required', 'string', 'lowercase'],
            'password' => ['required', 'string'],
        ]);

        $user = User::whereAny(['username', 'email'], $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Username or email not found'
            ], 404);
        }

        $credentials = [
            'username' => !filter_var($request->email, FILTER_VALIDATE_EMAIL) ? $request->email : $user->username,
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
    public function getAllUsers(Request $request)
    {
        $search_filter = $request->input('search');
        $department_filter = $request->input('department');
        $branch_filter = $request->input('branch');

        $users  = User::with([
            'branches',
            'departments',
            'positions',
            'evaluations',
            'doesEvaluated',
            'roles'
        ])
            ->search($search_filter)
            ->when(
                $department_filter,
                fn($q)
                =>
                $q->where('department_id', $department_filter)
            )
            ->when(
                $branch_filter,
                function ($q) use ($branch_filter) {
                    $q->whereHas(
                        'branches',
                        function ($subq) use ($branch_filter) {
                            $subq->where('branch_id', $branch_filter);
                        }
                    );
                }
            )
            ->whereNot('id', Auth::id())
            ->get();

        return response()->json([
            'message'       => 'Users fetched successfully',
            'users'         => $users
        ], 200);
    }


    public function getAllPendingUsers(Request $request)
    {
        $perPage = $request->input('per_page', 10);
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
            ->paginate($perPage);

        return response()->json([
            'user_status'       => $status_filter,
            'message'      => 'ok',
            'users'        => $pending_users
        ], 200);
    }


    public function getAllActiveUsers(Request $request)
    {
        $perPage = $request->input('per_page', 10);
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
            ->paginate($perPage);

        return response()->json([
            'message'   => 'ok',
            'users'     => $users
        ], 200);
    }


    public function showUser(User $user)
    {
        $shownUser = $user->load(
            'branches',
            'departments',
            'positions',
            'evaluations',
            'doesEvaluated',
            'roles'
        );
        return response()->json([
            'data'  =>  $shownUser
        ], 200);
    }

    public function getAllBranchHeads(Request $request)
    {
        $search  = $request->input('search');
        $users = User::with([
            'branches',
            'departments',
            'positions',
            'roles'
        ])
            ->search($search)
            ->whereIn('position_id', [35, 36, 37, 38]) // <--- all branch_manager/supervisor position id
            ->get();

        return response()->json([
            'branch_heads'      =>  $users
        ], 200);
    }

    public function getAllAreaManager(Request $request)
    {
        $search  = $request->input('search');
        $users = User::with([
            'branches',
            'departments',
            'positions',
            'roles'
        ])
            ->search($search)
            ->where('position_id', 16)
            ->get();

        return response()->json([
            'branch_heads'      =>  $users
        ], 200);
    }


    //applicable for area manager / branch manager/supervisor /department manager
    public function getAllEmployeeByAuth(Request $request)
    {
        $search  = $request->input('search');
        $manager = Auth::user();

        //first test if it is manager
        $isManagerOrSupervisor = $manager->positions()
            ->where(function ($q) {
                $q->where('label', 'LIKE', '%manager%')
                    ->orWhere('label', 'LIKE', '%supervisor%');
            })
            ->exists();

        if ($isManagerOrSupervisor) {

            $isHO = $manager->branches()->where('branch_id', 126)->exists();

            //area manager
            if (!$isHO && $manager->position_id == 16 && empty($manager->department_id)) {
                $branches = $manager->branches()->pluck('branches.id');

                $branchHeads = User::with('branches', 'positions')
                    ->whereHas(
                        'branches',
                        fn($query)
                        =>
                        $query->whereIn('branch_id', $branches)
                    )
                    ->whereIn('position_id', [35, 36, 37, 38]) // <--- all branch_manager/supervisor position id
                    ->search($search)
                    ->get();

                return response()->json([
                    'employees' => $branchHeads
                ], 200);
            }

            //branch manager/supervisor
            if (
                !$isHO
                &&
                (
                    $manager->position_id == 35 ||
                    $manager->position_id == 36 ||
                    $manager->position_id == 37 ||
                    $manager->position_id == 38
                )
                &&
                empty($manager->department_id)
            ) {
                $branches = $manager->branches()->pluck('branches.id');

                $employees = User::with('branches', 'positions')
                    ->whereHas(
                        'branches',
                        fn($query)
                        =>
                        $query->whereIn('branch_id', $branches)
                    )
                    ->whereNot('position_id', 16) // <--- area manager id
                    ->search($search)
                    ->get();

                return response()->json([
                    'employees' => $employees
                ], 200);
            }

            //Department manager
            if ($isHO  && !empty($manager->department_id)) {
                $employees = User::with('branches', 'positions')
                    ->whereRelation('branches', 'branch_id', 126) //<--- must branch HO
                    ->where('department_id', $manager->department_id) // <--- must the same department
                    ->search($search)
                    ->get();

                return response()->json([
                    'employees' => $employees
                ], 200);
            }
        }
        return response()->json([
            'error' => 'Auth user is not a manager'
        ], 401);
    }


    //update
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
            'roles'                     => ['required', Rule::exists('roles', 'name')],
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

        // Early block if no file uploaded
        if (!$request->file('file')) {
            return response()->json([
                'message'       => 'Image not found or invalid file.'
            ], 400);
        }

        $avatar = $request->file('file');
        $name = time() . '-' .  $user->username . '.' . $avatar->getClientOriginalExtension();
        $path = $avatar->storeAs('user-avatars', $name, 'public');

        if ($user->avatar) {
            if (Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
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

        ]);

        $items = [
            'fname'                     => $validated['fname'],
            'lname'                     => $validated['lname'],
            'email'                     => $validated['email'],
            'bio'                       => $request->bio ?? "",
        ];

        //file handling | storing
        if ($request->file('signature')) {
            $signature = $request->file('signature');
            $name = time() . '-' .  $user->username . '.' . $signature->getClientOriginalExtension();
            $path = $signature->storeAs('user-signatures', $name, 'public');

            if ($user->signature) {
                if (Storage::disk('public')->exists($user->signature)) {
                    Storage::disk('public')->delete($user->signature);
                }
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
        ], 201);
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

    public function updateUserBranch(User $user, Request $request)
    {

        $user->branches()->syncWithoutDetaching($request->branch_ids);

        return response()->json([
            'message'       =>  'User Branch Updated'
        ], 201);
    }

    public function removeUserBranches(User $user)
    {
        $user->branches()->detach();

        return response()->json([
            'message' => 'All user branches removed'
        ], 200);
    }


    //destroy || delete
    public function deleteUser(User $user)
    {
        $user->delete();

        return response()->json([
            'message'       => 'Deleted Successfully'
        ], 200);
    }
}
