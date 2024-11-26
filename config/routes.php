<?php

use App\Controller\TestController;
use Core\Router\Route;

Route::get("/", [TestController::class, "show"]);
