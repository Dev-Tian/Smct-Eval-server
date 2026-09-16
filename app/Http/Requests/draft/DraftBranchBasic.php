<?php

namespace App\Http\Requests\draft;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DraftBranchBasic extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(
            $this->mainRules(),
            $this->jobKnowledgeRules(),
            $this->qualityOfWorkRules(),
            $this->adaptabilityRules(),
            $this->teamworkRules(),
            $this->reliabilityRules(),
            $this->ethicalRules(),
            $this->customerServiceRules(),
            $this->managerialSkillsRules(),
        );
    }

    public function mainRules()
    {
        return [
            'rating'                                => ['nullable', 'numeric'],
            'performanceScore'                      => ['nullable', 'numeric'],
            'coverageFrom'                          => ['required', 'date'],
            'coverageTo'                            => ['required', 'date'],
            'reviewTypeProbationary'                => ['nullable', 'numeric'],
            'reviewTypeRegular'                     => ['nullable', 'string'],
            'reviewTypeOthersImprovement'           => ['nullable', 'boolean'],
            'reviewTypeOthersCustom'                => ['nullable', 'string'],
            'priorityArea1'                         => ['nullable', 'string', 'min:20'],
            'priorityArea2'                         => ['nullable', 'string', 'min:20'],
            'priorityArea3'                         => ['nullable', 'string', 'min:20'],
            'remarks'                               => ['nullable', 'string',],
        ];
    }

    public function jobKnowledgeRules()
    {
        return [
            'jobKnowledgeScore1'                    => ['nullable', 'numeric'],
            'jobKnowledgeScore2'                    => ['nullable', 'numeric'],
            'jobKnowledgeScore3'                    => ['nullable', 'numeric'],
            'jobKnowledgeComments1'                 => [Rule::requiredif( fn() => !empty(request()->jobKnowledgeScore1))],
            'jobKnowledgeComments2'                 => [Rule::requiredif( fn() => !empty(request()->jobKnowledgeScore2))],
            'jobKnowledgeComments3'                 => [Rule::requiredif( fn() => !empty(request()->jobKnowledgeScore3))],
        ];
    }

    public function qualityOfWorkRules()
    {
        return [
            'qualityOfWorkScore1'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore2'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore3'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore4'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore5'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore6'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore7'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore8'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore9'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore10'                  => ['nullable', 'numeric'],
            'qualityOfWorkScore11'                  => ['nullable', 'numeric'],
            'qualityOfWorkScore12'                  => ['nullable', 'numeric'],
            'qualityOfWorkComments1'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore1))],
            'qualityOfWorkComments2'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore2))],
            'qualityOfWorkComments3'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore3))],
            'qualityOfWorkComments4'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore4))],
            'qualityOfWorkComments5'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore5))],
            'qualityOfWorkComments6'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore6))],
            'qualityOfWorkComments7'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore7))],
            'qualityOfWorkComments8'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore8))],
            'qualityOfWorkComments9'                => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore9))],
            'qualityOfWorkComments10'               => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore10))],
            'qualityOfWorkComments11'               => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore11))],
            'qualityOfWorkComments12'               => [Rule::requiredif( fn() => !empty(request()->qualityOfWorkScore12))],
        ];
    }
    public function adaptabilityRules()
    {
        return [
            'adaptabilityScore1'                    => ['nullable', 'numeric'],
            'adaptabilityScore2'                    => ['nullable', 'numeric'],
            'adaptabilityScore3'                    => ['nullable', 'numeric'],
            'adaptabilityComments1'                 => [Rule::requiredif( fn() => !empty(request()->adaptabilityScore1))],
            'adaptabilityComments2'                 => [Rule::requiredif( fn() => !empty(request()->adaptabilityScore2))],
            'adaptabilityComments3'                 => [Rule::requiredif( fn() => !empty(request()->adaptabilityScore3))],
        ];
    }

    public function teamworkRules()
    {
        return [
            'teamworkScore1'                        => ['nullable', 'numeric'],
            'teamworkScore2'                        => ['nullable', 'numeric'],
            'teamworkScore3'                        => ['nullable', 'numeric'],
            'teamworkComments1'                     => [Rule::requiredif( fn() => !empty(request()->teamworkScore1))],
            'teamworkComments2'                     => [Rule::requiredif( fn() => !empty(request()->teamworkScore2))],
            'teamworkComments3'                     => [Rule::requiredif( fn() => !empty(request()->teamworkScore3))],
        ];
    }

    public function reliabilityRules()
    {
        return [
            'reliabilityScore1'                     => ['nullable', 'numeric'],
            'reliabilityScore2'                     => ['nullable', 'numeric'],
            'reliabilityScore3'                     => ['nullable', 'numeric'],
            'reliabilityScore4'                     => ['nullable', 'numeric'],
            'reliabilityComments1'                  => [Rule::requiredif( fn() => !empty(request()->reliabilityScore1))],
            'reliabilityComments2'                  => [Rule::requiredif( fn() => !empty(request()->reliabilityScore2))],
            'reliabilityComments3'                  => [Rule::requiredif( fn() => !empty(request()->reliabilityScore3))],
            'reliabilityComments4'                  => [Rule::requiredif( fn() => !empty(request()->reliabilityScore4))],
        ];
    }

    public function ethicalRules()
    {
        return [
            'ethicalScore1'                         => ['nullable', 'numeric'],
            'ethicalScore2'                         => ['nullable', 'numeric'],
            'ethicalScore3'                         => ['nullable', 'numeric'],
            'ethicalScore4'                         => ['nullable', 'numeric'],
            'ethicalExplanation1'                   => [Rule::requiredif( fn() => !empty(request()->ethicalScore1))],
            'ethicalExplanation2'                   => [Rule::requiredif( fn() => !empty(request()->ethicalScore2))],
            'ethicalExplanation3'                   => [Rule::requiredif( fn() => !empty(request()->ethicalScore3))],
            'ethicalExplanation4'                   => [Rule::requiredif( fn() => !empty(request()->ethicalScore4))],
        ];
    }

    public function customerServiceRules()
    {
        return [
            'customerServiceScore1'                 => ['nullable', 'numeric'],
            'customerServiceScore2'                 => ['nullable', 'numeric'],
            'customerServiceScore3'                 => ['nullable', 'numeric'],
            'customerServiceScore4'                 => ['nullable', 'numeric'],
            'customerServiceScore5'                 => ['nullable', 'numeric'],
            'customerServiceExplanation1'           => [Rule::requiredif( fn() => !empty(request()->customerServiceScore1))],
            'customerServiceExplanation2'           => [Rule::requiredif( fn() => !empty(request()->customerServiceScore2))],
            'customerServiceExplanation3'           => [Rule::requiredif( fn() => !empty(request()->customerServiceScore3))],
            'customerServiceExplanation4'           => [Rule::requiredif( fn() => !empty(request()->customerServiceScore4))],
            'customerServiceExplanation5'           => [Rule::requiredif( fn() => !empty(request()->customerServiceScore5))],
        ];
    }

    public function managerialSkillsRules()
    {
        return [
            'managerialSkillsScore1'                 => ['nullable', 'numeric'],
            'managerialSkillsScore2'                 => ['nullable', 'numeric'],
            'managerialSkillsScore3'                 => ['nullable', 'numeric'],
            'managerialSkillsScore4'                 => ['nullable', 'numeric'],
            'managerialSkillsScore5'                 => ['nullable', 'numeric'],
            'managerialSkillsScore6'                 => ['nullable', 'numeric'],
            'managerialSkillsExplanation1'           => [Rule::requiredif( fn() => !empty(request()->managerialSkillsScore1))],
            'managerialSkillsExplanation2'           => [Rule::requiredif( fn() => !empty(request()->managerialSkillsScore2))],
            'managerialSkillsExplanation3'           => [Rule::requiredif( fn() => !empty(request()->managerialSkillsScore3))],
            'managerialSkillsExplanation4'           => [Rule::requiredif( fn() => !empty(request()->managerialSkillsScore4))],
            'managerialSkillsExplanation5'           => [Rule::requiredif( fn() => !empty(request()->managerialSkillsScore5))],
            'managerialSkillsExplanation6'           => [Rule::requiredif( fn() => !empty(request()->managerialSkillsScore6))],
        ];
    }
}
