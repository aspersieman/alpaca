<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Task;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'name' => 'required|between:2,100',
          'status' => 'required|in:'.join(',', Task::STATUSES),
          'due' => 'required|date',
          'priority' => 'nullable|in:'.join(',', Task::PRIORITIES)
        ];
    }
}
