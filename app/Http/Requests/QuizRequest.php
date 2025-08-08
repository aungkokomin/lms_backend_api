<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class QuizRequest extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  */
    // public function authorize(): bool
    // {
    //     return true;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'lesson_id' => 'required',
            //|exists:lessons,id'
            'title' => 'string|max:255',
            'description' => 'required|string',
            'passing_score' => 'required|numeric',
            'time_limit' => 'required|integer'
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'   => 400,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()->first()
        ]));
    }
}
