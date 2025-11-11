<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAvatarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization will be checked in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'resource_id' => [
                'required',
                'string',
                Rule::exists('resources', 'id')->where(function ($query) {
                    $query->where('type', 'avatar_image')
                        ->where('status', 'approved');
                }),
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
            'resource_id.required' => 'Please select an avatar.',
            'resource_id.exists' => 'The selected avatar is not available or not approved.',
        ];
    }
}
