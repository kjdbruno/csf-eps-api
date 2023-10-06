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
            'officeID' => 'required|integer',
            'personnelID' => 'required|integer',
            'phyRating' => 'required',
            'serRating' => 'required',
            'perRating' => 'required',
            'ovrRating' => 'required',
            'suggestion' => 'sometimes'
        ];
    }
}
