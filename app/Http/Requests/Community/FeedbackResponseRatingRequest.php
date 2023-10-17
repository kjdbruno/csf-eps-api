<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackResponseRatingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'rating' => 'required|integer'
        ];
    }
}
