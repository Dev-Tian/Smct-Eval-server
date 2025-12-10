<?php

namespace App\Http\Controllers\Api;

use App\Models\UsersEvaluation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluatorDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $total_evaluations = UsersEvaluation::where('evaluator_id', $user->id)->whereNotNull('rating')->count() ?? 0;
        $sum_ratings = UsersEvaluation::where('evaluator_id', $user->id)->whereNotNull('rating')->sum('rating') ?? 0;
        $team_average = !empty($total_evaluations) ? ($sum_ratings / $total_evaluations) : 0;

        // Eval approvals
        $total_pending = UsersEvaluation::where('evaluator_id', $user->id)->where('status', 'pending')->whereNotNull('rating')->count() ?? 0;
        $total_approved = UsersEvaluation::where('evaluator_id', $user->id)->where('status', 'approved')->whereNotNull('rating')->count() ?? 0;

        return response()->json([
            'total_evaluations'     => $total_evaluations,
            'team_average'          => $team_average,
            'total_pending'         => $total_pending,
            'total_approved'        => $total_approved
        ]);
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
