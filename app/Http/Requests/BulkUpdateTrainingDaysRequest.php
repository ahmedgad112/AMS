<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdateTrainingDaysRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('edit-supervisors') ?? false;
    }

    public function rules(): array
    {
        return [
            'total_training_days' => ['required', 'integer', 'min:1', 'max:365'],
            'school_class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'status' => ['nullable', Rule::in(['active', 'completed', 'suspended'])],
        ];
    }

    public function messages(): array
    {
        return [
            'total_training_days.required' => 'عدد أيام التدريب مطلوب.',
            'total_training_days.min' => 'عدد أيام التدريب يجب أن يكون 1 على الأقل.',
            'total_training_days.max' => 'عدد أيام التدريب يجب ألا يتجاوز 365.',
        ];
    }
}
