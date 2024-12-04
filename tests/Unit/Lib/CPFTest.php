<?php

namespace Tests\Unit\Lib;

use Lib\CPF;
use Tests\TestCase;

class CPFTest extends TestCase
{
    public function test_is_valid(): void
    {
        $this->assertTrue(CPF::isValid("00279920717"));
        $this->assertFalse(CPF::isValid("10279920717"));
        $this->assertFalse(CPF::isValid("0 0279920717"));
        $this->assertFalse(CPF::isValid("0a279920717"));
        $this->assertFalse(CPF::isValid("0 279920717"));
        $this->assertFalse(CPF::isValid("0b279920717"));
    }
    public function test_get_random_CPF(): void
    {
        $this->assertTrue(CPF::isValid(CPF::getRandomCPF()));
        $this->assertTrue(CPF::isValid(CPF::getRandomCPF()));
        $this->assertTrue(CPF::isValid(CPF::getRandomCPF()));
        $this->assertTrue(CPF::isValid(CPF::getRandomCPF()));
        $this->assertTrue(CPF::isValid(CPF::getRandomCPF()));
    }
}
