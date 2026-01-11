<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTicketStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'required|in:open,in_progress,closed'
        ];
    }

    /**
     * Custom messages for status validation.
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Please provide a status to update.',
            'status.in'       => 'The status must be one of the following: open, in_progress, or closed.',
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
     * Custom JSON response for unauthorized admins.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Access denied. Only administrators can change the ticket status.',
        ], 403));
    }
}
