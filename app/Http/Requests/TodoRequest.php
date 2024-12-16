<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TodoRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                Rule::unique('todos', 'title')
                    ->ignore($this->route('todo'), 'title')
            ],
            'details' => ['required', 'string'],
            'status' => ['nullable', Rule::in('completed', 'not started', 'in progress')],
        ];
    }
}
