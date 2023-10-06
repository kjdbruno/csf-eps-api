<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackEntryOfflineRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'number' => 'required|digits:11',
            'sexID' => 'required|integer',
            'categoryID' => 'required|integer',
            'content' => 'sometimes|string',
            'photos' => 'sometimes'
        ];
    }
}
