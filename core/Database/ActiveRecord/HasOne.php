<?php

namespace Core\Database\ActiveRecord;

/**
 * @template T of Model 
 */
class HasOne
{
    /**
     * @param class-string<T> $related
     */
    public function __construct(
        private Model $model,
        private string $related,
        private string $foreignKey
    ) {
        //
    }

    /**
     * 
     * @return T 
     */
    public function get(): ?Model
    {
        return $this->related::findBy([$this->foreignKey, $this->model->id]);
    }

    /**
     * @param array<string, mixed> $params
     * @return T
     */
    public function new(array $params = []): Model
    {
        $params[$this->foreignKey] = $this->model->id;

        return $this->related::make($params);
    }
}
