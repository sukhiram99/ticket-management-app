<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to update this specific ticket.
     */
    public function authorize(): bool
    {
        // 1. Get the ticket instance from the route (Route Model Binding)
        $ticket = $this->route('ticket');

        // 2. Check if the authenticated user's ID matches the ticket's user_id
        return $this->user()->id === $ticket->user_id;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in_progress',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide a brief title for your ticket.',
            'description.required' => 'Please describe the issue so we can help.',
            'status.required' => 'The ticket status is required.',
            'status.in' => 'The status must be either "open" or "in_progress".',
        ];
    }

    /**
     * Custom JSON response for authorization failures (403 Forbidden).
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'You are not authorized to update this ticket.',
        ], 403));
    }

    /**
     * Custom JSON response for validation failures (422 Unprocessable Content).
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }
}
