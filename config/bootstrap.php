<?php

use Core\Env\EnvLoader;
use Core\Router\Router;

session_start();

require __DIR__ . "/../vendor/autoload.php";

EnvLoader::init();
Router::init();
