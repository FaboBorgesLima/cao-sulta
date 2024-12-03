<?php

namespace Core\Database\ActiveRecord;

class HasOne
{
    public function __construct(
        private Model $model,
        private string $related,
        private string $foreignKey
    ) {}

    public function get(): ?Model
    {
        return $this->related::findBy([$this->foreignKey => $this->model->id]);
    }

    /**
     * @param array<string, mixed> $params
     */
    public function new(array $params = []): Model
    {
        $params[$this->foreignKey] = $this->model->id;

        return new $this->related($params);
    }
}
