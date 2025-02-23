<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonUploadVideoRequest extends FormRequest
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
            'video' => ['required','file','mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/x-flv,video/webm'],
        ];
    }

    public function message()
    {
        return [
            'video.required' => 'A video file is required.',
            'video.file' => 'The uploaded file must be a valid file.',
            'video.mimetypes' => 'The video must be of type: mp4, mov, avi, wmv, flv, or webm.',
        ];
    }
}
