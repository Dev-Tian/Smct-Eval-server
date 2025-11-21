<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $branches = Branch::all();

        return response()->json([
            'branches' => $branches
        ], 200);
    }

    public function getTotalEmployeesBranch()
    {
        $all = Branch::withCount([
            'users as managers_count'
            =>
            fn($user)
            =>
            $user->whereHas(
                'positions',
                fn($position)
                =>
                $position->where('label', 'LIKE', "%manager%")
            ),
            'users as employees_count'
            =>
            fn($user)
            =>
            $user->whereHas(
                'positions',
                fn($position)
                =>
                $position->whereNot('label', 'LIKE', "%manager%")
            )
        ])
            ->get();


        return response()->json([
            'branches' => $all
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
        $validate = $request->validate([
            'branch_code'        =>  ['required', 'string', 'regex:/^[A-Z0-9\- ]+$/', Rule::unique('branches', 'branch_code')],
            'branch_name'        =>  ['required', 'string'],
            'branch'             =>  ['required', 'string'],
            'acronym'            =>  ['required', 'string', 'regex:/^[A-Z]+$/']
        ]);

        Branch::create([
            'branch_code'        => $validate['branch_code'],
            'branch_name'        => $validate['branch_name'],
            'branch'             => $validate['branch'],
            'acronym'            => $validate['acronym']
        ]);

        return response()->json([
            'message'       => 'Branch Successfully Created'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        return response()->json([
            'branch'        =>  $branch
        ]);
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
