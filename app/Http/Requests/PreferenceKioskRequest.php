<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferenceKioskRequest extends FormRequest
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
            'officeID' => 'required|integer',
            'positionID' => 'required|integer',
            'description' => 'required|string',
            'photo' => 'required|string'
        ];
    }
}
