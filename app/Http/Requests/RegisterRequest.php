<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $role = $this->input("role");
        $commonRules = [

            'username' => ['required', 'string'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],

            "role" => ["required"]

        ];
        if ($role === "instructor") {
            $specificRules = [

                'nrc' => ['required', 'string'],
                'edu_background' => ['required', 'string'],

            ];
        } else {
            $specificRules = [];
        }
        return array_merge($commonRules, $specificRules);
    }
    public function messages(): array
    {


        return [
            'username.required' => 'Username is required.',
            'username.string' => 'Username must be a valid string.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already taken.',
            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a valid string.',
            'password.min' => 'Password must be at least 8 characters long.',
            'nrc.required' => 'NRC field is required.',
            'nrc.string' => 'Pleases Fill valid format for NRC.',
            'edu_background.required' => 'Education field is required.',
            'edu_background.string' => 'Pleases Fill valid format for Education',
        ];
    }
}
