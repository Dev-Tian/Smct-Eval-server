<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{

    public function register_user(Request $request)
    {
        $validate = $request->validate([
            'fname'                     => ['required', 'string'],
            'lname'                     => ['required', 'string'],
            'email'                     => ['required', Rule::unique('users', 'email'), 'email', 'string', 'lowercase'],
            'position_id'               => ['required', Rule::exists('positions', 'id')],
            'branch_id'                 => ['required', Rule::exists('branches', 'id')],
            'department_id'             => ['required', Rule::exists('departments', 'id')],
            'signature'                 => ['required'],
            'username'                  => ['required', 'string', 'lowercase', Rule::unique('users', 'username')],
            'contact'                   => ['required', 'string'],
            'password'                  => ['required', 'string', 'min: 8', 'max:20']
        ]);

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
            'signature'                 => $path,
            'username'                  => $validate['username'],
            'contact'                   => $validate['contact'],
            'password'                  => $validate['password']
        ]);

        return response()->json([
            "status" => true,
            "message" => "Registered Successfully",
        ], 201);
    }

    public function user_login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (!Auth::attempt($request->only("email", "password"))) {
                return response()->json([
                    "status" => false,
                    "message" => "Emails and password does not matched with our records"
                ]);
            }

            return response()->json([
                "status" => true,
                "message" => "Login successfully. Redirecting you to Dashboard"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function user_index()
    {
        $users  = User::all();
        return response()->json([
            'message' => 'ok',
            'users' => $users
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show_user(string $id)
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

    /**
     * Update the specified resource in storage.
     */
    public function update_user(Request $request, string $id)
    {
        try {
            $validate = $request->validate([
                'data' => 'required'
            ]);

            $user = User::findOrFail(Auth::id());

            $user->update($validate);

            return response()->json([
                'message' => 'Updated Successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete_user(string $id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json([
                'message' => 'Updated Successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }
}
