<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferenceAccountVerifyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'employeeID' => 'required|integer',
            'officeID' => 'required|integer',
            'positionID' => 'required|integer',
            'yearID' => 'required|integer'
        ];
    }
}
