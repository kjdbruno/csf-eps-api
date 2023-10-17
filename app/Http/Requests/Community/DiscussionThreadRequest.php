<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;

class DiscussionThreadRequest extends FormRequest
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
            'discussionID' => 'required|integer',
            'content' => 'required|string'
        ];
    }
}
