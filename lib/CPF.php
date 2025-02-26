<?php

namespace Lib;

class CPF
{
    public static function isValid(string $cpf): bool
    {
        if (strlen($cpf) != 11 || !StringUtils::isNumeric($cpf)) {
            return false;
        }

        $digits = substr($cpf, 0, 9);
        $input_validator_digits = substr($cpf, 9);

        $validator_digits = static::getValidatorDigits($digits);

        return $input_validator_digits == $validator_digits;
    }

    public static function getRandomCPF(): string
    {
        $digits = "";

        for ($i = 0; $i < 9; $i++) {
            $digits .= (string) rand(0, 9);
        }

        return $digits . static::getValidatorDigits($digits);
    }

    /**
     * @param string $digits - must be 9 digits in length
     */
    public static function getValidatorDigits(string $digits): string
    {
        if (strlen($digits) != 9 || !StringUtils::isNumeric($digits)) {
            return "00";
        }
        $chars = str_split($digits);

        $sum = 0;

        foreach ($chars as $pos => $char) {
            $sum += ($pos + 1) * (int) $char;
        }

        $first_digit = $sum % 11 == 10 ? "0" : (string) ($sum % 11);

        $chars[] = $first_digit;

        $sum = 0;

        foreach ($chars as $pos => $char) {
            $sum += $pos * (int) $char;
        }

        $second_digit = $sum % 11 == 10 ? "0" : (string) ($sum % 11);

        return $first_digit . $second_digit;
    }
}
