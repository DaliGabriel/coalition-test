<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class IndexTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ];
    }

    /**
     * Return the selected project ID, or null when no filter is active.
     */
    public function projectId(): ?int
    {
        return $this->integer('project_id') ?: null;
    }
}
