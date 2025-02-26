<?php

namespace Core\Database\QueryBuilder;

class Condition
{
    public function __construct(
        protected string $column,
        protected string $operator,
        protected mixed $value
    ) {
        //
    }

    public function column(): string
    {
        return $this->column;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function placeholder(): string
    {
        return ':' . str_replace('.', "dot", $this->column());
    }

    public function __toString()
    {

        return "$this->column $this->operator {$this->placeholder()}";
    }
}
