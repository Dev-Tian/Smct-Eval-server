<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsersEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\select;

class EmployeeDashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $user_eval = UsersEvaluation::where('employee_id', $user->id)->get();
        $total_evaluations = UsersEvaluation::where('employee_id', $user->id)->count() ?? 0;
        $sum_ratings = UsersEvaluation::where('employee_id', $user->id)->whereNotNull("rating")->sum('rating') ?? 0;
        $average = empty(!$total_evaluations) ? ($sum_ratings / $total_evaluations) : 0;
        $recent_evaluation = UsersEvaluation::where('employee_id', $user->id)
            ->latest('created_at')
            ->select('id', 'rating')
            ->first();

        return response()->json([
            'total_evaluations'     =>  $total_evaluations,
            'average'               =>  $average,
            'recent_evaluation'     =>  $recent_evaluation,
            'user_eval'             =>  $user_eval,
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
