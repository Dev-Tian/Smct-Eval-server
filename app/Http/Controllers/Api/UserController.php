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


    public function userLogin(Request $request)
    {

        $request->validate([
            'username' => ['required', 'string', 'lowercase'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where(
            fn($user)
            =>
            $user->where('username', $request->username)
                ->orWhere('email', $request->username)
        )
            ->first();

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
        ]);
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
            'status'       => $status_filter,
            'message'      => 'ok',
            'users'        => $pending_users
        ]);
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
                $role->whereRelation('roles', 'name', $role_filter)
            )
            ->search($search_filter)
            ->get();

        return response()->json([
            'message'   => 'ok',
            'users'     => $users
        ]);
    }


    public function getCurrentUser()
    {
        $user = User::findOrFail(Auth::id());

        return response()->json([
            'data'  => $user->load(
                'branches',
                'departments',
                'positions',
                'suspensions',
                'evaluations',
                'doesEvaluated',
                'roles'
            )
        ]);
    }


    public function showUser(User $user)
    {
        return response()->json([
            'data'  =>  $user->load(
                'branches',
                'departments',
                'positions',
                'suspensions',
                'evaluations',
                'doesEvaluated',
                'roles'
            )
        ]);
    }


    public function getAllSuspendedUsers(Request $request)
    {
        $search_filter = $request->input('search');

        $sus_user  = User::with('branches', 'departments', 'positions', 'suspensions', 'roles')
            ->whereHas('suspensions', fn($query) =>  $query->where('is_done', false))
            ->where('suspension', true)
            ->search($search_filter)
            ->get();

        if (!$sus_user) {
            return response()->json([
                'message'   => 'No Data Found!'
            ]);
        }

        return response()->json([
            'suspended users'   => $sus_user,
            'message'           => 'Successfully fetch Suspended users'
        ]);
    }


    public function getAllReinstatedUsers(Request $request)
    {
        $search_filter = $request->input('search');

        $reinstated_users  = User::with('branches', 'departments', 'positions', 'suspensions', 'roles')
            ->whereHas('suspensions', fn($query) =>  $query->where('is_done', true))
            ->where('reinstated', true)
            ->search($search_filter)
            ->get();

        if (!$reinstated_users) {
            return response()->json([
                'message'   => 'No Data Found!'
            ]);
        }

        return response()->json([
            'reinstated users'   => $reinstated_users,
            'message'           => 'Successfully fetch Reinstated users'
        ]);
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
            'roles'                     => ['required', 'string', Rule::exists('roles', 'name')],
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
        ]);
    }


    public function uploadAvatar(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'file' => 'required'
        ]);

        // Early block if no file uploaded
        if (!$request->file('file')) {

            return response()->json([
                'message'       => 'Image not found or invalid file.'
            ], 400);
        }

        $avatar = $validated['file'];
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
        ]);
    }


    public function rejectRegistration(User $user)
    {
        $user->update([
            'is_active'      =>  'declined'
        ]);

        return response()->json([
            'message'       =>  'Declined successfully'
        ]);
    }


    public function deleteUser(User $user)
    {
        $user->delete();

        return response()->json([
            'message'       => 'Deleted Successfully'
        ], 200);
    }
}
