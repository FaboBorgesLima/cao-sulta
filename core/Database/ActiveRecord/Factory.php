<?php

namespace Core\Database\ActiveRecord;

/**
 * @template T of Model
 */
abstract class Factory
{
    /**
     * @param T $model
     */
    protected function __construct(protected Model $model)
    {
        //
    }

    /** @return array<string,int|string|Factory<Model>> */
    abstract protected function definition(): array;

    /**
     * @return array<T>
     */
    public function create(int $count = 1): array
    {
        $models = [];

        for ($i = 0; $i < $count; $i++) {

            $models[] = $this->model::create(static::createDefinitionAtributtes($this->definition()));
        }

        return $models;
    }

    /**
     * @param array<string,int|string|Factory<Model>> $definition
     * @return array<string,int|string>
     */
    static protected function createDefinitionAtributtes(array $definition): array
    {
        $params = [];

        foreach ($definition as $col => $value) {

            $params[$col] = static::createSingleDefinitionAtributte($value);
        }

        return $params;
    }

    /**
     * @param int|string|Factory<Model> $value
     * @return int|string
     */
    static protected function createSingleDefinitionAtributte(mixed $value): mixed
    {
        if ($value instanceof Factory) {
            return $value->create()[0]->id;
        }

        return $value;
    }
}
