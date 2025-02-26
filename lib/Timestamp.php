<?php

namespace Lib;

use Stringable;

class Timestamp implements Stringable
{
    private function __construct(public int $time)
    {
        /** */
    }

    public static function now(): Timestamp
    {
        return new Timestamp(time());
    }

    public static function fromMysql(string $mysql_timestamp): Timestamp
    {
        return new Timestamp(strtotime($mysql_timestamp));
    }

    public static function fromInt(int $i): Timestamp
    {
        return new Timestamp($i);
    }

    public function toMysqlTimestamp(): string
    {
        return date('Y-m-d H:i:s', $this->time);
    }

    public function getYear(): int
    {
        return (int) date('Y', $this->time);
    }

    public function __toString(): string
    {
        return (string) $this->time;
    }
}
