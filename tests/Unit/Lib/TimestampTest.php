<?php

namespace Tests\Unit\Lib;

use Lib\Timestamp;
use Tests\TestCase;

class TimestampTest extends TestCase
{
    public function test_now(): void
    {
        $ts = Timestamp::now();

        $this->assertEquals($ts->time, time());
    }

    public function test_to_mysql_timestamp(): void
    {
        $ts = Timestamp::fromInt(0);

        $this->assertEquals($ts->toMysqlTimestamp(), "1970-01-01 00:00:00");

        $ts = Timestamp::fromInt(PHP_INT_MAX);

        $this->assertEquals($ts->toMysqlTimestamp(), "292277026596-12-04 15:30:07");
    }

    public function test_from_mysql_timestamp(): void
    {
        $ts = Timestamp::fromMysql("1970-01-01 00:00:00");

        $this->assertEquals($ts->time, 0);
    }
}
