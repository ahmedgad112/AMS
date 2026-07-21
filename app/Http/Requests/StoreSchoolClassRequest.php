<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-classes') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:school_classes,code'],
            'location' => ['nullable', 'string', 'max:255'],
            'inspector_ids' => ['nullable', 'array'],
            'inspector_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفصل مطلوب.',
            'code.required' => 'كود الفصل مطلوب.',
            'code.unique' => 'كود الفصل مستخدم مسبقاً.',
        ];
    }
}
