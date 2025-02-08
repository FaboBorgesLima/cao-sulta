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

    public function paginate(int $page = 1, int $per_page = 10, string $route = null): Paginator
    {
        return new Paginator(
            class: $this->related,
            page: $page,
            per_page: $per_page,
            table: $this->related::table(),
            attributes: $this->related::columns(),
            conditions: [$this->foreignKey => $this->model->id],
            route: $route
        );
    }
}
