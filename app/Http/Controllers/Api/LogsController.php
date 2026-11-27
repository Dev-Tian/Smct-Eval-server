<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Logs;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page',10);
        $search = $request->input('search');
        $type = $request->input('type');

        $logs = Logs::with(['deletedBy' => fn($q)=>$q->select(['id','fname', 'lname'])])
                    ->when(($type && $type != "all"), fn($q) => $q->where('type', $type))
                    ->when($search,
                        fn($q)
                        =>
                        $q->where(
                            fn($w)
                            =>
                            $w->whereAny(['info', 'type'],'LIKE', "%{$search}%")
                                ->orWhereHas('deletedBy',
                                    fn($q)
                                    =>
                                    $q->whereRaw("CONCAT(fname, ' ', lname) LIKE ?", ["%{$search}%"])
                                        ->orWhereRaw("CONCAT(lname, ' ', fname) LIKE ?", ["%{$search}%"])
                                )
                        )
                    )
                    ->paginate($perPage);

        return response()->json(
            [
                'logs'  => $logs
            ]
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Logs $log)
    {
        return response()->json(
            [
                'logs' => $log->load(['deletedBy:id,fname,lname'])
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logs $logs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Logs $logs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logs $logs)
    {
        //
    }
}
