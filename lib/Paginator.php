<?php

namespace Lib;

use Core\Constants\Constants;
use Core\Database\QueryBuilder\ConditionFactory;
use Core\Database\QueryBuilder\Condition;
use Core\Database\Database;
use Core\Database\ActiveRecord\Model;
use PDO;
use PDOStatement;

/**
 *  @template T of Model
 */
class Paginator
{
    private ConditionFactory $conditionFactory;
    /**
     * @var array<int,Condition>
     */
    private array $conditions = [];

    /**
     * @param class-string<T> $for
     * @param array<int,array<int,string>> $conditions
     */
    public function __construct(private string $for, private int $length, array $conditions)
    {

        $this->conditionFactory = ConditionFactory::fromModelAttributes($this->for::columns());

        $this->conditions = $this->conditionFactory->fromConditions($conditions);
    }

    /**
     * @return Page<T>
     */
    public function getPage(int $page): Page
    {
        $sqlConditions = [];

        foreach ($this->conditions as $condition) {
            $sqlConditions[] = $condition->__toString();
        }

        $join_conditions = implode(' AND ', $sqlConditions);

        $query = <<<SQL
            SELECT * FROM {$this->for::table()} WHERE {$join_conditions} LIMIT :limit OFFSET :offset
        SQL;

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($query);

        foreach ($this->conditions as $condition) {
            $stmt->bindValue(':' . $condition->column(), $condition->value());
        }

        $stmt->bindValue(":limit", $this->length + 1, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $this->length * $page, PDO::PARAM_INT);

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];

        foreach ($rows as $row) {
            $models[] = $this->for::make($row);
        }

        return new Page(array_slice($models, 0, $this->length), $page, $this->length, count($rows) > $this->length);
    }
}
