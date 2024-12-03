<?php

namespace Lib;

class StringUtils
{
    public static function camelToSnakeCase($string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function lowerSnakeToCamelCase($string)
    {
        $camel_case = preg_replace_callback(
            '/_([a-zA-Z])/',
            fn($match) => strtoupper($match[1]),
            $string
        );

        return $camel_case;
    }

    public static function isNumeric(string $str): bool
    {
        return ! (bool) preg_match('/[^0-9]/', $str);
    }

    public static function snakeToCamelCase($string)
    {
        return ucfirst(self::lowerSnakeToCamelCase($string));
    }

    public static function isUpperCamelCase($string): bool
    {
        preg_match('/^([A-Z]+([a-z0-9]+)?)+/', $string, $matches);

        if (count($matches) == 0) {
            return false;
        }

        return $matches[0] == $string;
    }
}
