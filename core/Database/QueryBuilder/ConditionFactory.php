<?php

namespace Core\Database\QueryBuilder;

use InvalidArgumentException;

class ConditionFactory
{
    /**
     * @param array<string,mixed> $columnsHashMap
     */
    private function __construct(private array $columnsHashMap, private bool $stricColumns = true) {}

    /**
     * @param array<string> $attributes
     */
    public static function fromModelAttributes(array $attributes): ConditionFactory
    {
        $attributesHashMap = array_flip($attributes);
        $attributesHashMap["id"] = null;
        return new ConditionFactory($attributesHashMap);
    }

    public static function make(): ConditionFactory
    {
        return new ConditionFactory([], false);
    }

    /**
     * @param array<int,string|int>|array<int,array<int|string>> $queries
     * @return array<int,Condition>
     */
    public function fromConditions($queries): array
    {
        $conditions = [];

        if (gettype($queries[0]) != 'array') {
            $conditions[] = $this->fromArray($queries);
        } else {
            foreach ($queries as $query) {
                $conditions[] = $this->fromArray($query);
            }
        }

        return $conditions;
    }

    /**
     * @param array<int,string|int> $columnConditionValue
     */
    public function fromArray(array $columnConditionValue): Condition
    {
        $numberArguments = count($columnConditionValue);

        switch ($numberArguments) {
            case 0:
            case 1:
                throw new InvalidArgumentException("must have 2 or 3 arguments");
            case 2:
                if ($this->stricColumns && !key_exists($columnConditionValue[0], $this->columnsHashMap)) {
                    throw new InvalidArgumentException("invalid column '$columnConditionValue[0]'");
                }
                return new Condition($columnConditionValue[0], "=", $columnConditionValue[1]);
            case 3:
                if ($this->stricColumns && !key_exists($columnConditionValue[0], $this->columnsHashMap)) {
                    throw new InvalidArgumentException("invalid column '$columnConditionValue[0]'");
                }
                return new Condition($columnConditionValue[0], $columnConditionValue[1], $columnConditionValue[2]);
            default:
                throw new InvalidArgumentException("too many values");
        }
    }
}
