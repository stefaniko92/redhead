<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeCreateRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email',
            'type' => 'required',
            'available_hours' => Rule::requiredIf(fn () => $this->type === Employee::TYPE_PROFESSOR),
            'working_hours' => Rule::requiredIf(fn () => $this->type === Employee::TYPE_TRADER),
        ];
    }
}
