<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use function Pest\Laravel\json;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::all();

        return response()->json([
            'positions' => $positions
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
            'label'     =>      ['required', 'string', Rule::unique('positions', 'label')]
        ]);

        Position::create([
            'label'     =>  $validate['label'],
            'value'     =>  $validate['label']
        ]);

        return response()->json([
            'message'       =>  'Position Successfully created'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        return response()->json([
            'position'  =>  $position
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $validate = $request->validate([
           'label'     =>      ['required', 'string', Rule::unique('positions', 'label')]
        ]);

        $position->update([
            'label'     =>  $validate['label'],
            'value'     =>  $validate['label']
        ]);

        return response()->json([
            'message'       =>  ucfirst($validate['label']) . ' position has successfully updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( Position $position)
    {
        $positionIndicator = $position->label;
        $position->delete();

        return response()->json([
            'message'       =>    ucfirst($positionIndicator) . " position has been succesfully deleted"
        ]);
    }
}
