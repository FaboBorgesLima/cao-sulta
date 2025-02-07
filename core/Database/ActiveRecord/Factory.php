<?php

namespace Core\Database\ActiveRecord;

/**
 * @template T of Model
 */
abstract class Factory
{
    /**
     * @var class-string<T> $model
     */
    protected static string $model;


    /** @return array<string,int|string|Factory<Model>> */
    abstract protected static function definition(): array;

    /**
     * @return array<T>
     */
    public static function createMany(int $count = 1): array
    {
        $models = [];

        for ($i = 0; $i < $count; $i++) {

            $models[] = static::$model::create(static::createDefinitionAttributes(static::definition()));
        }

        return $models;
    }
    /**
     * @return T
     */
    public static function create(): Model
    {
        return static::createMany(1)[0];
    }

    /**
     * @return T
     */
    public static function make(): Model
    {
        return static::makeMany(1)[0];
    }

    /**
     * @return array<T>
     */
    public static function makeMany(int $count = 1): array
    {
        $models = [];

        for ($i = 0; $i < $count; $i++) {
            $definition = static::definition();
            $attributes = [];

            foreach ($definition as $col => $value) {
                if (!$value instanceof Factory) {
                    $attributes[$col] = $value;
                }
            }

            $models[] = static::$model::make($attributes);
        }

        return $models;
    }

    /**
     * @param array<string,int|string|Factory<Model>> $definition
     * @return array<string,int|string>
     */
    static protected function createDefinitionAttributes(array $definition): array
    {
        $params = [];

        foreach ($definition as $col => $value) {

            $params[$col] = static::createSingleDefinitionAttribute($value);
        }

        return $params;
    }

    /**
     * @param int|string|Factory<Model> $value
     * @return int|string
     */
    static protected function createSingleDefinitionAttribute(mixed $value): mixed
    {
        if ($value instanceof Factory) {
            return $value->createMany()[0]->id;
        }

        return $value;
    }
}
