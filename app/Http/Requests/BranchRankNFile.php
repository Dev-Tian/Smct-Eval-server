<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchRankNFile extends FormRequest
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
        return [
            $this->mainRules(),
        ];
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
}
