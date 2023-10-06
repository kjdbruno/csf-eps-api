<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreferencePositionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'label' => 'required|string|max:255'
        ];
    }
}
