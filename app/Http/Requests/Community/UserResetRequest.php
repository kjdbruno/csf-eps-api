<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

class UserResetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'oldPassword' => 'required|string|max:255',
            'password' => 'required|string|min:4|confirmed',
        ];
    }
}
