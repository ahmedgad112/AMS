<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-evaluations') ?? false;
    }

    public function rules(): array
    {
        return [
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'score.required' => 'الدرجة مطلوبة.',
            'score.min' => 'الدرجة يجب أن تكون بين 0 و 100.',
            'score.max' => 'الدرجة يجب أن تكون بين 0 و 100.',
        ];
    }
}
