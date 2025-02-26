<?php

namespace Tests\Unit\Core\Database\QueryBuilder;

use Core\Database\QueryBuilder\Condition;
use Core\Database\QueryBuilder\ConditionFactory;
use InvalidArgumentException;
use Tests\TestCase;

class ConditionFactoryTest extends TestCase
{
    public function test_from_model_attributes(): void
    {
        $factory = ConditionFactory::fromModelAttributes(["name", "age"]);

        $condition = $factory->fromArray(["age", "<", 18]);

        $this->assertInstanceOf(Condition::class, $condition);
        $this->assertEquals("age < :age", $condition->__toString());
        $this->assertEquals(18, $condition->value());

        $condition = $factory->fromArray(["name", "cool name"]);

        $this->assertInstanceOf(Condition::class, $condition);

        $this->assertEquals("name = :name", $condition->__toString());
        $this->assertEquals("cool name", $condition->value());

        $this->expectException(InvalidArgumentException::class);

        $factory->fromArray(["not_a_attribute", "=", "cool name"]);

        $condition = $factory->fromArray(["id", 3]);

        $this->assertInstanceOf(Condition::class, $condition);

        // --------------------- id automatic attribute

        $this->assertEquals("id = :id", $condition->__toString());
        $this->assertEquals(3, $condition->value());

        // --------------------- updated_at automatic attribute

        $condition = $factory->fromArray(["updated_at", 1]);

        $this->assertInstanceOf(Condition::class, $condition);

        $this->assertEquals("updated_at = :updated_at", $condition->__toString());
        $this->assertEquals(1, $condition->value());

        // --------------------- created_at automatic attribute

        $condition = $factory->fromArray(["created_at", 10]);

        $this->assertInstanceOf(Condition::class, $condition);

        $this->assertEquals("created_at = :created_at", $condition->__toString());
        $this->assertEquals(10, $condition->value());
    }
}
