<?php

namespace Core\Database\ActiveRecord;

use Core\Database\Database;
use Core\Database\QueryBuilder\Condition;
use Core\Database\QueryBuilder\ConditionFactory;
use PDO;

/**
 * @template TRelated of Model
 * @template TPivot of Model 
 */
class HasManyThrough
{
    private ConditionFactory $conditionFactory;
    /**
     * @param class-string<TRelated> $related
     * @param class-string<TPivot> $pivot
     */
    public function __construct(
        private Model $model,
        private string $related,
        private string $pivot,
        private string $foreignKeyModel,
        private string $foreignKeyRelated
    ) {
        $this->conditionFactory = ConditionFactory::make();
        //
    }

    /**
     * @return array<int,TRelated>
     */
    public function get(): array
    {
        $pdo = Database::getDatabaseConn();

        $sql = $this->baseQuery();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $this->model->id);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /** @var array<int,TRelated> */
        $models = [];

        foreach ($rows as $row) {
            $models[] = $this->related::make($row);
        }

        return $models;
    }


    private function baseQuery(): string
    {
        return <<<SQL
            SELECT {$this->related::table()}.*
            FROM {$this->pivot::table()}
            INNER JOIN {$this->related::table()}
            ON {$this->related::table()}.id = {$this->pivot::table()}.{$this->foreignKeyRelated} 
            INNER JOIN {$this->model::table()}
            ON {$this->model::table()}.id = {$this->pivot::table()}.{$this->foreignKeyModel}
            WHERE {$this->model::table()}.id = :id 
        SQL;
    }

    /**
     * @param array<int,string|int>|array<int,array<int|string>> $conditions
     * @return array<int,TRelated>
     */
    public function where($conditions): array
    {
        $condition_instances = $this->conditionFactory->fromConditions($conditions);
        $sql = '';

        if ($condition_instances) {
            $sql = $this->baseQuery() . ' AND ';
        }

        $conditions_sql_pieces = [];

        foreach ($condition_instances as $condition_instance) {
            $conditions_sql_pieces[] = $condition_instance->__toString();
        }

        $sql .= implode(' AND ', $conditions_sql_pieces);

        $pdo = Database::getDatabaseConn();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(":id", $this->model->id);

        foreach ($condition_instances as $condition_instance) {

            $stmt->bindValue($condition_instance->placeholder(), $condition_instance->value(), PDO::PARAM_STR);
        }

        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /** @var array<int,TRelated> */
        $models = [];

        foreach ($rows as $row) {
            $models[] = $this->related::make($row);
        }

        return $models;
    }
}
