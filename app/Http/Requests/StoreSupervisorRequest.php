<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use App\Support\ClassAuthorization;
use App\Support\PhoneNormalizer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupervisorRequest extends FormRequest
{
    use NormalizesPhone;
    public function authorize(): bool
    {
        if (! $this->user()?->can('create-supervisors')) {
            return false;
        }

        return ClassAuthorization::canAccessClass(
            $this->user(),
            (int) $this->input('school_class_id')
        );
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => PhoneNormalizer::validationRules(),
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
            'total_training_days' => ['required', 'integer', 'min:1', 'max:365'],
            'status' => ['required', Rule::in(['active', 'completed', 'suspended'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المشرف مطلوب.',
            'school_class_id.required' => 'الفصل مطلوب.',
            'total_training_days.required' => 'إجمالي أيام التدريب مطلوب.',
            ...PhoneNormalizer::validationMessages(),
        ];
    }
}
