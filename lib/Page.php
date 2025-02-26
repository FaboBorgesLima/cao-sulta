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
 *  @template T
 */
class Page
{
    /**
     * @param array<int,T> $content
     */
    public function __construct(public $content, public int $n, public int $length, public bool $hasNext)
    {
        //
    }
}
