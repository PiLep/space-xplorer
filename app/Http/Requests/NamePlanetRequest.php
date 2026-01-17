<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NamePlanetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization checked in controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = config('codex.name_validation');

        return [
            'name' => [
                'required',
                'string',
                'min:'.$rules['min_length'],
                'max:'.$rules['max_length'],
                'regex:'.$rules['allowed_characters'],
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est requis.',
            'name.min' => 'Le nom doit contenir au moins :min caractères.',
            'name.max' => 'Le nom ne peut pas dépasser :max caractères.',
            'name.regex' => 'Le nom contient des caractères non autorisés.',
        ];
    }
}

