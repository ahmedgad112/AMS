<?php

namespace App\Support;

class PhoneNormalizer
{
    public static function normalize(?string $phone): string
    {
        if ($phone === null || trim($phone) === '') {
            return '';
        }

        $phone = trim($phone);

        if (is_numeric($phone)) {
            $phone = sprintf('%.0f', (float) $phone);
        }

        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '20') && strlen($digits) >= 12) {
            $digits = '0'.substr($digits, 2);
        }

        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $digits = '0'.$digits;
        }

        return $digits;
    }

    public static function isValid(?string $phone): bool
    {
        $normalized = self::normalize($phone);

        return $normalized !== '' && (bool) preg_match('/^01\d{9}$/', $normalized);
    }

    /**
     * @return array<int, string>
     */
    public static function validationRules(bool $required = false): array
    {
        $rules = ['string', 'regex:/^01\d{9}$/'];

        array_unshift($rules, $required ? 'required' : 'nullable');

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public static function validationMessages(): array
    {
        return [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.regex' => 'رقم الهاتف يجب أن يكون 11 رقم ويبدأ بـ 01.',
        ];
    }
}
