<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferenceAccountModifyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'roleID' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'employeeID' => 'required|integer',
            'officeID' => 'required|integer',
            'positionID' => 'required|integer',
            'yearID' => 'required|integer',
            'avatar' => 'required|string'
        ];
    }
}
