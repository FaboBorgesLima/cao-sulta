<?php

namespace Config;

class App
{
    public static array $middlewareAliases = [
        'auth' => \App\Middleware\Authenticate::class,
        'vet' => \App\Middleware\Veterinary::class
    ];
}
