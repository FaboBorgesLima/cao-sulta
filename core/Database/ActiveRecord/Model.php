<?php

namespace Core\Database\ActiveRecord;

use Core\Database\Database;
use Core\Database\QueryBuilder\ConditionFactory;
use Lib\Paginator;
use Lib\StringUtils;
use Lib\Timestamp;
use PDO;
use ReflectionMethod;

/**
 * Class Model
 * @package Core\Database\ActiveRecord
 * @property ?int $id
 */
abstract class Model
{
    /** @var array<string, string> */
    protected array $errors = [];
    protected ?int $id = null;
    public ?Timestamp $created_at = null;
    public ?Timestamp $updated_at = null;

    /** @var array<string, mixed> */
    private array $attributes = [];

    protected static string $table = '';
    /** @var array<int,string> */
    protected static array $columns = [];

    /** @var array<string,string|int|float|bool> */
    protected static array $default = [];

    /** @var array<int,string> */
    protected static array $hidden = [];

    /** @var array<string,mixed> */
    private array $hide = [];

    /**
     * @param array<string, mixed> $params
     */
    private function __construct($params = [])
    {
        $this->hide = array_flip(static::$hidden);


        // Initialize attributes with null or default from database columns
        foreach (static::$columns as $column) {
            $this->attributes[$column] = null;
        }

        foreach (static::$default as $column => $value) {
            $this->__set($column, $value);
        }

        foreach ($params as $property => $value) {
            $this->__set($property, $value);
        }
    }

    /* ------------------- SERIALIZATION METHODS ------------------- */

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        /** @var array<string,mixed> */
        $arr = [
            "id" => $this->id,
            "updated_at" => $this->updated_at,
            "created_at" => $this->created_at
        ];

        foreach ($this->attributes as $name => $value) {

            if (!key_exists($name, $this->hide)) {
                $arr[$name] = $value;
            }
        }

