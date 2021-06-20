<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'name' => 'required',
          'email' => 'required|email|unique:users,email',
          'password' => 'required|min:8',
          'permissions' => 'array',
          'groups' => 'array',
          'avatar_image' => 'nullable|image'
        ];
    }
}
