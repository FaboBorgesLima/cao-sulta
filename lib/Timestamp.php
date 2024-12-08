<?php

namespace Lib;

class Timestamp
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
}
