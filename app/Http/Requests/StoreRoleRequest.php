<?php

namespace App\Http\Requests;

use App\Support\PermissionCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-roles') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9\-]+$/',
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(PermissionCatalog::allNames())],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الدور مطلوب.',
            'name.regex' => 'اسم الدور يجب أن يكون بالإنجليزية (حروف صغيرة وأرقام وشرطة فقط).',
            'name.unique' => 'اسم الدور مستخدم مسبقاً.',
        ];
    }
}
