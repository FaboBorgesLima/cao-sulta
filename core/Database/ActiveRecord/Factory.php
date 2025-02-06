<?php

namespace Core\Database\ActiveRecord;

/**
 * @template T
 * @param class-string<T> $className
 * @param int $id
 * @return T|null
 */
abstract class Factory
{

    /**
     * @template T
     * @param class-string<T> $model
     */
    protected function __construct(protected string $model)
    {
        //
    }

    abstract protected function definition(): array;
}
