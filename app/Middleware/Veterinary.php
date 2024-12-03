<?php

namespace App\Middleware;

use Core\Http\Middleware\Middleware;
use Core\Http\Request;
use Lib\Authentication\Auth;

class Veterinary implements Middleware
{
    public function handle(Request $request): void
    {
        if (!Auth::isVet()) {
            $this->redirectTo(route('auth.new'));
        }
    }

    private function redirectTo(string $location): void
    {
        header('Location: ' . $location);
        exit;
    }
}
