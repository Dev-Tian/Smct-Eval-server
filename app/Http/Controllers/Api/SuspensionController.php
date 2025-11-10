<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Suspension;
use App\Models\User;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;

use function Laravel\Prompts\error;

class SuspensionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(User $user, Request $request)
    {
        $validate = $request->validate([
            'reason'        =>  ['required', 'alpha', 'string'],
            'duration'        =>  ['required', 'numeric', 'digits_between:1,3'],
            'suspended_by'        =>  ['required', 'alpha', 'string'],
        ]);

        Suspension::create([
            'user_id'               =>  $user->id,
            'reason'                =>  $validate['reason'],
            'days'                  =>  $validate['duration'],
            'suspended_by'          =>  $validate['suspended_by']
        ]);

        $user->update([
            'suspension'    =>  true
        ]);

        return response()->json([
            'message'   =>  'Suspended Successfully',
            'user'      =>  $user->fname
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Suspension $suspension)
    {
        $suspension->update(['is_done' => true]);
        $user = User::findOrFail($suspension->user_id);

        if(!$user){
            return response()->json([
                'message'       =>  'User not Found!'
            ],404);
        }

        $user->update([
            'reinstated'            => true,
            'reinstated_date'       => now(),
            'suspension'            => false
        ]);
        return response()->json([
            'message'   => 'Reinstated Successfully'
        ]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
