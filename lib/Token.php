<?php

namespace Lib;

class Token
{
    public static function make(): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(hash("sha256", random_bytes(256), true)));
    }
}
