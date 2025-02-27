<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateRequest extends FormRequest
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
            "user_id" => ["sometimes", "required", "exists:users,id"],
        ];
    }
    public function messages()
    {
        return [
            'user_id.required' => 'The username field is required.',
            'user_id.sometimes' => 'The user id is required sometimes.',
            'user_id.exists' => 'The selected user id does not exist.',
        ];
    }
}
