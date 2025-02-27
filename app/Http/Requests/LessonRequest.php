<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
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

            "course_id" => ['required',"exists:courses,id"],
            "title" => ['required',"min:3","max:225","string"],
            "lesson_detail" => ['required','string'],
            "is_available" => ['nullable','boolean'],
            "video_url" => ['required','string']
        ];
    }
    public function messages(): array
    {
        return [
            'course_id.required' => 'The course ID is required.',
            'course_id.exists' => 'The selected course does not exist.',

            'title.required' => 'The title is required.',
            'title.min' => 'The title must be at least 3 characters.',
            'title.max' => 'The title may not be greater than 225 characters.',
            'title.string' => 'The title must be a string.',

            'lesson_detail.required' => 'The lesson detail is required.',
            'lesson_detail.string' => 'The lesson detail must be a string.',

            'is_available.boolean' => 'The availability must be true or false.',

            'video_url.required' => 'The video URL is required.',
            'video_url.string' => 'The video URL must be a string.',
        ];
    }
}
