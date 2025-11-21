<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EvaluationsNotif;
use Illuminate\Support\Facades\Notification;

use function Symfony\Component\Clock\now;

class UsersEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

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
            ->when($status, fn($q)  => $q->where('status', $status))
            ->when($quarter, fn($q) => $q->where('quarter_of_submission_id', $quarter))
            ->when($year, fn($q)    => $q->whereYear('created_at', $year))
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

    public function store(Request $request, User $user)
    {
        $auth_user_evaluator = Auth::user();

        $validated  = $request->validate([
            'category'                              => ['required', 'string'],
            'rating'                                => ['required'],
            'coverageFrom'                          => ['required'],
            'coverageTo'                            => ['required'],
            'reviewTypeProbationary'                => ['nullable', 'numeric'],
            'reviewTypeRegular'                     => ['nullable', 'string'],
            'reviewTypeOthersImprovement'           => ['nullable', 'boolean'],
            'reviewTypeOthersCustom'                => ['nullable', 'string'],
            'priorityArea1'                         => ['nullable', 'string'],
            'priorityArea2'                         => ['nullable', 'string'],
            'priorityArea3'                         => ['nullable', 'string'],
            'remarks'                               => ['nullable', 'string'],
            'overallComments'                       => ['nullable', 'string'],

            // validation for child job_knowledge
            'job_knowledge'                         => ['required', 'array'],
            'job_knowledge.*.question_number'       => ['required', 'numeric'],
            'job_knowledge.*.score'                 => ['required', 'numeric'],
            'job_knowledge.*.comment'               => ['nullable', 'string'],

            // validation for child quality_of_works
            'quality_of_works'                      => ['required', 'array'],
            'quality_of_works.*.question_number'    => ['required', 'numeric'],
            'quality_of_works.*.score'              => ['required', 'numeric'],
            'quality_of_works.*.comment'            => ['nullable', 'string'],

            // validation for child adaptability
            'adaptability'                          => ['required', 'array'],
            'adaptability.*.question_number'        => ['required', 'numeric'],
            'adaptability.*.score'                  => ['required', 'numeric'],
            'adaptability.*.comment'                => ['nullable', 'string'],

            // validation for child teamworks
            'teamworks'                             => ['required', 'array'],
            'teamworks.*.question_number'           => ['required', 'numeric'],
            'teamworks.*.score'                     => ['required', 'numeric'],
            'teamworks.*.comment'                   => ['nullable', 'string'],

            // validation for child reliabilities
            'reliabilities'                         => ['required', 'array'],
            'reliabilities.*.question_number'       => ['required', 'numeric'],
            'reliabilities.*.score'                 => ['required', 'numeric'],
            'reliabilities.*.comment'               => ['nullable', 'string'],

            // validation for child ethicals
            'ethicals'                              => ['required', 'array'],
            'ethicals.*.question_number'            => ['required', 'numeric'],
            'ethicals.*.score'                      => ['required', 'numeric'],
            'ethicals.*.explanation'                => ['nullable', 'string'],

            // validation for child customer_services
            'customer_services'                     => ['required', 'array'],
            'customer_services.*.question_number'   => ['required', 'numeric'],
            'customer_services.*.score'             => ['required', 'numeric'],
            'customer_services.*.explanation'       => ['nullable', 'string'],
        ]);

        $submission  =  UsersEvaluation::create([
            'employee_id'                       =>  $user->id,
            'evaluator_id'                      =>  $auth_user_evaluator->id,
            'category'                          =>  $validated['category'],
            'rating'                            =>  $validated['rating'],
            'coverageFrom'                      =>  $validated['coverageFrom'],
            'coverageTo'                        =>  $validated['coverageTo'],
            'reviewTypeProbationary'            =>  $validated['reviewTypeProbationary'] ?? null,
            'reviewTypeRegular'                 =>  $validated['reviewTypeRegular'] ?? null,
            'reviewTypeOthersImprovement'       =>  $validated['reviewTypeOthersImprovement'] ?? null,
            'reviewTypeOthersCustom'            =>  $validated['reviewTypeOthersCustom'] ?? null,
            'priorityArea1'                     =>  $validated['priorityArea1'] ?? null,
            'priorityArea2'                     =>  $validated['priorityArea2'] ?? null,
            'priorityArea3'                     =>  $validated['priorityArea3'] ?? null,
            'remarks'                           =>  $validated['remarks'] ?? null,
            'overallComments'                   =>  $validated['overallComments'] ?? null,
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

        foreach ($childMappings as $requestKey => $relation) {
            foreach ($request->$requestKey as $item) {
                $submission->$relation()->create($item);
            }
        }


        $user->notify(new EvaluationsNotif(
            'pending',
            $submission->id,
            $submission->employee_id,
            $user->fname . ' ' . $user->lname,
            $submission->evaluator_id,
            $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname,
            "An evaluation submitted by " . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . " is awaiting your approval.",
        ));

        $notificationData =  new EvaluationsNotif(
            'pending',
            $submission->id,
            $submission->employee_id,
            $user->fname . ' ' . $user->lname,
            $submission->evaluator_id,
            $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname,
            "A new evaluation submitted for " . $user->fname . ' ' . $user->lname . " was submitted by " . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname,
        );

        User::with('roles')
            ->whereHas(
                'roles',
                fn($q)
                =>
                $q->where('name', 'hr')->orWhere('name', 'admin')
            )
            ->chunk(
                100,
                function ($hrs) use ($notificationData) {
                    Notification::send($hrs, $notificationData);
                }
            );

        return response()->json([
            'message'       =>  'Submitted Successfully'
        ], 201);
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
        $search = $request->input('search');
        $status = $request->input('status');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

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
            ->when($status,  fn($q) =>  $q->where('status', $status))
            ->when($quarter, fn($q) =>  $q->where('quarter_of_submission_id', $quarter))
            ->when($year,    fn($q) =>  $q->whereYear('created_at', $year))
            ->get();

        return response()->json([
            'myEval_as_Employee'         =>   $user_eval
        ], 200);
    }

    public function getEvalAuthEvaluator(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

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
            ->when($status, fn($q) =>  $q->where('status', $status))
            ->when($quarter, fn($q) =>  $q->where('quarter_of_submission_id', $quarter))
            ->when($year,   fn($q) =>  $q->whereYear('created_at', $year))
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
    public function approvedByEmployee(UsersEvaluation $usersEvaluation)
    {
        $usersEvaluation->update([
            'status'                => 'completed',
            'employeeApprovedAt'    => now()
        ]);

        $evaluator = User::findOrFail($usersEvaluation->evaluator_id);
        $auth_user_employee = Auth::user();


        $evaluator->notify(new EvaluationsNotif(
            'completed',
            $usersEvaluation->id,
            $usersEvaluation->employee_id,
            $auth_user_employee->fname . ' ' . $auth_user_employee->lname,
            $usersEvaluation->evaluator_id,
            $evaluator->fname . ' ' . $evaluator->lname,
            "Your submitted evaluation for " . $auth_user_employee->fname . ' ' . $auth_user_employee->lname . " has been successfully approved.",
        ));

        $notificationData =  new EvaluationsNotif(
            'completed',
            $usersEvaluation->id,
            $usersEvaluation->employee_id,
            $auth_user_employee->fname . ' ' . $auth_user_employee->lname,
            $usersEvaluation->evaluator_id,
            $evaluator->fname . ' ' . $evaluator->lname,
            "An evaluation submitted by " . $evaluator->fname . ' ' . $evaluator->lname . " for " . $auth_user_employee->fname . ' ' . $auth_user_employee->lname . " has been approved ",
        );

        User::with('roles')
            ->whereHas(
                'roles',
                fn($q)
                =>
                $q->where('name', 'hr')->orWhere('name', 'admin')
            )
            ->chunk(
                100,
                function ($hrs) use ($notificationData) {
                    Notification::send($hrs, $notificationData);
                }
            );

        return response()->json([
            'message'       => 'Evaluation approved by employee successfully'
        ], 200);
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
