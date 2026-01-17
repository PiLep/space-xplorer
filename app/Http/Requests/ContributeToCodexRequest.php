<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContributeToCodexRequest extends FormRequest
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
        $rules = config('codex.content_validation');

        return [
            'content' => [
                'required',
                'string',
                'min:'.$rules['min_length'],
                'max:'.$rules['max_length'],
                function ($attribute, $value, $fail) use ($rules) {
                    // Forbidden words validation (case-insensitive)
                    $forbiddenWords = $rules['forbidden_words'] ?? [];
                    $contentLower = mb_strtolower($value);

                    foreach ($forbiddenWords as $forbiddenWord) {
                        if (mb_strpos($contentLower, mb_strtolower($forbiddenWord)) !== false) {
                            $fail('Le contenu contient un mot interdit.');
                        }
                    }
                },
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
            'content.required' => 'Le contenu est requis.',
            'content.min' => 'Le contenu doit contenir au moins :min caractères.',
            'content.max' => 'Le contenu ne peut pas dépasser :max caractères.',
        ];
    }
}

