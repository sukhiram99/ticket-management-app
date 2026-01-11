<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ensure this returns true if the user is authenticated
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id', // Recommended: Validate the merged field,
            'status' => 'nullable|in:open',

        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a brief title for your ticket.',
            'title.max'      => 'The title is too long; please keep it under 255 characters.',
            'description.required' => 'Please describe the issue so we can help.',
            'status.in' => 'The status must be either "open".',

        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Use $this->user() to access the authenticated user within a FormRequest
        if ($user = $this->user()) {
            $this->merge([
                'user_id' => $user->id, // Note: id is typically a property, not a method id()
            ]);
        }
    }
}
