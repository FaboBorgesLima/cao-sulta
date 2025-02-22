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

    public function __toString()
    {

        return "$this->column $this->operator :$this->column";
    }
}
