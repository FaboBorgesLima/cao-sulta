<?php

namespace Tests\Unit\Lib;

use Lib\CRMV;
use Lib\Timestamp;
use Tests\TestCase;

class CRMVTest extends TestCase
{
    public function test_is_valid(): void
    {
        $this->assertTrue(CRMV::isValid(Timestamp::now()->getYear() . "999"));
        $this->assertTrue(CRMV::isValid("1968001"));
        $this->assertTrue(CRMV::isValid("1968999"));

        $this->assertFalse(CRMV::isValid("1967001"));
        $this->assertFalse(CRMV::isValid((Timestamp::now()->getYear() + 1) . "999"));
        $this->assertFalse(CRMV::isValid("20010001"));
        $this->assertFalse(CRMV::isValid("20011000"));
    }
}
