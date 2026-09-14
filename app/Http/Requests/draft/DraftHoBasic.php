<?php

namespace App\Http\Requests\create;

use Illuminate\Foundation\Http\FormRequest;

class HoBasic extends FormRequest
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
            $this->managerialSkillsRules()
        );
    }

    public function mainRules()
    {
        return [
            'rating'                                => ['required', 'numeric'],
            'performanceScore'                      => ['required', 'numeric'],
            'coverageFrom'                          => ['required', 'date'],
            'coverageTo'                            => ['required', 'date'],
            'reviewTypeProbationary'                => ['nullable', 'numeric'],
            'reviewTypeRegular'                     => ['nullable', 'string'],
            'reviewTypeOthersImprovement'           => ['nullable', 'boolean'],
            'reviewTypeOthersCustom'                => ['nullable', 'string'],
            'priorityArea1'                         => ['required', 'string', 'min:20'],
            'priorityArea2'                         => ['nullable', 'string', 'min:20'],
            'priorityArea3'                         => ['nullable', 'string', 'min:20'],
            'remarks'                               => ['nullable', 'string'],
        ];
    }

    public function jobKnowledgeRules()
    {
        return [
            'jobKnowledgeScore1'                    => ['nullable', 'numeric'],
            'jobKnowledgeScore2'                    => ['nullable', 'numeric'],
            'jobKnowledgeScore3'                    => ['nullable', 'numeric'],
            'jobKnowledgeComments1'                 => ['required_with:jobKnowledgeScore1', 'string'],
            'jobKnowledgeComments2'                 => ['required_with:jobKnowledgeScore2', 'string'],
            'jobKnowledgeComments3'                 => ['required_with:jobKnowledgeScore3', 'string'],
        ];
    }

    public function qualityOfWorkRules()
    {
        return [
            'qualityOfWorkScore1'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore2'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore3'                   => ['nullable', 'numeric'],
            'qualityOfWorkScore4'                   => ['nullable', 'numeric'],
            'qualityOfWorkComments1'                => ['required_with:qualityOfWorkScore1', 'string'],
            'qualityOfWorkComments2'                => ['required_with:qualityOfWorkScore2', 'string'],
            'qualityOfWorkComments3'                => ['required_with:qualityOfWorkScore3', 'string'],
            'qualityOfWorkComments4'                => ['required_with:qualityOfWorkScore4', 'string'],
        ];
    }

    public function adaptabilityRules()
    {
        return [
            'adaptabilityScore1'                    => ['nullable', 'numeric'],
            'adaptabilityScore2'                    => ['nullable', 'numeric'],
            'adaptabilityScore3'                    => ['nullable', 'numeric'],
            'adaptabilityComments1'                 => ['required_with:adaptabilityScore1', 'string'],
            'adaptabilityComments2'                 => ['required_with:adaptabilityScore2', 'string'],
            'adaptabilityComments3'                 => ['required_with:adaptabilityScore3', 'string'],
        ];
    }

    public function teamworkRules()
    {
        return [
            'teamworkScore1'                        => ['nullable', 'numeric'],
            'teamworkScore2'                        => ['nullable', 'numeric'],
            'teamworkScore3'                        => ['nullable', 'numeric'],
            'teamworkComments1'                     => ['required_with:teamworkScore1', 'string'],
            'teamworkComments2'                     => ['required_with:teamworkScore2', 'string'],
            'teamworkComments3'                     => ['required_with:teamworkScore3', 'string'],
        ];
    }

    public function reliabilityRules()
    {
        return [
            'reliabilityScore1'                     => ['nullable', 'numeric'],
            'reliabilityScore2'                     => ['nullable', 'numeric'],
            'reliabilityScore3'                     => ['nullable', 'numeric'],
            'reliabilityScore4'                     => ['nullable', 'numeric'],
            'reliabilityComments1'                  => ['required_with:reliabilityScore1', 'string'],
            'reliabilityComments2'                  => ['required_with:reliabilityScore2', 'string'],
            'reliabilityComments3'                  => ['required_with:reliabilityScore3', 'string'],
            'reliabilityComments4'                  => ['required_with:reliabilityScore4', 'string'],
        ];
    }

    public function ethicalRules()
    {
        return [
            'ethicalScore1'                         => ['nullable', 'numeric'],
            'ethicalScore2'                         => ['nullable', 'numeric'],
            'ethicalScore3'                         => ['nullable', 'numeric'],
            'ethicalScore4'                         => ['nullable', 'numeric'],
            'ethicalExplanation1'                   => ['required_with:ethicalScore1', 'string'],
            'ethicalExplanation2'                   => ['required_with:ethicalScore2', 'string'],
            'ethicalExplanation3'                   => ['required_with:ethicalScore3', 'string'],
            'ethicalExplanation4'                   => ['required_with:ethicalScore4', 'string'],
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
            'managerialSkillsExplanation1'           => ['required_with:managerialSkillsScore1', 'string'],
            'managerialSkillsExplanation2'           => ['required_with:managerialSkillsScore2', 'string'],
            'managerialSkillsExplanation3'           => ['required_with:managerialSkillsScore3', 'string'],
            'managerialSkillsExplanation4'           => ['required_with:managerialSkillsScore4', 'string'],
            'managerialSkillsExplanation5'           => ['required_with:managerialSkillsScore5', 'string'],
            'managerialSkillsExplanation6'           => ['required_with:managerialSkillsScore6', 'string'],
        ];
    }
}
