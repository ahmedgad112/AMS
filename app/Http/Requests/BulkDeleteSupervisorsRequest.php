<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkDeleteSupervisorsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('delete-supervisors') ?? false;
    }

    public function rules(): array
    {
        return [
            'delete_all_filtered' => ['nullable', 'boolean'],
            'supervisor_ids' => ['required_without:delete_all_filtered', 'array', 'min:1'],
            'supervisor_ids.*' => ['integer', 'exists:supervisors,id'],
            'search' => ['nullable', 'string', 'max:255'],
            'school_class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'status' => ['nullable', Rule::in(['active', 'completed', 'suspended'])],
            'warnings' => ['nullable', Rule::in(['active', 'deducted'])],
        ];
    }

    public function messages(): array
    {
        return [
            'supervisor_ids.required_without' => 'يرجى تحديد مشرف واحد على الأقل للحذف.',
            'supervisor_ids.min' => 'يرجى تحديد مشرف واحد على الأقل للحذف.',
        ];
    }
}
