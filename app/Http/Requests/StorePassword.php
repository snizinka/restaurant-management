<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePassword extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'password' => ['required'],
            'confirm' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'password.required' => "Please enter new password.",
            'confirm.required' => "Please confirm new password.",
        ];
    }
}
