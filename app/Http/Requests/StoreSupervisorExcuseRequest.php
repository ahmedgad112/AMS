<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupervisorExcuseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('save-attendance-records') ?? false;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'excuse_reason' => ['required', 'string', 'max:2000'],
            'excuse_attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'يرجى تحديد تاريخ الغياب.',
            'date.date' => 'تاريخ الغياب غير صالح.',
            'excuse_reason.required' => 'سبب العذر مطلوب.',
            'excuse_attachment.mimes' => 'المرفق يجب أن يكون صورة أو PDF.',
            'excuse_attachment.max' => 'حجم المرفق يجب ألا يتجاوز 5 ميجابايت.',
        ];
    }
}
