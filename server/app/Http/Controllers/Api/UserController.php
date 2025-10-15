<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{

     public function register_user(Request $request){
        try{
            $validate = $request->validate([
                'fname'=>"required"
            ]);

            $user = User::create($validate);

            return response()->json([
                "status" => true,
                "message" => "Registered Successfully",
            ]);
        } catch (Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
            ]);
        }
}

    public function user_login(Request $request){
        try{
            $request->validate([
                'email'=>'required|email',
                'password'=>'required'
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
         }catch (Exception $e) {
                return response()->json([
                    'errors' => $e->getMessage()
            ]);
        }
    }

    public function user_index()
    {
        $users  = User::all();
        return response()->json([
            'message'=>'ok',
            'users'=>$users
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show_user(string $id)
    {
        try{
            $user = User::findOrFail($id);

            return response()->json([
                'data'=>$user
            ]);
       }catch (Exception $e) {
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
        try{
            $validate = $request->validate([
                'data'=>'required'
            ]);

            $user = User::findOrFail(Auth::id());

            $user->update($validate);

            return response()->json([
                'message'=>'Updated Successfully'
            ]);

        }catch (Exception $e) {
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
        try{
            $user = User::findOrFail($id);

            $user->delete();

            return response()->json([
                'message'=>'Updated Successfully'
            ]);

        }catch (Exception $e) {
            return response()->json([
                'errors' => $e->getMessage()
            ]);
        }
    }
}
