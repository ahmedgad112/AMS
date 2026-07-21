<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use App\Support\ClassAuthorization;
use App\Support\PhoneNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupervisorRequest extends FormRequest
{
    use NormalizesPhone;
    public function authorize(): bool
    {
        if (! $this->user()?->can('edit-supervisors')) {
            return false;
        }

        $supervisor = $this->route('supervisor');

        return ClassAuthorization::canAccessClass(
            $this->user(),
            $supervisor->school_class_id
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => PhoneNormalizer::validationRules(),
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'total_training_days' => ['required', 'integer', 'min:1', 'max:365'],
            'deducted_days' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'completed', 'suspended'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المشرف مطلوب.',
            'school_class_id.required' => 'الفصل مطلوب.',
            ...PhoneNormalizer::validationMessages(),
        ];
    }
}
