<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'nullable|min:8|confirmed',
            'role' => 'nullable|in:user,admin',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'We need to know your name to create an account.',
            'name.string'   => 'Your name must contain only text characters.',
            'email.required' => 'An email address is required for login.',
            'email.email'    => 'Please provide a valid email format (e.g., user@example.com).',
            'email.unique'   => 'This email address is already registered with us.',
            'password.min'   => 'For security, your password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => $validator->errors()->first(),
        ], 422));
    }
}
