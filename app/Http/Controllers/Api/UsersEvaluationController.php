<?php

namespace App\Http\Controllers\Api;

use App\Enum\EvalStatus;
use App\Enum\QuarterDateRange;
use App\Http\Controllers\Controller;
use App\Http\Requests\BranchBasic;
use App\Http\Requests\BranchBasicAreaManager;
use App\Http\Requests\BranchRankNFile;
use App\Http\Requests\HoBasic;
use App\Http\Requests\HoRankNFile;
use App\Models\Assign_approver;
use App\Models\User;
use App\Models\UsersEvaluation;
use App\Notifications\EvalNotifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class UsersEvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $quarter = $request->input('quarter');
        $year = $request->input('year');
        $rating = $request->input('rating');
        $branches = [];
        if(!empty($request->input('branch'))) {
            $branches = array_merge(explode(',',$request->input('branch'))) ;
        }

        $isHr = Auth::user()->hasRole('hr');

        $all_evaluations = UsersEvaluation::query()
            ->with(
                [
                    'employee:id,position_id,branch_id,fname,lname,emp_id,contact,date_hired,signature',
                    'employee.branch:id,branch_code,branch_name',
                    'employee.positions:id,label',
                    'evaluator:id,fname,lname,signature',
                    'evaluatorsHead:id,fname,lname,signature',
                ]
            )
            ->when($isHr, fn($q) => $q->whereIn('status', [EvalStatus::pending, EvalStatus::completed]))
            ->search($search)
            ->when($status,  fn($q) => $q->where('status', $status))
            ->when($quarter, fn($q) => $q->where(fn($sub) => $sub->where('reviewTypeRegular', $quarter)->orWhere('reviewTypeProbationary', $quarter)))
            ->when($year,
                fn($q)
                =>
                $q->where(
                    fn($r)
                    =>
                    $r->whereYear('coverageTo', $year)->orWhereYear('coverageFrom', $year)
                )
            )
            ->when($rating,  function ($q) use ($rating) {
                match ($rating) {
                    'poor'      => $q->where('rating', '<', 2.5),
                    'low'       => $q->where('rating', '<', 3),
                    'good'      => $q->whereBetween('rating', [3, 3.9]),
                    'excellent' => $q->where('rating', '>=', 4),
                    default     => $q->where('rating', 5),
                };
            })
            ->when(!empty($branches), function ($q) use ($branches) {
                $q->whereHas('employee', function ($sub) use ($branches) {
                    $sub->where(function ($query) use ($branches) {
                        $query->whereHas('branches', function ($q) use ($branches) {
                            $q->whereIn('branches.id', $branches);
                        })
                        ->orWhereHas('branch', function ($q) use ($branches) {
                            $q->whereIn('branches.id', $branches);
                        });
                    });
                });
            })
            ->latest('created_at')
            ->paginate($perPage);

        return response()->json(
            [
                'evaluations' => $all_evaluations,
            ],
            200
        );
    }

    public function getQuarters(User $user)
    {
        $data = UsersEvaluation::where('employee_id', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('reviewTypeProbationary')
                ->orWhereNotNull('reviewTypeRegular');
            })
            ->where(function($q) {
                $q->whereYear('coverageFrom', now()->year)
                ->orWhereYear('coverageTo', now()->year);
            })
            ->get(
                    [
                        'reviewTypeProbationary',
                        'reviewTypeRegular'
                    ]
                );

        return response()->json(
            [
                'data' => $data,
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

    // public function IndirectEvaluatorsHead(int $id)
    // {
    //        return DB::table('assigned_user')->where('employee_id', $id)->where('isIndirectEvaluator', true)->value('evaluator_id');
    // }

    public function getApprovers(int $id)
    {
        return Assign_approver::where('evaluator_id', $id)->get();
    }

    public function BranchRankNFile(BranchRankNFile $validated, User $user)
    {
        $auth_user_evaluator = Auth::user();

        $status = '';
        $approver1 = null;
        $approver2 = null;
        $approverModel = $this->getApprovers($auth_user_evaluator->id);

        if(!$approverModel || $approverModel->isEmpty()){
            $status = EvalStatus::pending;
        }else{
            $status = EvalStatus::pending_approval_1;
            $approver1 = $approverModel->firstWhere('sequence', 1)?->approver_id;
            $approver2 = $approverModel->firstWhere('sequence', 2)?->approver_id;
        }

        $evalDateFrom = $validated['coverageFrom'];
        $evalDateTo = $validated['coverageTo'];

        if(!empty($validated['reviewTypeRegular']))
        {
            [$evalDateFrom, $evalDateTo] = match($validated['reviewTypeRegular'])
           {
                "Q1"    =>  QuarterDateRange::Q1->range(),
                "Q2"    =>  QuarterDateRange::Q2->range(),
                "Q3"    =>  QuarterDateRange::Q3->range(),
                "Q4"    =>  QuarterDateRange::Q4->range(),
           };
        }

        $submission = UsersEvaluation::create(
            [
                'employee_id'                   => $user->id,
                'evaluator_id'                  => $auth_user_evaluator->id,
                'approver1_id'                  => $approver1,
                'approver2_id'                  => $approver2,
                'evaluationType'                => 'BranchRankNFile',
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performanceScore'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['reviewTypeProbationary'] ?: null,
                'reviewTypeRegular'             => $validated['reviewTypeRegular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['reviewTypeOthersImprovement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['reviewTypeOthersCustom'] ?: null,
                'priorityArea1'                 => $validated['priorityArea1'] ?: null,
                'priorityArea2'                 => $validated['priorityArea2'] ?: null,
                'priorityArea3'                 => $validated['priorityArea3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            $submission->jobKnowledge()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['jobKnowledgeScore' . $i],
                    'comment'               => $validated['jobKnowledgeComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            $submission->qualityOfWorks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['qualityOfWorkScore' . $i],
                    'comment'               => $validated['qualityOfWorkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->adaptability()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['adaptabilityScore' . $i],
                    'comment'               => $validated['adaptabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->teamworks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['teamworkScore' . $i],
                    'comment'               => $validated['teamworkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->reliabilities()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['reliabilityScore' . $i],
                    'comment'               => $validated['reliabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->ethicals()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['ethicalScore' . $i],
                    'explanation'           => $validated['ethicalExplanation' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            $submission->customerServices()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['customerServiceScore' . $i],
                    'explanation'           => $validated['customerServiceExplanation' . $i] ?: null,
                ]
            );
        }
        //notification for employee
        $user->notify(new EvalNotifications('An evaluation submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));

        //notification for admin and hr
        $notificationData = new EvalNotifications('A new evaluation submitted for ' . $user->fname . ' ' . $user->lname . ' was submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname);

        User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('name', 'hr')->orWhere('name', 'admin'))
            ->chunk(100, function ($hrs) use ($notificationData)
                {
                    Notification::send($hrs, $notificationData);
                }
            );

        return response()->json(
            [
                'message' => 'Submitted Successfully',
            ],
            201
        );
    }

    public function BranchBasicAreaManager(BranchBasicAreaManager $validated, User $user)
    {
        $auth_user_evaluator = Auth::user();

        $status = '';
        $approver1 = null;
        $approver2 = null;
        $approverModel = $this->getApprovers($auth_user_evaluator->id);

        if(!$approverModel || $approverModel->isEmpty()){
            $status = EvalStatus::pending;
        }else{
            $status = EvalStatus::pending_approval_1;
            $approver1 = $approverModel->firstWhere('sequence', 1)?->approver_id;
            $approver2 = $approverModel->firstWhere('sequence', 2)?->approver_id;
        }

        $evalDateFrom = $validated['coverageFrom'];
        $evalDateTo = $validated['coverageTo'];

        if(!empty($validated['reviewTypeRegular']))
        {
            [$evalDateFrom, $evalDateTo] = match($validated['reviewTypeRegular'])
           {
                "Q1"    =>  QuarterDateRange::Q1->range(),
                "Q2"    =>  QuarterDateRange::Q2->range(),
                "Q3"    =>  QuarterDateRange::Q3->range(),
                "Q4"    =>  QuarterDateRange::Q4->range(),
           };
        }

        $submission = UsersEvaluation::create(
            [
                'employee_id'                   => $user->id,
                'evaluator_id'                  => $auth_user_evaluator->id,
                'approver1_id'                  => $approver1,
                'approver2_id'                  => $approver2,
                'evaluationType'                => 'BranchBasicAreaManager',
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performanceScore'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['reviewTypeProbationary'] ?: null,
                'reviewTypeRegular'             => $validated['reviewTypeRegular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['reviewTypeOthersImprovement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['reviewTypeOthersCustom'] ?: null,
                'priorityArea1'                 => $validated['priorityArea1'] ?: null,
                'priorityArea2'                 => $validated['priorityArea2'] ?: null,
                'priorityArea3'                 => $validated['priorityArea3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            $submission->jobKnowledge()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['jobKnowledgeScore' . $i],
                    'comment'               => $validated['jobKnowledgeComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 12; $i++) {
            $submission->qualityOfWorks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['qualityOfWorkScore' . $i],
                    'comment'               => $validated['qualityOfWorkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->adaptability()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['adaptabilityScore' . $i],
                    'comment'               => $validated['adaptabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->teamworks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['teamworkScore' . $i],
                    'comment'               => $validated['teamworkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->reliabilities()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['reliabilityScore' . $i],
                    'comment'               => $validated['reliabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->ethicals()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['ethicalScore' . $i],
                    'explanation'           => $validated['ethicalExplanation' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 6; $i++) {
            $submission->managerialSkills()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['managerialSkillsScore' . $i],
                    'explanation'           => $validated['managerialSkillsExplanation' . $i] ?: null,
                ]
            );
        }
        //notification for employee
        $user->notify(new EvalNotifications('An evaluation submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));

        //notification for admin and hr
        $notificationData = new EvalNotifications('A new evaluation submitted for ' . $user->fname . ' ' . $user->lname . ' was submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname);

        User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('name', 'hr')->orWhere('name', 'admin'))
            ->chunk(100, function ($hrs) use ($notificationData) {
                Notification::send($hrs, $notificationData);
            });

        return response()->json(
            [
                'message' => 'Submitted Successfully',
            ],
            201
        );
    }

    public function BranchBasic(BranchBasic $validated, User $user)
    {
        $auth_user_evaluator = Auth::user();

        $status = '';
        $approver1 = null;
        $approver2 = null;
        $approverModel = $this->getApprovers($auth_user_evaluator->id);

        if(!$approverModel || $approverModel->isEmpty()){
            $status = EvalStatus::pending;
        }else{
            $status = EvalStatus::pending_approval_1;
            $approver1 = $approverModel->firstWhere('sequence', 1)?->approver_id;
            $approver2 = $approverModel->firstWhere('sequence', 2)?->approver_id;
        }


        $evalDateFrom = $validated['coverageFrom'];
        $evalDateTo = $validated['coverageTo'];

        if(!empty($validated['reviewTypeRegular']))
        {

        [$evalDateFrom, $evalDateTo] = match($validated['reviewTypeRegular'])
           {
                "Q1"    =>  QuarterDateRange::Q1->range(),
                "Q2"    =>  QuarterDateRange::Q2->range(),
                "Q3"    =>  QuarterDateRange::Q3->range(),
                "Q4"    =>  QuarterDateRange::Q4->range(),
           };
        }

        $submission = UsersEvaluation::create(
            [
                'employee_id'                   => $user->id,
                'evaluator_id'                  => $auth_user_evaluator->id,
                'approver1_id'                  => $approver1,
                'approver2_id'                  => $approver2,
                'evaluationType'                => 'BranchBasic',
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performanceScore'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['reviewTypeProbationary'] ?: null,
                'reviewTypeRegular'             => $validated['reviewTypeRegular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['reviewTypeOthersImprovement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['reviewTypeOthersCustom'] ?: null,
                'priorityArea1'                 => $validated['priorityArea1'] ?: null,
                'priorityArea2'                 => $validated['priorityArea2'] ?: null,
                'priorityArea3'                 => $validated['priorityArea3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            $submission->jobKnowledge()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['jobKnowledgeScore' . $i],
                    'comment'               => $validated['jobKnowledgeComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 12; $i++) {
            $submission->qualityOfWorks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['qualityOfWorkScore' . $i],
                    'comment'               => $validated['qualityOfWorkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->adaptability()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['adaptabilityScore' . $i],
                    'comment'               => $validated['adaptabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->teamworks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['teamworkScore' . $i],
                    'comment'               => $validated['teamworkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->reliabilities()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['reliabilityScore' . $i],
                    'comment'               => $validated['reliabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->ethicals()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['ethicalScore' . $i],
                    'explanation'           => $validated['ethicalExplanation' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            $submission->customerServices()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['customerServiceScore' . $i],
                    'explanation'           => $validated['customerServiceExplanation' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 6; $i++) {
            $submission->managerialSkills()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['managerialSkillsScore' . $i],
                    'explanation'           => $validated['managerialSkillsExplanation' . $i] ?: null,
                ]
            );
        }
        //notification for employee
        $user->notify(new EvalNotifications('An evaluation submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));

        //notification for admin and hr
        $notificationData = new EvalNotifications('A new evaluation submitted for ' . $user->fname . ' ' . $user->lname . ' was submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname);

        User::with('roles')
            ->whereHas('roles',
                fn($q)
                =>
                $q->where('name', 'hr')
                  ->orWhere('name', 'admin')
            )
            ->chunk(100, function ($hrs) use ($notificationData)
                {
                    Notification::send($hrs, $notificationData);
                }
            );

        return response()->json(
            [
                'message' => 'Submitted Successfully',
            ],
            201
        );
    }

    public function HoRankNFile(HoRankNFile $validated, User $user)
    {
        $auth_user_evaluator = Auth::user();

        $status = '';
        $approver1 = null;
        $approver2 = null;
        $approverModel = $this->getApprovers($auth_user_evaluator->id);

        if(!$approverModel || $approverModel->isEmpty()){
            $status = EvalStatus::pending;
        }else{
            $status = EvalStatus::pending_approval_1;
            $approver1 = $approverModel->firstWhere('sequence', 1)?->approver_id;
            $approver2 = $approverModel->firstWhere('sequence', 2)?->approver_id;
        }

        $evalDateFrom = $validated['coverageFrom'];
        $evalDateTo = $validated['coverageTo'];

        if(!empty($validated['reviewTypeRegular']))
        {

        [$evalDateFrom, $evalDateTo] = match($validated['reviewTypeRegular'])
           {
                "Q1"    =>  QuarterDateRange::Q1->range(),
                "Q2"    =>  QuarterDateRange::Q2->range(),
                "Q3"    =>  QuarterDateRange::Q3->range(),
                "Q4"    =>  QuarterDateRange::Q4->range(),
           };
        }

        $submission = UsersEvaluation::create(
            [
                'employee_id'                   => $user->id,
                'evaluator_id'                  => $auth_user_evaluator->id,
                'approver1_id'                  => $approver1,
                'approver2_id'                  => $approver2,
                'evaluationType'                => 'HoRankNFile',
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performanceScore'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['reviewTypeProbationary'] ?: null,
                'reviewTypeRegular'             => $validated['reviewTypeRegular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['reviewTypeOthersImprovement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['reviewTypeOthersCustom'] ?: null,
                'priorityArea1'                 => $validated['priorityArea1'] ?: null,
                'priorityArea2'                 => $validated['priorityArea2'] ?: null,
                'priorityArea3'                 => $validated['priorityArea3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            $submission->jobKnowledge()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['jobKnowledgeScore' . $i],
                    'comment'               => $validated['jobKnowledgeComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->qualityOfWorks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['qualityOfWorkScore' . $i],
                    'comment'               => $validated['qualityOfWorkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->adaptability()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['adaptabilityScore' . $i],
                    'comment'               => $validated['adaptabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->teamworks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['teamworkScore' . $i],
                    'comment'               => $validated['teamworkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->reliabilities()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['reliabilityScore' . $i],
                    'comment'               => $validated['reliabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->ethicals()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['ethicalScore' . $i],
                    'explanation'           => $validated['ethicalExplanation' . $i] ?: null,
                ]
            );
        }

        //notification for employee
        $user->notify(new EvalNotifications('An evaluation submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));

        //notification for admin and hr
        $notificationData = new EvalNotifications('A new evaluation submitted for ' . $user->fname . ' ' . $user->lname . ' was submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname);

        User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('name', 'hr')->orWhere('name', 'admin'))
            ->chunk(100, function ($hrs) use ($notificationData)
                {
                    Notification::send($hrs, $notificationData);
                }
            );

        return response()->json(
            [
                'message' => 'Submitted Successfully',
            ],
            201
        );
    }

    public function HoBasic(HoBasic $validated, User $user)
    {
        $auth_user_evaluator = Auth::user();

        $status = '';
        $approver1 = null;
        $approver2 = null;
        $approverModel = $this->getApprovers($auth_user_evaluator->id);

        if(!$approverModel || $approverModel->isEmpty()){
            $status = EvalStatus::pending;
        }else{
            $status = EvalStatus::pending_approval_1;
            $approver1 = $approverModel->firstWhere('sequence', 1)?->approver_id;
            $approver2 = $approverModel->firstWhere('sequence', 2)?->approver_id;
        }

        $evalDateFrom = $validated['coverageFrom'];
        $evalDateTo = $validated['coverageTo'];

        if(!empty($validated['reviewTypeRegular']))
        {

        [$evalDateFrom, $evalDateTo] = match($validated['reviewTypeRegular'])
           {
                "Q1"    =>  QuarterDateRange::Q1->range(),
                "Q2"    =>  QuarterDateRange::Q2->range(),
                "Q3"    =>  QuarterDateRange::Q3->range(),
                "Q4"    =>  QuarterDateRange::Q4->range(),
           };
        }

        $submission = UsersEvaluation::create(
            [
                'employee_id'                   => $user->id,
                'evaluator_id'                  => $auth_user_evaluator->id,
                'approver1_id'                  => $approver1,
                'approver2_id'                  => $approver2,
                'evaluationType'                => 'HoBasic',
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performanceScore'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['reviewTypeProbationary'] ?: null,
                'reviewTypeRegular'             => $validated['reviewTypeRegular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['reviewTypeOthersImprovement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['reviewTypeOthersCustom'] ?: null,
                'priorityArea1'                 => $validated['priorityArea1'] ?: null,
                'priorityArea2'                 => $validated['priorityArea2'] ?: null,
                'priorityArea3'                 => $validated['priorityArea3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        for ($i = 1; $i <= 3; $i++) {
            $submission->jobKnowledge()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['jobKnowledgeScore' . $i],
                    'comment'               => $validated['jobKnowledgeComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->qualityOfWorks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['qualityOfWorkScore' . $i],
                    'comment'               => $validated['qualityOfWorkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->adaptability()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['adaptabilityScore' . $i],
                    'comment'               => $validated['adaptabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $submission->teamworks()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['teamworkScore' . $i],
                    'comment'               => $validated['teamworkComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->reliabilities()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['reliabilityScore' . $i],
                    'comment'               => $validated['reliabilityComments' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 4; $i++) {
            $submission->ethicals()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['ethicalScore' . $i],
                    'explanation'           => $validated['ethicalExplanation' . $i] ?: null,
                ]
            );
        }

        for ($i = 1; $i <= 6; $i++) {
            $submission->managerialSkills()->create(
                [
                    'users_evaluation_id'   => $submission->id,
                    'question_number'       => $i,
                    'score'                 => $validated['managerialSkillsScore' . $i],
                    'explanation'           => $validated['managerialSkillsExplanation' . $i] ?: null,
                ]
            );
        }

        //notification for employee
        $user->notify(new EvalNotifications('An evaluation submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));

        //notification for admin and hr
        $notificationData = new EvalNotifications('A new evaluation submitted for ' . $user->fname . ' ' . $user->lname . ' was submitted by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname);

        User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('name', 'hr')->orWhere('name', 'admin'))
            ->chunk(100, function ($hrs) use ($notificationData)
                {
                    Notification::send($hrs, $notificationData);
                }
            );

        return response()->json(
            [
                'message' => 'Submitted Successfully',
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(UsersEvaluation $usersEvaluation)
    {
        return response()->json(
            [
                'user_eval' => $usersEvaluation->loadRelations(),
            ],
            200
        );
    }

    public function getMyEvalAuthEmployee(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

        $user = Auth::user();
        $user_eval = UsersEvaluation::with(
                [
                    'employee:id,position_id,branch_id,fname,lname,emp_id,contact,date_hired,signature',
                    'employee.branch:id,branch_code,branch_name',
                    'employee.positions:id,label',
                    'evaluator:id,fname,lname,signature',
                    'evaluatorsHead:id,fname,lname,signature',
                    'jobKnowledge',
                    'adaptability',
                    'qualityOfWorks',
                    'teamworks',
                    'reliabilities',
                    'ethicals',
                    'customerServices'
                ]
            )
            ->where('employee_id', $user->id)
            ->whereIn('status', [EvalStatus::pending, EvalStatus::completed])
            ->search($search)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when(
                $quarter,
                fn($q) => $q->where(function ($subq) use ($quarter) {
                    match ($quarter) {
                        'Others'    => $subq->whereNot('reviewTypeOthersImprovement', 0)->orWhereNotNull('reviewTypeOthersCustom'),
                        default     => $subq->where('reviewTypeProbationary', $quarter)->orWhere('reviewTypeRegular', $quarter),
                    };
                })
            )
            ->when($year, fn($q) => $q->where( fn($r)=> $r->whereYear('coverageFrom', $year)->orWhereYear('coverageTo', $year)))
            ->latest('created_at')
            ->paginate($perPage);

        $years = UsersEvaluation::selectRaw('YEAR(created_at) as year')->where('employee_id', $user->id)->groupBy('year')->get();

        return response()->json(
            [
                'myEval_as_Employee' => $user_eval,
                'years' => $years,
            ],
            200
        );
    }

    public function getEvalAuthEvaluator(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $status = $request->input('status');
        $quarter = $request->input('quarter');
        $year = $request->input('year');

        $user = Auth::user();

        $user_eval = UsersEvaluation::query()
            ->with(
                [
                    'employee:id,position_id,branch_id,fname,lname,emp_id,contact,date_hired,signature',
                    'employee.branch:id,branch_code,branch_name',
                    'employee.positions:id,label',
                    'evaluator:id,fname,lname,signature',
                    'evaluatorsHead:id,fname,lname,signature',
                    'jobKnowledge',
                    'adaptability',
                    'qualityOfWorks',
                    'teamworks',
                    'reliabilities',
                    'ethicals',
                    'customerServices'
                ]
            )
            ->orWhereAny(['evaluator_id','evaluator_head_id'], $user->id)
            ->search($search)
            ->when($status, fn($q) => $q->where('status', $status))
            ->when(
                $quarter,
                fn($q) => $q->where(function ($subq) use ($quarter) {
                    match ($quarter) {
                        'Others'    => $subq->whereNot('reviewTypeOthersImprovement', false)->orWhereNotNull('reviewTypeOthersCustom'),
                        default     => $subq->where('reviewTypeProbationary', $quarter)->orWhere('reviewTypeRegular', $quarter),
                    };
                }),
            )
            ->when($year, fn($q) => $q->where( fn($r)=> $r->whereYear('coverageFrom', $year)->orWhereYear('coverageTo', $year)))
            ->latest('created_at')
            ->paginate($perPage);

        return response()->json(
            [
                'myEval_as_Evaluator' => $user_eval,
            ],
            200
        );
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
        $usersEvaluation->update(
            [
                'status' => 'completed',
                'employeeApprovedAt' => now(),
            ]
        );

        $evaluator = User::findOrFail($usersEvaluation->evaluator_id);
        $auth_user_employee = Auth::user();

        $evaluator->notify(new EvalNotifications('Your submitted evaluation for ' . $auth_user_employee->fname . ' ' . $auth_user_employee->lname . ' has been successfully approved.'));

        $notificationData = new EvalNotifications('An evaluation submitted by ' . $evaluator->fname . ' ' . $evaluator->lname . ' for ' . $auth_user_employee->fname . ' ' . $auth_user_employee->lname . ' has been approved ');

        User::with('roles')
            ->whereHas('roles', fn($q) => $q->where('name', 'hr')->orWhere('name', 'admin'))
            ->chunk(100, function ($hrs) use ($notificationData)
                {
                    Notification::send($hrs, $notificationData);
                }
            );

        return response()->json(
            [
                'message'   => 'Evaluation approved by employee successfully',
                'data'      => $usersEvaluation->loadRelations(),
            ],
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UsersEvaluation $usersEvaluation)
    {
        $authUser = Auth::user();
        if(($usersEvaluation->status !== "completed" && $usersEvaluation->employeeApprovedAt === null) || $authUser->roles->name === "admin"){
             $usersEvaluation->delete();

            return response()->json(
                [
                    'message' => 'Deleted Successfully',
                ],
                200
            );
        }

        return response()->json(
            [
                'message'       =>  "This evaluation can’t be deleted because both parties have approved it. Please refresh to view the latest updates."
            ]
            ,400
        );
    }

    public function acceptDraftEvaluation(UsersEvaluation $usersEvaluation)
    {
        $usersEvaluation->update(
            [
                'headApprovedAt'        =>  now(),
                'status'                =>  EvalStatus::pending
            ]
        );

        return response()->json(
            [
                'message'       =>  'Approval Successfully'
            ]
            ,201
        );
    }

    public function rejectDraftEvaluation(UsersEvaluation $usersEvaluation,Request $request)
    {
        $validated = $request->validate(
            [
                'note'      =>  ['required','string','max:20']
            ]
        );

        $usersEvaluation->update(
            [
                'status'            =>  EvalStatus::rejected,
                'noteIfRejected'    =>  $validated['note']
            ]
        );

        return response()->json(
            [
                'message'   =>  'Evaluation reject Successfully'
            ]
            ,201
        );


    }

    public function getAllYears()
    {
        $years = UsersEvaluation::select(DB::raw('YEAR(created_at) as year'))->distinct()->orderBy('year', 'DESC')->get();

        return response()->json(
            [
                'years' => $years,
            ],
            200
        );
    }

}
