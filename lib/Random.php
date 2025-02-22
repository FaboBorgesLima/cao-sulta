<?php

namespace Lib;

class Random
{
    private static array $names = [
        "José",
        "Pedro",
        "Nicolas",
        "any",
        "Maria",
        "Silva",
        "joão",
        "caio",
        "Amber",
        "Sophia",
        "Johanne",
        "Jade",
        "mario",
        "Leslie",
    ];
    public static function token(): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(hash("sha256", random_bytes(256), true)));
    }

    public static function alfabeticString(int $length): string
    {
        $out = "";

        for ($i = 0; $i < $length; $i++) {
            $out .= static::char("a", "z");
        }

        return $out;
    }

    public static function char(string $start, string $end): string
    {
        return chr(random_int(ord($start), ord($end)));
    }

    public static function CRMV(): string
    {
        return (string) random_int(CRMV::MIN_YEAR, Timestamp::now()->getYear())
            . StringUtils::intToStringPaddingLeft(random_int(0, 999), 3);
    }

    public static function CPF(): string
    {
        return CPF::getRandomCPF();
    }

    public static function email(): string
    {
        return Random::alfabeticString(25) . '@' . implode(".", [
            Random::alfabeticString(5),
            Random::alfabeticString(3),
            Random::alfabeticString(2)
        ]);
        ;
    }

    public static function state(): string
    {
        return static::itemFromArray(State::getStates());
    }

    public static function name(): string
    {
        return implode(" ", [
            static::itemFromArray(static::$names),
            static::itemFromArray(static::$names)
        ]);
    }

    /**
     * @template T
     * @template I
     * @param array<T,I> $arr
     * @return I
     */
    public static function keyFromArray(array $arr): int | string
    {
        return array_rand($arr, 1);
    }

    /**
     * @template T
     * @template I
     * @param array<T,I> $arr
     * @return I
     */
    public static function itemFromArray(array $arr): mixed
    {
        return $arr[static::keyFromArray($arr)];
    }
}
