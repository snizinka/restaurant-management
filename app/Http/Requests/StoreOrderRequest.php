<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address' => ['required', 'max:255'],
            'phone' => ['required', 'max:255'],
            'username' => ['required', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'address.required' => "Please enter delivery address.",
            'phone.required' => "Please enter your phone.",
            'username.required' => "Please enter your contacts.",
        ];
    }
}
