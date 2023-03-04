<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return
            [
                'address' => ['required', 'max:255'],
                'name' => ['required', 'max:255'],
                'contacts' => ['required', 'max:255'],
            ];
    }

    public function messages()
    {
        return [
            'address.required' => "Please enter restaurant address.",
            'name.required' => "Please enter restaurant name.",
            'contacts.required' => "Please enter restaurant contacts.",
        ];
    }
}
