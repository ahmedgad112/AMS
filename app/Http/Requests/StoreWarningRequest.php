<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-warnings') ?? false;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'سبب الإنذار مطلوب.',
        ];
    }
}
