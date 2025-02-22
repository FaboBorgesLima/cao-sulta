<?php

namespace Core\Database\ActiveRecord;


/**
 * @template T of Model
 */
class BelongsTo
{
    /**
     * @param class-string<T> $related
     * @param string $foreignKey
     */
    public function __construct(
        private Model $model,
        private string $related,
        private string $foreignKey
    ) {}

    /**
     * @return T
     */
    public function get(): Model
    {
        $attribute = $this->foreignKey;
        return $this->related::findBy([['id', $this->model->$attribute]]);
    }
}
