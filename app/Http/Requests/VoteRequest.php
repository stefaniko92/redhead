<?php

namespace App\Http\Requests;

use App\Models\JobApproval;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VoteRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'job_id' => 'required',
            'vote' => ['required', Rule::in([JobApproval::VOTE_APPROVED, JobApproval::VOTE_REJECTED])]
        ];
    }
}
