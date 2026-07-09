<?php

namespace App\Http\Controllers\Api;

use App\Enum\EvalStatus;
use App\Enum\QuarterDateRange;
use App\Http\Controllers\Controller;
use App\Http\Requests\update\UpdateBranchBasic;
use App\Http\Requests\update\UpdateBranchBasicAreaManager;
use App\Http\Requests\update\UpdateBranchRankNFile;
use App\Http\Requests\update\UpdateHoBasic;
use App\Http\Requests\update\UpdateHoRankNFile;
use App\Models\UsersEvaluation;
use App\Notifications\EvalNotifications;
use Illuminate\Support\Facades\Auth;

class UpdateUsersEvaluationController extends Controller
{

    public function statusLogic(UsersEvaluation $usersEvaluation) : EvalStatus
    {
        return match (true) {

            $usersEvaluation->rejected_by_id == $usersEvaluation->approver2_id && !empty($usersEvaluation->approver2_id)
                => EvalStatus::pending_approval_2,

            ($usersEvaluation->rejected_by_id == $usersEvaluation->approver1_id && !empty($usersEvaluation->approver1_id)) || !empty($usersEvaluation->approver1_id)
                => EvalStatus::pending_approval_1,

            default => EvalStatus::pending,
        };
    }

    public function BranchBasic(UpdateBranchBasic $validated, UsersEvaluation $usersEvaluation)
    {
        $status = $this->statusLogic($usersEvaluation);

        $evalDateFrom = $validated['coverage_from'];
        $evalDateTo = $validated['coverage_to'];

        if(!empty($validated['review_type_regular']))
        {
            [$evalDateFrom, $evalDateTo] = match($validated['review_type_regular'])
            {
                    "Q1"    =>  QuarterDateRange::Q1->range(),
                    "Q2"    =>  QuarterDateRange::Q2->range(),
                    "Q3"    =>  QuarterDateRange::Q3->range(),
                    "Q4"    =>  QuarterDateRange::Q4->range(),
            };
        }

        $usersEvaluation->update(
            [
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performance_score'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['review_type_probationary'] ?: null,
                'reviewTypeRegular'             => $validated['review_type_regular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['review_type_others_improvement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['review_type_others_custom'] ?: null,
                'priorityArea1'                 => $validated['priority_area_1'] ?: null,
                'priorityArea2'                 => $validated['priority_area_2'] ?: null,
                'priorityArea3'                 => $validated['priority_area_3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        foreach ($validated['job_knowledge'] as $jobKnowledge) {
            $usersEvaluation->jobKnowledge()
                ->whereKey($jobKnowledge['id'])
                ->update([
                    'score'     => $jobKnowledge['score'],
                    'comment'   => $jobKnowledge['comment'],
                ]);
        }

        foreach ($validated['quality_of_works'] as $qualityOfWorks) {
            $usersEvaluation->qualityOfWorks()
                ->whereKey($qualityOfWorks['id'])
                ->update([
                    'score'     => $qualityOfWorks['score'],
                    'comment'   => $qualityOfWorks['comment'],
                ]);
        }

        foreach ($validated['adaptabilities'] as $adaptability) {
            $usersEvaluation->adaptability()
                ->whereKey($adaptability['id'])
                ->update([
                    'score'     => $adaptability['score'],
                    'comment'   => $adaptability['comment'],
                ]);
        }

        foreach ($validated['teamworks'] as $teamworks) {
            $usersEvaluation->teamworks()
                ->whereKey($teamworks['id'])
                ->update([
                    'score'     => $teamworks['score'],
                    'comment'   => $teamworks['comment'],
                ]);
        }

        foreach ($validated['reliabilities'] as $reliabilities) {
            $usersEvaluation->reliabilities()
                ->whereKey($reliabilities['id'])
                ->update([
                    'score'     => $reliabilities['score'],
                    'comment'   => $reliabilities['comment'],
                ]);
        }

        foreach ($validated['ethicals'] as $ethicals) {
            $usersEvaluation->ethicals()
                ->whereKey($ethicals['id'])
                ->update([
                    'score'         => $ethicals['score'],
                    'explanation'   => $ethicals['explanation'],
                ]);
        }

        foreach ($validated['customer_services'] as $customerServices) {
            $usersEvaluation->customerServices()
                ->whereKey($customerServices['id'])
                ->update([
                    'score'         => $customerServices['score'],
                    'explanation'   => $customerServices['explanation'],
                ]);
        }

        foreach ($validated['managerial_skills'] as $managerialSkills) {
            $usersEvaluation->managerialSkills()
                ->whereKey($managerialSkills['id'])
                ->update([
                    'score'         => $managerialSkills['score'],
                    'explanation'   => $managerialSkills['explanation'],
                ]);
        }

        $auth_user_evaluator = Auth::user();
        if(empty($usersEvaluation->rejected_by_id))
        {
            $usersEvaluation->employee->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your sign.'));
        }else{
            if($usersEvaluation->rejected_by_id == $usersEvaluation->approver1_id)
            {
                $usersEvaluation->approver1->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
            elseif($usersEvaluation->rejected_by_id == $usersEvaluation->approver2_id)
            {
                $usersEvaluation->approver2->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
        }

        return response()->json(
            [
                'message' => 'Updated Successfully',
            ],
            201
        );
    }

    public function BranchRankNFile(UpdateBranchRankNFile $validated, UsersEvaluation $usersEvaluation)
    {
        $status = $this->statusLogic($usersEvaluation);

        $evalDateFrom = $validated['coverage_from'];
        $evalDateTo = $validated['coverage_to'];

        if(!empty($validated['review_type_regular']))
        {
            [$evalDateFrom, $evalDateTo] = match($validated['review_type_regular'])
            {
                    "Q1"    =>  QuarterDateRange::Q1->range(),
                    "Q2"    =>  QuarterDateRange::Q2->range(),
                    "Q3"    =>  QuarterDateRange::Q3->range(),
                    "Q4"    =>  QuarterDateRange::Q4->range(),
            };
        }

        $usersEvaluation->update(
            [
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performance_score'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['review_type_probationary'] ?: null,
                'reviewTypeRegular'             => $validated['review_type_regular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['review_type_others_improvement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['review_type_others_custom'] ?: null,
                'priorityArea1'                 => $validated['priority_area_1'] ?: null,
                'priorityArea2'                 => $validated['priority_area_2'] ?: null,
                'priorityArea3'                 => $validated['priority_area_3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        foreach ($validated['job_knowledge'] as $jobKnowledge) {
            $usersEvaluation->jobKnowledge()
                ->whereKey($jobKnowledge['id'])
                ->update([
                    'score'     => $jobKnowledge['score'],
                    'comment'   => $jobKnowledge['comment'],
                ]);
        }

        foreach ($validated['quality_of_works'] as $qualityOfWorks) {
            $usersEvaluation->qualityOfWorks()
                ->whereKey($qualityOfWorks['id'])
                ->update([
                    'score'     => $qualityOfWorks['score'],
                    'comment'   => $qualityOfWorks['comment'],
                ]);
        }

        foreach ($validated['adaptabilities'] as $adaptability) {
            $usersEvaluation->adaptability()
                ->whereKey($adaptability['id'])
                ->update([
                    'score'     => $adaptability['score'],
                    'comment'   => $adaptability['comment'],
                ]);
        }

        foreach ($validated['teamworks'] as $teamworks) {
            $usersEvaluation->teamworks()
                ->whereKey($teamworks['id'])
                ->update([
                    'score'     => $teamworks['score'],
                    'comment'   => $teamworks['comment'],
                ]);
        }

        foreach ($validated['reliabilities'] as $reliabilities) {
            $usersEvaluation->reliabilities()
                ->whereKey($reliabilities['id'])
                ->update([
                    'score'     => $reliabilities['score'],
                    'comment'   => $reliabilities['comment'],
                ]);
        }

        foreach ($validated['ethicals'] as $ethicals) {
            $usersEvaluation->ethicals()
                ->whereKey($ethicals['id'])
                ->update([
                    'score'         => $ethicals['score'],
                    'explanation'   => $ethicals['explanation'],
                ]);
        }

        foreach ($validated['customer_services'] as $customerServices) {
            $usersEvaluation->customerServices()
                ->whereKey($customerServices['id'])
                ->update([
                    'score'         => $customerServices['score'],
                    'explanation'   => $customerServices['explanation'],
                ]);
        }

        $auth_user_evaluator = Auth::user();
        if(empty($usersEvaluation->rejected_by_id))
        {
            $usersEvaluation->employee->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your sign.'));
        }else{
            if($usersEvaluation->rejected_by_id == $usersEvaluation->approver1_id)
            {
                $usersEvaluation->approver1->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
            elseif($usersEvaluation->rejected_by_id == $usersEvaluation->approver2_id)
            {
                $usersEvaluation->approver2->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
        }

        return response()->json(
            [
                'message' => 'Updated Successfully',
            ],
            201
        );
    }

    public function HoBasic(UpdateHoBasic $validated, UsersEvaluation $usersEvaluation)
    {
        $status = $this->statusLogic($usersEvaluation);

        $evalDateFrom = $validated['coverage_from'];
        $evalDateTo = $validated['coverage_to'];

        if(!empty($validated['review_type_regular']))
        {
            [$evalDateFrom, $evalDateTo] = match($validated['review_type_regular'])
            {
                    "Q1"    =>  QuarterDateRange::Q1->range(),
                    "Q2"    =>  QuarterDateRange::Q2->range(),
                    "Q3"    =>  QuarterDateRange::Q3->range(),
                    "Q4"    =>  QuarterDateRange::Q4->range(),
            };
        }

        $usersEvaluation->update(
            [
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performance_score'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['review_type_probationary'] ?: null,
                'reviewTypeRegular'             => $validated['review_type_regular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['review_type_others_improvement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['review_type_others_custom'] ?: null,
                'priorityArea1'                 => $validated['priority_area_1'] ?: null,
                'priorityArea2'                 => $validated['priority_area_2'] ?: null,
                'priorityArea3'                 => $validated['priority_area_3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        foreach ($validated['job_knowledge'] as $jobKnowledge) {
            $usersEvaluation->jobKnowledge()
                ->whereKey($jobKnowledge['id'])
                ->update([
                    'score'     => $jobKnowledge['score'],
                    'comment'   => $jobKnowledge['comment'],
                ]);
        }

        foreach ($validated['quality_of_works'] as $qualityOfWorks) {
            $usersEvaluation->qualityOfWorks()
                ->whereKey($qualityOfWorks['id'])
                ->update([
                    'score'     => $qualityOfWorks['score'],
                    'comment'   => $qualityOfWorks['comment'],
                ]);
        }

        foreach ($validated['adaptabilities'] as $adaptability) {
            $usersEvaluation->adaptability()
                ->whereKey($adaptability['id'])
                ->update([
                    'score'     => $adaptability['score'],
                    'comment'   => $adaptability['comment'],
                ]);
        }

        foreach ($validated['teamworks'] as $teamworks) {
            $usersEvaluation->teamworks()
                ->whereKey($teamworks['id'])
                ->update([
                    'score'     => $teamworks['score'],
                    'comment'   => $teamworks['comment'],
                ]);
        }

        foreach ($validated['reliabilities'] as $reliabilities) {
            $usersEvaluation->reliabilities()
                ->whereKey($reliabilities['id'])
                ->update([
                    'score'     => $reliabilities['score'],
                    'comment'   => $reliabilities['comment'],
                ]);
        }

        foreach ($validated['ethicals'] as $ethicals) {
            $usersEvaluation->ethicals()
                ->whereKey($ethicals['id'])
                ->update([
                    'score'         => $ethicals['score'],
                    'explanation'   => $ethicals['explanation'],
                ]);
        }

        foreach ($validated['managerial_skills'] as $managerialSkills) {
            $usersEvaluation->managerialSkills()
                ->whereKey($managerialSkills['id'])
                ->update([
                    'score'         => $managerialSkills['score'],
                    'explanation'   => $managerialSkills['explanation'],
                ]);
        }

        $auth_user_evaluator = Auth::user();
        if(empty($usersEvaluation->rejected_by_id))
        {
            $usersEvaluation->employee->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your sign.'));
        }else{
            if($usersEvaluation->rejected_by_id == $usersEvaluation->approver1_id)
            {
                $usersEvaluation->approver1->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
            elseif($usersEvaluation->rejected_by_id == $usersEvaluation->approver2_id)
            {
                $usersEvaluation->approver2->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
        }

        return response()->json(
            [
                'message' => 'Updated Successfully',
            ],
            201
        );
    }

    public function HoRankNFile(UpdateHoRankNFile $validated, UsersEvaluation $usersEvaluation)
    {
        $status = $this->statusLogic($usersEvaluation);

        $evalDateFrom = $validated['coverage_from'];
        $evalDateTo = $validated['coverage_to'];

        if(!empty($validated['review_type_regular']))
        {
            [$evalDateFrom, $evalDateTo] = match($validated['review_type_regular'])
            {
                    "Q1"    =>  QuarterDateRange::Q1->range(),
                    "Q2"    =>  QuarterDateRange::Q2->range(),
                    "Q3"    =>  QuarterDateRange::Q3->range(),
                    "Q4"    =>  QuarterDateRange::Q4->range(),
            };
        }

        $usersEvaluation->update(
            [
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performance_score'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['review_type_probationary'] ?: null,
                'reviewTypeRegular'             => $validated['review_type_regular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['review_type_others_improvement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['review_type_others_custom'] ?: null,
                'priorityArea1'                 => $validated['priority_area_1'] ?: null,
                'priorityArea2'                 => $validated['priority_area_2'] ?: null,
                'priorityArea3'                 => $validated['priority_area_3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        foreach ($validated['job_knowledge'] as $jobKnowledge) {
            $usersEvaluation->jobKnowledge()
                ->whereKey($jobKnowledge['id'])
                ->update([
                    'score'     => $jobKnowledge['score'],
                    'comment'   => $jobKnowledge['comment'],
                ]);
        }

        foreach ($validated['quality_of_works'] as $qualityOfWorks) {
            $usersEvaluation->qualityOfWorks()
                ->whereKey($qualityOfWorks['id'])
                ->update([
                    'score'     => $qualityOfWorks['score'],
                    'comment'   => $qualityOfWorks['comment'],
                ]);
        }

        foreach ($validated['adaptabilities'] as $adaptability) {
            $usersEvaluation->adaptability()
                ->whereKey($adaptability['id'])
                ->update([
                    'score'     => $adaptability['score'],
                    'comment'   => $adaptability['comment'],
                ]);
        }

        foreach ($validated['teamworks'] as $teamworks) {
            $usersEvaluation->teamworks()
                ->whereKey($teamworks['id'])
                ->update([
                    'score'     => $teamworks['score'],
                    'comment'   => $teamworks['comment'],
                ]);
        }

        foreach ($validated['reliabilities'] as $reliabilities) {
            $usersEvaluation->reliabilities()
                ->whereKey($reliabilities['id'])
                ->update([
                    'score'     => $reliabilities['score'],
                    'comment'   => $reliabilities['comment'],
                ]);
        }

        foreach ($validated['ethicals'] as $ethicals) {
            $usersEvaluation->ethicals()
                ->whereKey($ethicals['id'])
                ->update([
                    'score'         => $ethicals['score'],
                    'explanation'   => $ethicals['explanation'],
                ]);
        }

        $auth_user_evaluator = Auth::user();
        if(empty($usersEvaluation->rejected_by_id))
        {
            $usersEvaluation->employee->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your sign.'));
        }else{
            if($usersEvaluation->rejected_by_id == $usersEvaluation->approver1_id)
            {
                $usersEvaluation->approver1->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
            elseif($usersEvaluation->rejected_by_id == $usersEvaluation->approver2_id)
            {
                $usersEvaluation->approver2->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
        }

        return response()->json(
            [
                'message' => 'Updated Successfully',
            ],
            201
        );
    }

    public function BranchBasicAreaManager(UpdateBranchBasicAreaManager $validated, UsersEvaluation $usersEvaluation)
    {
        $status = $this->statusLogic($usersEvaluation);

        $evalDateFrom = $validated['coverage_from'];
        $evalDateTo = $validated['coverage_to'];

        if(!empty($validated['review_type_regular']))
        {
            [$evalDateFrom, $evalDateTo] = match($validated['review_type_regular'])
            {
                    "Q1"    =>  QuarterDateRange::Q1->range(),
                    "Q2"    =>  QuarterDateRange::Q2->range(),
                    "Q3"    =>  QuarterDateRange::Q3->range(),
                    "Q4"    =>  QuarterDateRange::Q4->range(),
            };
        }

        $usersEvaluation->update(
            [
                'rating'                        => $validated['rating'],
                'percentage'                    => $validated['performance_score'],
                'coverageFrom'                  => $evalDateFrom,
                'coverageTo'                    => $evalDateTo,
                'reviewTypeProbationary'        => $validated['review_type_probationary'] ?: null,
                'reviewTypeRegular'             => $validated['review_type_regular'] ?: null,
                'reviewTypeOthersImprovement'   => $validated['review_type_others_improvement'] ?: null,
                'reviewTypeOthersCustom'        => $validated['review_type_others_custom'] ?: null,
                'priorityArea1'                 => $validated['priority_area_1'] ?: null,
                'priorityArea2'                 => $validated['priority_area_2'] ?: null,
                'priorityArea3'                 => $validated['priority_area_3'] ?: null,
                'remarks'                       => $validated['remarks'] ?: null,
                'evaluatorApprovedAt'           => now(),
                'status'                        => $status
            ]
        );

        foreach ($validated['job_knowledge'] as $jobKnowledge) {
            $usersEvaluation->jobKnowledge()
                ->whereKey($jobKnowledge['id'])
                ->update([
                    'score'     => $jobKnowledge['score'],
                    'comment'   => $jobKnowledge['comment'],
                ]);
        }

        foreach ($validated['quality_of_works'] as $qualityOfWorks) {
            $usersEvaluation->qualityOfWorks()
                ->whereKey($qualityOfWorks['id'])
                ->update([
                    'score'     => $qualityOfWorks['score'],
                    'comment'   => $qualityOfWorks['comment'],
                ]);
        }

        foreach ($validated['adaptabilities'] as $adaptability) {
            $usersEvaluation->adaptability()
                ->whereKey($adaptability['id'])
                ->update([
                    'score'     => $adaptability['score'],
                    'comment'   => $adaptability['comment'],
                ]);
        }

        foreach ($validated['teamworks'] as $teamworks) {
            $usersEvaluation->teamworks()
                ->whereKey($teamworks['id'])
                ->update([
                    'score'     => $teamworks['score'],
                    'comment'   => $teamworks['comment'],
                ]);
        }

        foreach ($validated['reliabilities'] as $reliabilities) {
            $usersEvaluation->reliabilities()
                ->whereKey($reliabilities['id'])
                ->update([
                    'score'     => $reliabilities['score'],
                    'comment'   => $reliabilities['comment'],
                ]);
        }

        foreach ($validated['ethicals'] as $ethicals) {
            $usersEvaluation->ethicals()
                ->whereKey($ethicals['id'])
                ->update([
                    'score'         => $ethicals['score'],
                    'explanation'   => $ethicals['explanation'],
                ]);
        }

        foreach ($validated['managerial_skills'] as $managerialSkills) {
            $usersEvaluation->managerialSkills()
                ->whereKey($managerialSkills['id'])
                ->update([
                    'score'         => $managerialSkills['score'],
                    'explanation'   => $managerialSkills['explanation'],
                ]);
        }

        $auth_user_evaluator = Auth::user();
        if(empty($usersEvaluation->rejected_by_id))
        {
            $usersEvaluation->employee->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your sign.'));
        }else{
            if($usersEvaluation->rejected_by_id == $usersEvaluation->approver1_id)
            {
                $usersEvaluation->approver1->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
            elseif($usersEvaluation->rejected_by_id == $usersEvaluation->approver2_id)
            {
                $usersEvaluation->approver2->notify(new EvalNotifications('An evaluation edited by ' . $auth_user_evaluator->fname . ' ' . $auth_user_evaluator->lname . ' is awaiting your approval.'));
            }
        }

        return response()->json(
            [
                'message' => 'Updated Successfully',
            ],
            201
        );
    }
}
