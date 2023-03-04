<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'price' => ['required', 'numeric'],
            'ingredients' => ['required', 'max:255'],
            'category_id' => ['required', 'numeric'],
            'restaurant_id' => ['required', 'numeric']
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Please enter dish name.",
            'price.required' => "Please enter dish price.",
            'price.numeric' => "Please enter numeric dish price.",
            'ingredients.required' => "Please enter dish ingredients.",
            'category_id.required' => "Please enter dish category.",
            'restaurant_id.required' => "Please enter dish restaurant.",
        ];
    }
}
