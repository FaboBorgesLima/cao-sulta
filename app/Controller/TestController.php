<?php

namespace App\Controller;

use Core\Http\Controllers\Controller;
use Core\Http\Response;

class TestController extends Controller
{
    public function show(): void
    {
        echo "Test";
    }
}
