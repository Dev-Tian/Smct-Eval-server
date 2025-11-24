<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::all();

        return response()->json([
            'departments' => $departments
        ]);
    }

    public function getTotalEmployeesDepartments()
    {
        $all = Department::withCount([
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
            'departments'       => $all
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
            'department_name'     => ['required', 'string', 'alpha']
        ]);

        Department::create([
            'department_name'     => $validate['department_name']
        ]);

        return response()->json([
            'message'       =>  'Added Successfully '
        ], 201);
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
    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json([
            'message'       =>  'Department Deleted Successfully'
        ], 200);
    }
}
