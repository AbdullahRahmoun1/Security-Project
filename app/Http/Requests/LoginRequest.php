<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => ['required','string'],
            'password' => ['required','string','between:5,30'],
            'public_key' => ['required','string','min:1']
        ];
    }
}
