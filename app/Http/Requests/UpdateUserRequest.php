<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
  /**
  * Get the validation rules that apply to the request.
  *
  * @return array
  */
  public function rules()
  {
    return [
      'name' => 'min:3|max:100',
      'email' => 'email',
      'permissions' => 'array',
      'groups' => 'array',
      'avatar_image' => 'nullable|image'
    ];
  }
}
