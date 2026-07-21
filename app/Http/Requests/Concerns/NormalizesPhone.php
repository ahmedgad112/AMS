<?php

namespace App\Http\Requests\Concerns;

use App\Support\PhoneNormalizer;

trait NormalizesPhone
{
    protected function prepareForValidation(): void
    {
        if (! $this->has('phone')) {
            return;
        }

        $phone = $this->input('phone');

        if ($phone === null || trim((string) $phone) === '') {
            $this->merge(['phone' => null]);

            return;
        }

        $this->merge(['phone' => PhoneNormalizer::normalize((string) $phone)]);
    }
}
