<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesPhone;
use App\Support\PhoneNormalizer;
use Illuminate\Foundation\Http\FormRequest;

class SupervisorInquiryRequest extends FormRequest
{
    use NormalizesPhone;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => PhoneNormalizer::validationRules(required: true),
        ];
    }

    public function messages(): array
    {
        return PhoneNormalizer::validationMessages();
    }
}
