<?php

namespace Tests\Unit;

use App\Support\PhoneNormalizer;
use PHPUnit\Framework\TestCase;

class PhoneNormalizerTest extends TestCase
{
    public function test_normalizes_ten_digit_excel_number_by_prepending_zero(): void
    {
        $this->assertSame('01012345678', PhoneNormalizer::normalize('1012345678'));
    }

    public function test_normalizes_numeric_string_from_excel(): void
    {
        $this->assertSame('01111111111', PhoneNormalizer::normalize('1111111111'));
    }

    public function test_normalizes_international_format(): void
    {
        $this->assertSame('01012345678', PhoneNormalizer::normalize('+201012345678'));
    }

    public function test_validates_eleven_digit_egyptian_mobile(): void
    {
        $this->assertTrue(PhoneNormalizer::isValid('01012345678'));
        $this->assertTrue(PhoneNormalizer::isValid('1012345678'));
        $this->assertFalse(PhoneNormalizer::isValid('0101234567'));
        $this->assertFalse(PhoneNormalizer::isValid('0212345678'));
    }
}
