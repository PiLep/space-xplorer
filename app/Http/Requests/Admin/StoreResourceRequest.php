<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResourceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated admin users can create resources
        return $this->user() && $this->user()->is_super_admin;
    }

    /**
     * Prepare the data for validation.
     * Convert tags string to array if needed.
     */
    protected function prepareForValidation(): void
    {
        // Convert tags string to array if it's a string
        if ($this->has('tags') && is_string($this->tags)) {
            $tagsString = trim($this->tags);
            if (empty($tagsString)) {
                $this->merge(['tags' => []]);
            } else {
                $tagsArray = array_map('trim', explode(',', $tagsString));
                $tagsArray = array_filter($tagsArray); // Remove empty values
                $this->merge(['tags' => array_values($tagsArray)]);
            }
        } elseif (! $this->has('tags') || $this->tags === null) {
            $this->merge(['tags' => []]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', Rule::in(['avatar_image', 'planet_image', 'planet_video'])],
            'prompt' => ['required', 'string', 'min:10', 'max:2000'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
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
            'type.required' => 'The resource type is required.',
            'type.in' => 'The resource type must be one of: avatar_image, planet_image, planet_video.',
            'prompt.required' => 'The prompt is required.',
            'prompt.min' => 'The prompt must be at least 10 characters.',
            'prompt.max' => 'The prompt must not exceed 2000 characters.',
            'tags.array' => 'Tags must be an array.',
            'tags.*.string' => 'Each tag must be a string.',
            'tags.*.max' => 'Each tag must not exceed 50 characters.',
            'description.max' => 'The description must not exceed 1000 characters.',
        ];
    }
}
