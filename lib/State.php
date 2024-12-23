<?php

namespace Lib;

class State
{
    /** @var array<int,string> */
    private static array $states = [
        "AC",
        "AL",
        "AP",
        "AM",
        "BA",
        "CE",
        "DF",
        "ES",
        "GO",
        "MA",
        "MT",
        "MS",
        "MG",
        "PA",
        "PB",
        "PR",
        "PE",
        "PI",
        "RJ",
        "RN",
        "RS",
        "RO",
        "RR",
        "SC",
        "SP",
        "SE",
        "TO"
    ];

    /**
     * @return array<int,string>
     */
    public static function getStates(): array
    {
        return static::$states;
    }

    public static function isState(string $str): bool
    {
        foreach (static::$states as $state) {
            if ($str == $state) {
                return true;
            }
        }

        return false;
    }
}
