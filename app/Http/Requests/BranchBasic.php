<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchBasic extends FormRequest
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
            'rating'                                => ['required', 'numeric'],
            'coverageFrom'                          => ['required', 'date'],
            'coverageTo'                            => ['required', 'date'],
            'reviewTypeProbationary'                => ['nullable', 'numeric'],
            'reviewTypeRegular'                     => ['nullable', 'string'],
            'reviewTypeOthersImprovement'           => ['nullable', 'boolean'],
            'reviewTypeOthersCustom'                => ['nullable', 'string'],
            'priorityArea1'                         => ['nullable', 'string'],
            'priorityArea2'                         => ['nullable', 'string'],
            'priorityArea3'                         => ['nullable', 'string'],
            'remarks'                               => ['nullable', 'string'],
        ];
    }

    public function jobKnowledgeRules()
    {
        return [
            'jobKnowledgeScore1'                    => ['required', 'numeric'],
            'jobKnowledgeScore2'                    => ['required', 'numeric'],
            'jobKnowledgeScore3'                    => ['required', 'numeric'],
            'jobKnowledgeComments1'                 => ['nullable', 'string'],
            'jobKnowledgeComments2'                 => ['nullable', 'string'],
            'jobKnowledgeComments3'                 => ['nullable', 'string'],
        ];
    }

    public function qualityOfWorkRules()
    {
        return [
            'qualityOfWorkScore1'                   => ['required', 'numeric'],
            'qualityOfWorkScore2'                   => ['required', 'numeric'],
            'qualityOfWorkScore3'                   => ['required', 'numeric'],
            'qualityOfWorkScore4'                   => ['required', 'numeric'],
            'qualityOfWorkScore5'                   => ['required', 'numeric'],
            'qualityOfWorkScore6'                   => ['required', 'numeric'],
            'qualityOfWorkScore7'                   => ['required', 'numeric'],
            'qualityOfWorkScore8'                   => ['required', 'numeric'],
            'qualityOfWorkScore9'                   => ['required', 'numeric'],
            'qualityOfWorkScore10'                  => ['required', 'numeric'],
            'qualityOfWorkScore11'                  => ['required', 'numeric'],
            'qualityOfWorkScore12'                  => ['required', 'numeric'],
            'qualityOfWorkComments1'                => ['nullable', 'string'],
            'qualityOfWorkComments2'                => ['nullable', 'string'],
            'qualityOfWorkComments3'                => ['nullable', 'string'],
            'qualityOfWorkComments4'                => ['nullable', 'string'],
            'qualityOfWorkComments5'                => ['nullable', 'string'],
            'qualityOfWorkComments6'                => ['nullable', 'string'],
            'qualityOfWorkComments7'                => ['nullable', 'string'],
            'qualityOfWorkComments8'                => ['nullable', 'string'],
            'qualityOfWorkComments9'                => ['nullable', 'string'],
            'qualityOfWorkComments10'               => ['nullable', 'string'],
            'qualityOfWorkComments11'               => ['nullable', 'string'],
            'qualityOfWorkComments12'               => ['nullable', 'string'],
        ];
    }

    public function adaptabilityRules()
    {
        return [
            'adaptabilityScore1'                    => ['required', 'numeric'],
            'adaptabilityScore2'                    => ['required', 'numeric'],
            'adaptabilityScore3'                    => ['required', 'numeric'],
            'adaptabilityComments1'                 => ['nullable', 'string'],
            'adaptabilityComments2'                 => ['nullable', 'string'],
            'adaptabilityComments3'                 => ['nullable', 'string'],
        ];
    }

    public function teamworkRules()
    {
        return [
            'teamworkScore1'                        => ['required', 'numeric'],
            'teamworkScore2'                        => ['required', 'numeric'],
            'teamworkScore3'                        => ['required', 'numeric'],
            'teamworkComments1'                     => ['nullable', 'string'],
            'teamworkComments2'                     => ['nullable', 'string'],
            'teamworkComments3'                     => ['nullable', 'string'],
        ];
    }

    public function reliabilityRules()
    {
        return [
            'reliabilityScore1'                     => ['required', 'numeric'],
            'reliabilityScore2'                     => ['required', 'numeric'],
            'reliabilityScore3'                     => ['required', 'numeric'],
            'reliabilityScore4'                     => ['required', 'numeric'],
            'reliabilityComments1'                  => ['nullable', 'string'],
            'reliabilityComments2'                  => ['nullable', 'string'],
            'reliabilityComments3'                  => ['nullable', 'string'],
            'reliabilityComments4'                  => ['nullable', 'string'],
        ];
    }

    public function ethicalRules()
    {
        return [
            'ethicalScore1'                         => ['required', 'numeric'],
            'ethicalScore2'                         => ['required', 'numeric'],
            'ethicalScore3'                         => ['required', 'numeric'],
            'ethicalScore4'                         => ['required', 'numeric'],
            'ethicalExplanation1'                   => ['nullable', 'string'],
            'ethicalExplanation2'                   => ['nullable', 'string'],
            'ethicalExplanation3'                   => ['nullable', 'string'],
            'ethicalExplanation4'                   => ['nullable', 'string'],
        ];
    }

    public function customerServiceRules()
    {
        return [
            'customerServiceScore1'                 => ['required', 'numeric'],
            'customerServiceScore2'                 => ['required', 'numeric'],
            'customerServiceScore3'                 => ['required', 'numeric'],
            'customerServiceScore4'                 => ['required', 'numeric'],
            'customerServiceScore5'                 => ['required', 'numeric'],
            'customerServiceExplanation1'           => ['nullable', 'string'],
            'customerServiceExplanation2'           => ['nullable', 'string'],
            'customerServiceExplanation3'           => ['nullable', 'string'],
            'customerServiceExplanation4'           => ['nullable', 'string'],
            'customerServiceExplanation5'           => ['nullable', 'string'],
        ];
    }

    public function managerialSkillsRules()
    {
        return [
            'managerialSkillsScore1'                 => ['required', 'numeric'],
            'managerialSkillsScore2'                 => ['required', 'numeric'],
            'managerialSkillsScore3'                 => ['required', 'numeric'],
            'managerialSkillsScore4'                 => ['required', 'numeric'],
            'managerialSkillsScore5'                 => ['required', 'numeric'],
            'managerialSkillsScore6'                 => ['required', 'numeric'],
            'managerialSkillsExplanation1'           => ['nullable', 'string'],
            'managerialSkillsExplanation2'           => ['nullable', 'string'],
            'managerialSkillsExplanation3'           => ['nullable', 'string'],
            'managerialSkillsExplanation4'           => ['nullable', 'string'],
            'managerialSkillsExplanation5'           => ['nullable', 'string'],
            'managerialSkillsExplanation6'           => ['nullable', 'string'],
        ];
    }
}