        return $arr;
    }

    public function makeVisible(string $attribute): self
    {
        unset($this->hide[$attribute]);

        return $this;
    }

    public function makeHidden(string $attribute): self
    {
        $this->hide[$attribute] = null;

        return $this;
    }

    /* ------------------- MAGIC METHODS ------------------- */
    public function __get(string $property): mixed
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (array_key_exists($property, $this->attributes)) {
            return $this->attributes[$property];
        }

        $method = StringUtils::lowerSnakeToCamelCase($property);
        if (method_exists($this, $method)) {
            $reflectionMethod = new ReflectionMethod($this, $method);
            $returnType = $reflectionMethod->getReturnType();

            $allowedTypes = [
                'Core\Database\ActiveRecord\BelongsTo',
                'Core\Database\ActiveRecord\HasMany',
                'Core\Database\ActiveRecord\BelongsToMany',
                'Core\Database\ActiveRecord\HasOne'
            ];

            if ($returnType !== null && in_array($returnType->getName(), $allowedTypes)) {
                return $this->$method()->get();
            }
        }

        throw new \Exception("Property {$property} not found in " . static::class);
    }

    public function __set(string $property, mixed $value): void
    {
        if ($property == "updated_at" || $property == "created_at") {
            if (is_string($value)) {
                $this->$property = Timestamp::fromMysql($value);
                return;
            }
            $this->$property = $value;
            return;
        }

        if (property_exists($this, $property)) {
            $this->$property = $value;
            return;
        }

        if (array_key_exists($property, $this->attributes)) {
            $this->attributes[$property] = $value;
            return;
        }

        throw new \Exception("Property {$property} not found in " . static::class);
    }

    public static function table(): string
    {
        return static::$table;
    }

    /**
     * @return array<int, string>
     */
    public static function columns(): array
    {
        return static::$columns;
    }

    /* ------------------- VALIDATIONS METHODS ------------------- */
    public function isValid(): bool
    {
        $this->errors = [];

        $this->validates();

        return empty($this->errors);
    }

    public function newRecord(): bool
    {
        return $this->id === null;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function errors(string $index = null): string | null
    {
        if (isset($this->errors[$index])) {
            return $this->errors[$index];
        }

        return null;
    }

    /**
     * @return array<string,string>
     */
    public function getAllErrors(): array
    {
        return $this->errors;
    }

    public function addError(string $index, string $value): void
    {
        $this->errors[$index] = $value;
    }

    public abstract function validates(): void;

    /* ------------------- DATABASE METHODS ------------------- */
    public function save(): bool
    {
        if ($this->isValid()) {
            $pdo = Database::getDatabaseConn();
            if ($this->newRecord()) {
                $table = static::$table;
                $attributes = implode(', ', static::$columns);
                $values = ':' . implode(', :', static::$columns);

                $this->created_at = Timestamp::now();

                $sql = <<<SQL
                    INSERT INTO {$table} ({$attributes},created_at) VALUES ({$values},:created_at);
                SQL;

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(":created_at", $this->created_at->toMysqlTimestamp());

                foreach (static::$columns as $column) {
                    $stmt->bindValue($column, $this->$column);
                }

                $stmt->execute();

                $this->id = (int)$pdo->lastInsertId();
            } else {
                $this->updated_at = Timestamp::now();

                $table = static::$table;

                $sets = array_map(function ($column) {
                    return "{$column} = :{$column}";
                }, static::$columns);
                $sets = implode(', ', $sets);


                $sql = <<<SQL
                    UPDATE {$table} set {$sets},updated_at = :updated_at WHERE id = :id;
                SQL;

                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $this->id);
                $stmt->bindValue(':updated_at', $this->updated_at->toMysqlTimestamp());
                foreach (static::$columns as $column) {
                    $stmt->bindValue($column, $this->$column);
                }

                $stmt->execute();
            }
            return true;
        }
        return false;
    }


    /**
     * will only make the model, without saving
     * @param array<string, mixed> $params
     * @return static
     */
    public static function make($params): Model
    {
        return new static($params);
    }

    /**
     * will create the model, saving it
     * @param array<string, mixed> $params
     * @return static
     */
    public static function create($params): Model
    {
        $model = new static($params);

        $model->save();

        return $model;
    }


    /**
     * @param array<string, mixed> $data
     */
    public function update(array $data): bool
    {
        $table = static::$table;

        $sets = array_map(function ($column) {
            return "{$column} = :{$column}";
        }, array_keys($data));
        $sets = implode(', ', $sets);

        $this->updated_at = Timestamp::now();

        $sql = <<<SQL
            UPDATE {$table} set {$sets},updated_at = :updated_at WHERE id = :id;
        SQL;

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $this->id);
        $stmt->bindValue(':updated_at', $this->updated_at->toMysqlTimestamp());

        foreach ($data as $column => $value) {
            $stmt->bindValue($column, $value);
        }

        $stmt->execute();
        return ($stmt->rowCount() !== 0);
    }

    public function destroy(): bool
    {
        $table = static::$table;

        $sql = <<<SQL
            DELETE FROM {$table} WHERE id = :id;
        SQL;

        $pdo = Database::getDatabaseConn();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $this->id);

        $stmt->execute();

        return ($stmt->rowCount() != 0);
    }

    public static function findById(int $id): static|null
    {
        $pdo = Database::getDatabaseConn();

        $attributes = implode(', ', static::$columns);
        $table = static::$table;

        $sql = <<<SQL
            SELECT id,created_at,updated_at,{$attributes} FROM {$table} WHERE id = :id;
        SQL;

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return new static($row);
    }

    /**
     * @return array<static>
     */
    public static function all(): array
    {
        $models = [];

        $attributes = implode(', ', static::$columns);
        $table = static::$table;

        $sql = <<<SQL
            SELECT id,created_at,updated_at, {$attributes} FROM {$table};
        SQL;

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $resp = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resp as $row) {
            $models[] = new static($row);
        }

        return $models;
    }

    public static function paginate(int $page = 1, int $per_page = 10, string $route = null): Paginator
    {
        return new Paginator(
            class: static::class,
            page: $page,
            per_page: $per_page,
            table: static::$table,
            attributes: static::$columns,
            route: $route
        );
    }

    /**
     * @param array<int, mixed> $queries
     * @return array<static>
     */
    public static function where(array $queries): array
    {
        $table = static::$table;
        $attributes = implode(', ', static::$columns);

        $sql = <<<SQL
            SELECT id,created_at,updated_at, {$attributes} FROM {$table} WHERE 
        SQL;

        $conditionFactory = ConditionFactory::fromModelAttributes(static::$columns);

        $conditions = [];

        if (gettype($queries[0]) != 'array') {
            $conditions[] = $conditionFactory->fromArray($queries);
        } else {
            foreach ($queries as $query) {
                $conditions[] = $conditionFactory->fromArray($query);
            }
        }
        $sqlConditions = array_map(function ($condition) {
            return $condition->__toString();
        }, $conditions);


        $sql .= implode(' AND ', $sqlConditions);

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);

        foreach ($conditions as $condition) {
            $stmt->bindValue($condition->column(), $condition->value());
        }

        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($rows as $row) {
            $models[] = new static($row);
        }
        return $models;
    }

    /**
     * @param array<mixed> $query
     */
    public static function findBy($query): ?static
    {
        $resp = self::where($query);
        if (isset($resp[0]))
            return $resp[0];

        return null;
    }

    /**
     * @param array<string, mixed> $query
     */
    public static function exists($query): bool
    {
        $resp = self::where([$query]);
        return !empty($resp);
    }

    /* ------------------- RELATIONSHIPS METHODS ------------------- */

    /**
     * @template T of Model
     * @param class-string<T> $related
     * @param string $foreignKey
     * @return BelongsTo<T>
     */
    public function belongsTo(string $related, string $foreignKey): BelongsTo
    {
        return new BelongsTo($this, $related, $foreignKey);
    }

    /**
     * @template T of Model 
     * @param class-string<T> $related
     * @return HasMany<T>
     */
    public function hasMany(string $related, string $foreignKey): HasMany
    {
        return new HasMany($this, $related, $foreignKey);
    }

    /**
     * @template T of Model 
     * @param class-string<T> $related
     * @return HasOne<T>
     */
    public function hasOne(string $related, string $foreignKey): HasOne
    {
        return new HasOne($this, $related, $foreignKey);
    }

    /**
     * @template T of Model 
     * @param class-string<T> $related
     * @return BelongsToMany<T>
     */
    public function belongsToMany(string $related, string $pivot_table, string $from_foreign_key, string $to_foreign_key): BelongsToMany
    {
        return new BelongsToMany($this, $related, $pivot_table, $from_foreign_key, $to_foreign_key);
    }
}
