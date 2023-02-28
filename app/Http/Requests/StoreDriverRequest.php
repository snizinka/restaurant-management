<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'lastname' => ['required', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Please enter driver's name.",
            'lastname.required' => "Please enter driver's lastname.",
        ];
    }
}
