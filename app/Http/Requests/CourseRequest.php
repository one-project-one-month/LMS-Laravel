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

            "course_name" => ["sometimes", "required", "min:5", "max:225"],
            'thumbnail' => [
                "sometimes",
                "required",
                'file',
                'mimes:jpg,jpeg,png',
                // 'max:2048',
            ],
            "type" => ["sometimes", "required", Rule::in(["free", "paid"])],
            "level" => ["sometimes", "required", Rule::in(["beginner", "intermediate", "advance"])],
            "description" => ["nullable", "string"],
            "duration" => ["sometimes", "required", "string"],
            "original_price" => ["sometimes", "required", "string"],
            "current_price" => ["sometimes", "required", "string"],
            "category_id" => ["sometimes", "required"]
        ];
    }

    // {
    //     "category" : "webDevelopment"
    // }

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
            'thumbnail.required' => 'Please upload a file!',
            'thumbnail.file' => 'The uploaded item must be a valid image.',
            'thumbnail.mimes' => 'Only JPG, PNG, and PDF files are allowed.',
            'thumbnail.max' => 'Max file size allowed is 2MB.',
        ];
    }
}
