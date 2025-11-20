<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Notifications\EvaluationsNotif;

use function Symfony\Component\Clock\now;

class UsersEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $status    = $request->input('status');
        $quarter   = $request->input('quarter');
        $year      = $request->input('year');

        $all_evaluations = UsersEvaluation::with(
            'employee',
            'evaluator',
            'quarterUsersEvaluations',
            'jobKnowledge',
            'adaptability',
            'qualityOfWorks',
            'teamworks',
            'reliabilities',
            'ethicals',
            'customerServices'
        )
        ->orderBy('id', 'desc')
        ->search($search)
        ->when($status, fn($q)    => $q->where('status', $status))
        ->when($quarter, fn($q)   => $q->where('quarter_of_submission_id', $quarter))
        ->when($year, fn($q)      => $q->whereYear('created_at', $year))
        ->get();

        return response()->json([
            'Evaluations'   =>  $all_evaluations
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

    public function store(Request $request,User $user)
    {
        $auth_user = Auth::user();


        //  expected load----
        //  "job_knowledge": [
        //     { "question_number": 1, "score": 5, "comment": "Good" }
        // ]

        $validated  = $request->validate([
            'category'                              => ['required', 'string'],
            'rating'                                => ['required'],
            'coverageFrom'                          => ['required'],
            'coverageTo'                            => ['required'],
            'reviewTypeProbationary'                => ['nullable', 'numeric'],
            'reviewTypeRegular'                     => ['nullable','string'],
            'reviewTypeOthersImprovement'           => ['nullable','boolean'],
            'reviewTypeOthersCustom'                => ['nullable','string'],
            'priorityArea1'                         => ['nullable','string'],
            'priorityArea2'                         => ['nullable','string'],
            'priorityArea3'                         => ['nullable','string'],
            'remarks'                               => ['nullable','string'],
            'overallComments'                       => ['nullable','string'],

            // validation for child job_knowledge
            'job_knowledge'                         => ['required','array'],
            'job_knowledge.*.question_number'       => ['required','numeric'],
            'job_knowledge.*.score'                 => ['required','numeric'],
            'job_knowledge.*.comment'               => ['nullable','string'],

            // validation for child quality_of_works
            'quality_of_works'                      => ['required','array'],
            'quality_of_works.*.question_number'    => ['required','numeric'],
            'quality_of_works.*.score'              => ['required','numeric'],
            'quality_of_works.*.comment'            => ['nullable','string'],

            // validation for child adaptability
            'adaptability'                          => ['required','array'],
            'adaptability.*.question_number'        => ['required','numeric'],
            'adaptability.*.score'                  => ['required','numeric'],
            'adaptability.*.comment'                => ['nullable','string'],

            // validation for child teamworks
            'teamworks'                             => ['required','array'],
            'teamworks.*.question_number'           => ['required','numeric'],
            'teamworks.*.score'                     => ['required','numeric'],
            'teamworks.*.comment'                   => ['nullable','string'],

            // validation for child reliabilities
            'reliabilities'                         => ['required','array'],
            'reliabilities.*.question_number'       => ['required','numeric'],
            'reliabilities.*.score'                 => ['required','numeric'],
            'reliabilities.*.comment'               => ['nullable','string'],

            // validation for child ethicals
            'ethicals'                              => ['required','array'],
            'ethicals.*.question_number'            => ['required','numeric'],
            'ethicals.*.score'                      => ['required','numeric'],
            'ethicals.*.explanation'                => ['nullable','string'],

            // validation for child customer_services
            'customer_services'                     => ['required','array'],
            'customer_services.*.question_number'   => ['required','numeric'],
            'customer_services.*.score'             => ['required','numeric'],
            'customer_services.*.explanation'       => ['nullable','string'],
        ]);

        $submission  =  UsersEvaluation::create([
            'employee_id'                       =>  $user->id,
            'evaluator_id'                      =>  $auth_user->id,
            'category'                          =>  $validated['category'],
            'rating'                            =>  $validated['rating'],
            'coverageFrom'                      =>  $validated['coverageFrom'],
            'coverageTo'                        =>  $validated['coverageTo'],
            'reviewTypeProbationary'            =>  $validated['reviewTypeProbationary'],
            'reviewTypeRegular'                 =>  $validated['reviewTypeRegular'],
            'reviewTypeOthersImprovement'       =>  $validated['reviewTypeOthersImprovement'],
            'reviewTypeOthersCustom'            =>  $validated['reviewTypeOthersCustom'],
            'priorityArea1'                     =>  $validated['priorityArea1'],
            'priorityArea2'                     =>  $validated['priorityArea2'],
            'priorityArea3'                     =>  $validated['priorityArea3'],
            'remarks'                           =>  $validated['remarks'],
            'overallComments'                   =>  $validated['overallComments'],
            'evaluatorApprovedAt'               =>  now()
        ]);

            $childMappings = [
                'job_knowledge'     => 'jobKnowledge',
                'quality_of_works'  => 'qualityOfWorks',
                'adaptability'      => 'adaptability',
                'teamworks'         => 'teamworks',
                'reliabilities'     => 'reliabilities',
                'ethicals'          => 'ethicals',
                'customer_services' => 'customerServices',
            ];

            foreach($childMappings as $requestKey => $relation) {
                foreach($request->$requestKey as $item){
                    $submission->$relation()->create($item);
                }
            }

        $params =  new EvaluationsNotif(
                            'pending',
                            $submission->id ,
                            $submission->employee_id,
                            $submission->employee_id,
                            "You have a evaluation to approve"
                );

        $employee = User::findOrFail($submission->employee_id);
        $employee->notify($params);

        $hrs = User::with('roles')
            ->whereRelation('roles','name','hr')
            ->chunk(100,
                function($hrs) use ($params){
                    Notification::send($hrs, $params);
                }
        );

        return response()->json([
            'message'       =>  'Submitted Successfully'
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UsersEvaluation $usersEvaluation)
    {
        $user_eval = $usersEvaluation->load(
            'employee',
            'evaluator',
            'quarterUsersEvaluations',
            'jobKnowledge',
            'adaptability',
            'qualityOfWorks',
            'teamworks',
            'reliabilities',
            'ethicals',
            'customerServices'
        )
        ->orderBy('id', 'desc');

        return response()->json([
            'user_eval'         =>   $user_eval
        ], 200);
    }

    public function getMyEvalAuthEmployee(Request $request)
    {
        $search     = $request->input('search');
        $status     = $request->input('status');
        $quarter    = $request->input('quarter');
        $year       = $request->input('year');

        $user = Auth::user();
        $user_eval = UsersEvaluation::with([
            'employee',
            'evaluator',
            'quarterUsersEvaluations',
            'jobKnowledge',
            'adaptability',
            'qualityOfWorks',
            'teamworks',
            'reliabilities',
            'ethicals',
            'customerServices'
        ])
        ->orderBy('id', 'desc')
        ->where('employee_id', $user->id)
        ->search($search)
        ->when($status,     fn($q)    =>  $q->where('status', $status))
        ->when($quarter,    fn($q)    =>  $q->where('quarter_of_submission_id', $quarter))
        ->when($year,       fn($q)    =>  $q->whereYear('created_at', $year))
        ->get();

        return response()->json([
            'myEval_as_Employee'         =>   $user_eval
        ], 200);
    }

    public function getEvalAuthEvaluator(Request $request)
    {
        $search     = $request->input('search');
        $status     = $request->input('status');
        $quarter    = $request->input('quarter');
        $year       = $request->input('year');

        $user = Auth::user();
        $user_eval = UsersEvaluation::with([
            'employee',
            'evaluator',
            'quarterUsersEvaluations',
            'jobKnowledge',
            'adaptability',
            'qualityOfWorks',
            'teamworks',
            'reliabilities',
            'ethicals',
            'customerServices'
        ])
        ->orderBy('id', 'desc')
        ->where('evaluator_id', $user->id)
        ->search($search)
        ->when($status,     fn($q)     =>  $q->where('status', $status))
        ->when($quarter,    fn($q)     =>  $q->where('quarter_of_submission_id', $quarter))
        ->when($year,       fn($q)     =>  $q->whereYear('created_at', $year))
        ->get();

        return response()->json([
            'myEval_as_Employee'         =>   $user_eval
        ], 200);
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
    public function destroy(UsersEvaluation $usersEvaluation)
    {
        $usersEvaluation->delete();

        return response()->json([
            'message'       => 'Deleted Successfully'
        ], 200);
    }
}
