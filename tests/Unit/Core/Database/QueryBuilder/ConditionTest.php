<?php

namespace Tests\Unit\Core\Database\QueryBuilder;

use Core\Database\QueryBuilder\Condition;
use Tests\TestCase;

class ConditionTest extends TestCase
{
    public function test_to_string(): void
    {
        $condition = new Condition("name", "=", "a cool name");

        $this->assertEquals("name = :name", $condition->__toString());
        $this->assertEquals("a cool name", $condition->value());
    }
}
