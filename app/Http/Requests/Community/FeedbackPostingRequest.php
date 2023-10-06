<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackPostingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'userID' => 'required|integer',
            'categoryID' => 'required|integer',
            'content' => 'sometimes',
            'photos' => 'sometimes'
        ];
    }
}
