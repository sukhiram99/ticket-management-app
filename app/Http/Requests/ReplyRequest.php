<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        $user = $this->user();

        // Authorize if Admin OR Ticket Owner
        return $user->role === 'admin' || $user->id === $ticket->user_id;
    }

    /**
     * Validation rules for the reply.
     */
    public function rules(): array
    {
        return [
            // Required, must be string, minimum 2 characters
            'message' => 'required|string|min:2|unique:replies,message'
        ];
    }

    /**
     * Custom error messages for the reply.
     */
    public function messages(): array
    {
        return [
            'message.required' => 'The reply message cannot be empty.',
            'message.string'   => 'The reply must be a valid text string.',
            'message.min'      => 'Your message is too short; please enter at least 2 characters.',
            'message.unique'   => 'This exact message has already been posted. Please provide a different response.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: Only the creator or an admin can reply.',
        ], 403));
    }
}
