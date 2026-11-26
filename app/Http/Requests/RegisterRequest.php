<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Family;

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
     * Prepare the data for validation (normalization).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'invite_code' => $this->invite_code ? strtoupper(trim($this->invite_code)) : null,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'registration_type' => ['required', 'in:new_family,join_family'],
            'family_name' => [
                'required_if:registration_type,new_family',
                'nullable',
                'string',
                'max:100',
            ],
            'invite_code' => [
                'required_if:registration_type,join_family',
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    if ($this->input('registration_type') === 'join_family') {
                        if (!Family::where('invite_code', $value)->exists()) {
                            $fail('The provided family invite code was not found. Please verify with your family admin.');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Your full name is required.',
            'name.max' => 'Your name may not exceed 100 characters.',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please provide a valid email format.',
            'email.unique' => 'This email address is already registered in Money Tracker.',
            'password.required' => 'A password is required.',
            'password.min' => 'The password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'registration_type.required' => 'Please select whether you want to create a new family or join an existing one.',
            'registration_type.in' => 'Invalid family onboarding option selected.',
            'family_name.required_if' => 'Please specify a name for your new family.',
            'invite_code.required_if' => 'Please enter the 8-character invite code provided by your family admin.',
        ];
    }
}
