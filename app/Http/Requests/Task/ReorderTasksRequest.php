<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTasksRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tasks'   => ['required', 'array'],
            'tasks.*' => ['required', 'integer', 'exists:tasks,id'],
        ];
    }

    /**
     * Get custom error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'tasks.required'  => 'The task order list is required.',
            'tasks.*.exists'  => 'One or more task IDs are invalid.',
        ];
    }
}
