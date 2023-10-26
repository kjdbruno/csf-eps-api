<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackEntryKioskRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'sometimes',
            'number' => 'sometimes',
            'email' => 'sometimes',
            'officeID' => 'required|integer',
            'personnelID' => 'sometimes',
            'phyRating' => 'required',
            'serRating' => 'required',
            'perRating' => 'required',
            'ovrRating' => 'required',
            'suggestion' => 'sometimes'
        ];
    }
}
