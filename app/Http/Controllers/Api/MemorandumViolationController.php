<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemorandumViolation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MemorandumViolationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year');
        $page = $request->input('per_page',10);

        $memos = MemorandumViolation::select(
                                            [
                                                'id',
                                                'user_id',
                                                'violation_date',
                                                'violation_title',
                                                'offense',
                                                'sanction'
                                            ]
                                        )
                                        ->with(['user:id,fname,lname'])
                                        ->when($month, fn($q) => $q->whereMonth('violation_date', $month))
                                        ->when($year, fn($q) => $q->whereYear('violation_date', $year))
                                        ->search($search)
                                        ->paginate($page);

        $years = MemorandumViolation::selectRaw('YEAR(violation_date) as years')->groupByRaw('YEAR(violation_date)')->get();

        return response()->json(
            [
                'memos'   => $memos,
                'years'   => $years
            ],
            200
        );
    }

    public function auth_index(Request $request)
    {
        $auth_user = Auth::user();

        $search = $request->input('search');
        $month = $request->input('month');
        $page = $request->input('per_page',10);

        $memos = MemorandumViolation::where('user_id', $auth_user->id)
                   ->when( $search, fn ($q) => $q->whereLike('violation_title', "%{$search}%"))
                   ->when( $month, fn ($q) => $q->whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", $month))
                   ->paginate($page);

        return response()->json(
            [
                'memos'   => $memos
            ],
            200
        );
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
        $validate = $request->validate(
            [
                'id'                   => ['required', 'numeric', Rule::exists(User::class, 'id')],
                'title'                => ['required', 'string'],
                'violation_date'       => ['required', 'date'],
                'offense'              => ['required', 'string'],
                'sanction'             => ['nullable','string']
            ]
        );

        MemorandumViolation::create(
            [
                'user_id'            =>  $validate['id'],
                'violation_title'    =>  $validate['title'],
                'violation_date'     =>  $validate['violation_date'],
                'offense'            =>  $validate['offense'],
                'sanction'           =>  $validate['sanction']
            ]
        );

        return response()->json(
            [
               'message'   => 'Memo stored successfully'
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(MemorandumViolation $MemorandumViolation)
    {
        return response()->json(
            [
                'memos' => $MemorandumViolation
            ],
            200
        );

    }

    public function show_perUser(?int $id)
    {
        $memos = MemorandumViolation::where('user_id', $id )
                                        ->get(
                                            [
                                                'id',
                                                'user_id',
                                                'violation_title',
                                                'violation_date',
                                                'offense',
                                                'sanction'
                                            ]
                                        );

        return response()->json(
            [
                'memos' => $memos
            ],
            200
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MemorandumViolation $MemorandumViolation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MemorandumViolation $memorandumViolation)
    {
        $validate = $request->validate(
            [
                'title'                => ['required', 'string'],
                'violation_date'       => ['required', 'date'],
                'offense'              => ['required', 'string'],
                'sanction'             => ['string']
            ]
        );

        $memorandumViolation->update(
            [
                'violation_title'    =>  $validate['title'],
                'violation_date'     =>  $validate['violation_date'],
                'offense'            =>  $validate['offense'],
                'sanction'           =>  $validate['sanction']
            ]
        );

        return response()->json(
            [
                'message'       =>  'Memo Update Successfully'
            ]
            ,201
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MemorandumViolation $memorandumViolation)
    {
        if(Auth::user()->roles()->where('name', 'hr')->exists())
        {
            $memorandumViolation->delete();

            return response()->json(
                [
                    'message'       =>  "Memorandum has been deleted."
                ]
                ,200
            );
        }

        return response()->json(
            [
                'message'       =>  "You must be assigned as an HR to delete this memorandum."
            ]
            ,403
        );
    }
}
