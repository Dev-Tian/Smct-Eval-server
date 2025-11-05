<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller{

    public function register_user(Request $request){
        try{
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
                "message" => "Registered Successfully",
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function user_login(Request $request){
        try{
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
            $role = $user->getRoleNames();

            return response()->json([
                "role"    => $role,
                "status"  => true,
                "message" => "Login successful. Redirecting you to Dashboard"
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAllUsers(){
        try{
            $users  = User::whereNot('id','=', Auth::id())
                        ->get();

            return response()->json([
                'message' => 'ok',
                'users' => $users
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Fetch all users failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAll_Pending_users(){
        try{
            $pending_users  = User::with('positions','branches','departments')
                                ->whereNot('is_active', "active")
                                ->whereNot('id','=', Auth::id())
                                ->get();

            return response()->json([
                'message' => 'ok',
                'users' => $pending_users
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Fetch all pending users failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getAll_Active_users(Request $request){
        try{
            $role_filter = $request->input('role');
            $search_filter = $request->input('search');

            $active_users  = User::with('positions','branches','departments','roles')
                                ->where('is_active', "active")
                                ->whereNot('id','=', Auth::id());

            if(!empty($role_filter)){
                 $active_users->whereHas('roles',
                                    function($q) use ($role_filter) {
                                        $q->where('name', $role_filter);
                                });
            }

            if(!empty($search_filter)){
                $active_users->where(
                        function($q) use ($search_filter){
                            $q->where('fname', 'like', "%{$search_filter}%")
                              ->orWhere('lname', 'like', "%{$search_filter}%")
                              ->orWhere('email', 'like', "%{$search_filter}%");
                        });
            }

            $users = $active_users->get();

            return response()->json([
                'message' => 'ok',
                'users' => $users
            ]);

        } catch (Exception $e) {
            return response()->json([
                 'message' => 'Fetch all active users failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getCurrentUser(){
        try{
            $user = User::findOrFail(Auth::id());

            if(!$user){
                return response()->json([
                    'message'   =>  'User not found'
                ]);
            }

            return response()->json([
                'data' => $user
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Fetch current user failed',
                'error' => $e->getMessage(),
            ], 500);
        }

    }


    public function show_user($id){
        try{
            $user = User::findOrFail($id);

            if(!$user){
                return response()->json([
                    'message'   =>  'User not found'
                ]);
            }

            return response()->json([
                'data' => $user
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Fetch user failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update_user(Request $request, string $id){
        try{
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
                'roles'                     => ['required','string', Rule::exists('roles','name')],
                'password'                  => ['nullable', 'string', 'min: 8', 'max:20']
            ]);

            $user->syncRoles([$request->roles]);

            $updateData =[
                'fname'                     => $validate['fname'],
                'lname'                     => $validate['lname'],
                'email'                     => $validate['email'],
                'position_id'               => $validate['position_id'],
                'branch_id'                 => $validate['branch_id'],
                'department_id'             => $validate['department_id'],
                'username'                  => $validate['username'],
                'contact'                   => $validate['contact'],
            ];
            if (!empty($validate['password']) && is_string($validate['password'])) {
                $updateData['password'] = $validate['password'];
            }

            $user->update($updateData);

            return response()->json([
                'message' => 'Updated Successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
            'message' => 'Update failed',
            'error' => $e->getMessage(),
        ], 500);
        }
    }


    public function upload_Avatar(Request $request){
        try{
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

        } catch (Exception $e) {
            return response()->json([
            'message' => 'Update failed',
            'error' => $e->getMessage(),
        ], 500);
        }
    }


    public function update_user_auth(Request $request){
        try{
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

        } catch (Exception $e) {
            return response()->json([
            'message' => 'Update failed',
            'error' => $e->getMessage(),
        ], 500);
        }
    }


    public function approveRegistration($id){
        try{
            $user = User::findOrFail($id);

            if(!$user){
                return response()->json([
                    'message'   => 'User not found'
                ]);
            }

            $user->update([
                'is_active'         =>  'active'
            ]);

            return response()->json([
                'message'       =>  'Approved'
            ]);

        } catch (Exception $e) {
            return response()->json([
            'message' => 'approved failed',
            'error' => $e->getMessage(),
        ], 500);
        }

    }


    public function rejectRegistration($id){
        try{
            $user = User::findOrFail($id);

            if(!$user){
                return response()->json([
                    'message'   => 'User not found'
                ]);
            }

            $user->update([
                'is_active'         =>  'declined'
            ]);

            return response()->json([
                'message'       =>  'Declined successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
            'message' => 'rejection failed',
            'error' => $e->getMessage(),
        ], 500);
        }

    }


    public function delete_user($id){
        try{
            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $user->delete();

            return response()->json([
                'message' => 'Deleted Successfully'
            ], 200);

          } catch (Exception $e) {
            return response()->json([
            'message' => 'deleting failed',
            'error' => $e->getMessage(),
        ], 500);
        }
    }
}
