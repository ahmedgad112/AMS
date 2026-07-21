<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRecordsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('save-attendance-records') ?? false;
    }

    public function rules(): array
    {
        return [
            'records' => ['required', 'array', 'min:1'],
            'records.*.supervisor_id' => ['required', 'integer', 'exists:supervisors,id'],
            'records.*.status' => ['required', Rule::in(['present', 'absent', 'late', 'excused'])],
            'records.*.excuse_reason' => ['nullable', 'string', 'max:2000'],
            'records.*.excuse_attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            foreach ($this->input('records', []) as $index => $record) {
                if (($record['status'] ?? null) === 'excused' && empty($record['excuse_reason'])) {
                    $validator->errors()->add(
                        "records.{$index}.excuse_reason",
                        'سبب العذر مطلوب عند اختيار غياب بعذر.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'records.required' => 'يجب إدخال سجل حضور واحد على الأقل.',
            'records.*.status.required' => 'حالة الحضور مطلوبة.',
            'records.*.excuse_attachment.mimes' => 'المرفق يجب أن يكون صورة أو PDF.',
            'records.*.excuse_attachment.max' => 'حجم المرفق يجب ألا يتجاوز 5 ميجابايت.',
        ];
    }
}
