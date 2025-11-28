<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersEvaluation;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //users
        $total_users = User::count();
        $total_pending_users = User::where('is_active', 'pending')->count();
        $total_active_users = User::where('is_active', 'active')->count();
        $total_declined_users = User::where('is_active', 'declined')->count();

        //evaluations
        $total_evaluations = UsersEvaluation::count();
        $total_pending_evaluations = UsersEvaluation::where('status', 'pending')->count();
        $total_completed_evaluations = UsersEvaluation::where('status', 'completed')->count();

        return response()->json([
            'total_users'                   => $total_users,
            'total_pending_users'           => $total_pending_users,
            'total_active_users'            => $total_active_users,
            'total_evaluations'             => $total_evaluations,
            'total_pending_evaluations'     => $total_pending_evaluations,
            'total_completed_evaluations'   => $total_completed_evaluations,
            'total_declined_users'          => $total_declined_users
        ], 200);
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
