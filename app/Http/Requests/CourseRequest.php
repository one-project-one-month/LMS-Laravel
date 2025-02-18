<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CourseRequest extends FormRequest
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
            "course_name" => ["required", "min:5", "max:225"],
            'thumbnail' => ['required', 'string'],
            "type" => ["required", Rule::in(["free", "paid"])],
            "level" => ["required", Rule::in(["beginner", "intermediate", "advance"])],
            "description" => ["nullable", "string"],
            "duration" => ["required", "string"],
            "original_price" => ["required", "string"],
            "current_price" => ["required", "string"],
            "instructor_id" => ["required","exists:instructors,id"],
            "category_id" => ["required"]
        ];
    }

    public function messages(): array
    {
        return [
            'course_name.required' => 'The username field is required.',
            'username.string' => 'The username must be a valid string.',
            'username.max' => 'The username must not exceed 255 characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already taken.',

            'password.required' => 'The password field is required.',
            'password.string' => 'The password must be a valid string.',
            'password.min' => 'The password must be at least 8 characters long.',
        ];
    }
}
