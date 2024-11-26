<?php

namespace App\Controller;

use Core\Http\Controllers\Controller;
use Core\Http\Response;

class TestController extends Controller
{
    function show(): void
    {
        echo "Test";
    }
}
