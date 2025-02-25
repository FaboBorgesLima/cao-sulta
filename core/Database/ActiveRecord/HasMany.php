<?php

namespace Core\Database\ActiveRecord;

use Lib\Paginator;

/**
 * @template T of Model 
 */
class HasMany
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
     * @return array<T>
     */
    public function get(): array
    {
        return $this->related::where([
            [$this->foreignKey, $this->model->id]
        ]);
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

    /**
     * @return T|null
     */
    public function findById(int $id): ?Model
    {
        return $this->related::findBy(
            [
                $this->foreignKey => $this->model->id,
                'id' => $id,
            ]
        );
    }

    /** 
     * @param array<int, array<int, string>> $conditions 
     * @return Paginator<T>
     * */
    public function paginate(int $length, array $conditions): Paginator
    {
        return new Paginator(
            $this->related,
            $length,
            $conditions
        );
    }
}
