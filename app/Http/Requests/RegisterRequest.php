<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "name" => "required|min:3|max:100",
            "nickname" => "required|min:3|max:50",
            "email" => "required|email",
            "password" => "required|min:6|max:50|confirmed"
        ];
    }
}
