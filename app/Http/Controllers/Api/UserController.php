<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

    public function register_user(Request $request)
    {
        try {
            $validate = $request->validate([
                'fname'                     => ['required', 'string'],
                'lname'                     => ['required', 'string'],
                'email'                     => ['required', Rule::unique('users', 'email'), 'email', 'string', 'lowercase'],
                'position_id'               => ['required', Rule::exists('positions', 'id')],
                'branch_id'                 => ['required', Rule::exists('branches', 'id')],
                'department_id'             => ['nullable', Rule::exists('departments', 'id')],
                'signature'                 => ['required'],
                'username'                  => ['required', 'string', 'lowercase', Rule::unique('users', 'username')],
                'contact'                   => ['required', 'string'],
                'password'                  => ['required', 'string', 'min: 8', 'max:20']
            ]);

            //file handling | storing
            if ($request->file('signature')) {
                $signature = $validate['signature'];

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
                'branch_id'                 => $validate['branch_id'],
                'department_id'             => $validate['department_id'],
                'signature'                 => $path ?? null,
                'username'                  => $validate['username'],
                'contact'                   => $validate['contact'],
                'password'                  => $validate['password']
            ]);

            $user->assignRole('employee');

            return response()->json([
                "status" => true,
                "message" => "Registered Successfully",
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function user_login(Request $request)
    {
        try {
            $request->validate([
                'username' => ['required', 'string', 'lowercase'],
                'password' => ['required', 'string'],
            ]);

            $credentials = $request->only('username', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    "status" => false,
                    "message" => "Email and password do not match our records"
                ], 400);
            }
            $user  = Auth::user();
            $role = $user->roles->pluck('name');
            return response()->json([
                "role"    => $role,
                "status"  => true,
                "message" => "Login successful. Redirecting you to Dashboard"
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllUsers()
    {
        $users  = User::whereNot('id','=', Auth::id())
                      ->get();

        return response()->json([
            'message' => 'ok',
            'users' => $users
        ]);
    }

    public function getAll_Pending_users()
    {
        $pending_users  = User::with('positions','branches','departments')
                              ->where('is_active', "pending")
                              ->where('id','!=', Auth::id())
                              ->get();

        return response()->json([
            'message' => 'ok',
            'users' => $pending_users
        ]);
    }

    public function getAll_Active_users()
    {
        $active_users  = User::with('positions','branches','departments','roles')
                             ->where('is_active', "active")
                             ->whereNot('id','=', Auth::id())
                             ->get();
        return response()->json([
            'message' => 'ok',
            'users' => $active_users
        ]);
    }

    public function getAll_rejected_users()
    {
        $user = Auth::user();
        $rejected_users  = User::with('positions','branches','departments','roles')
                               ->where('is_active', "declined")
                               ->whereNot('id','=', Auth::id())
                               ->get();
        return response()->json([
            'message' => 'ok',
            'users' => $rejected_users
        ]);
    }
    /**
     * Display the specified resource.
     */
    public function getCurrentUser()
    {
        try {
            $user = User::findOrFail(Auth::id());

            return response()->json([
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function show_user($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'data' => $user
            ]);
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

// todo partial
    public function update_user(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $validate = $request->validate([
                'fname'                     => ['required', 'string'],
                'lname'                     => ['required', 'string'],
                'email'                     => ['required', Rule::unique('users', 'email')->ignore($user->id), 'email', 'string', 'lowercase'],
                'position_id'               => ['required', Rule::exists('positions', 'id')],
                'branch_id'                 => ['required', Rule::exists('branches', 'id')],
                'department_id'             => ['nullable', Rule::exists('departments', 'id')],
                'username'                  => ['required', 'string', 'lowercase', Rule::unique('users', 'username')->ignore($user->id)],
                'contact'                   => ['required', 'string'],
                'roles'                     => ['required', 'string'],
            ]);


            $user->update([
                'fname'                     => $validate['fname'],
                'lname'                     => $validate['lname'],
                'email'                     => $validate['email'],
                'position_id'               => $validate['position_id'],
                'branch_id'                 => $validate['branch_id'],
                'department_id'             => $validate['department_id'],
                'signature'                 => $path ?? null,
                'username'                  => $validate['username'],
                'contact'                   => $validate['contact'],
                'roles'
            ]);

            return response()->json([
                'message' => 'Updated Successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function upload_Avatar(Request $request)
    {
         /** @var User $user */
        $user = Auth::user();
        $validated = $request->validate([
            'file' => 'required'
        ]);

        //file handling | storing
        if ($request->file('file')) {
            $avatar = $validated['file'];
            $name = time() . '-' .  $user->username . '.' . $avatar->getClientOriginalExtension();
            $path = $avatar->storeAs('user-avatars', $name, 'public');

            if(Storage::disk('public')->exists($user->avatar)){
                Storage::disk('public')->delete($user->avatar);
            }

        } else {
            return response()->json([
                'message'       => 'Image not found or invalid file.'
            ], 400);
        }

        $user->update([
            'avatar' => $path ?? null
        ]);

        return response()->json([
            "img_url"   =>$name,
            "status"    => true,
            "message"   => "Uploaded Successfully",
        ], 201);
    }

    public function update_employee_auth(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

            $validated = $request->validate([
                'fname'                     => ['required', 'string'],
                'lname'                     => ['required', 'string'],
                'email'                     => ['required', Rule::unique('users', 'email')->ignore($user->id), 'email', 'string', 'lowercase'],
                'signature'                 => ['required'],
            ]);


        //file handling | storing
            if ($request->file('signature')) {
                $signature = $validated['signature'];
                $name = time() . '-' .  $user->username . '.' . $signature->getClientOriginalExtension();
                $path = $signature->storeAs('user-signatures', $name, 'public');

                if(Storage::disk('public')->exists($user->signature)){
                    Storage::disk('public')->delete($user->signature);
                }

            } elseif(is_string($request->signature)) {
                $path  = $request->signature;
            }else{
                return response()->json([
                    'message'       => 'Image not found or invalid file.'
                ], 400);
            }

            $user->update([
                'fname'                     => $validated['fname'],
                'lname'                     => $validated['lname'],
                'email'                     => $validated['email'],
                'bio'                       => $request->bio?? "",
                'signature'                 => $path ,
            ]);

        return response()->json([
            "img_url"=>$path,
            "status" => true,
            "message" => "Uploaded Successfully",
        ], 201);
    }


    public function delete_user($id)
    {
            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user->delete();

            return response()->json([
                'message' => 'Deleted Successfully'
            ], 200);
        }
}
