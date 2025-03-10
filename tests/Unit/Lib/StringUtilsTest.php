<?php

namespace Tests\Unit\Lib;

use Lib\StringUtils;
use Tests\TestCase;

class StringUtilsTest extends TestCase
{
    public function test_is_upper_camel_case(): void
    {
        $this->assertTrue(StringUtils::isUpperCamelCase("TestTest"));
        $this->assertTrue(StringUtils::isUpperCamelCase("CPF"));
        $this->assertTrue(StringUtils::isUpperCamelCase("CPFTest"));
        $this->assertTrue(StringUtils::isUpperCamelCase("TestTest0"));

        $this->assertFalse(StringUtils::isUpperCamelCase("tEST"));
        $this->assertFalse(StringUtils::isUpperCamelCase("Test Test"));
        $this->assertFalse(StringUtils::isUpperCamelCase("testtest"));
        $this->assertFalse(StringUtils::isUpperCamelCase("0testtest"));
    }

    public function test_is_numeric(): void
    {
        $this->assertTrue(StringUtils::isNumeric("0123456789"));
        $this->assertTrue(StringUtils::isNumeric("0000"));
        $this->assertFalse(StringUtils::isNumeric("012345 6789"));
        $this->assertFalse(StringUtils::isNumeric("012345a122"));
        $this->assertFalse(StringUtils::isNumeric("012345aaaa89"));
        for ($i = 0; $i < 10; $i++) {
            $this->assertTrue(StringUtils::isNumeric((string) rand(0, 10000000)));
        }
    }
}
