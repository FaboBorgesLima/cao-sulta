<?php

namespace App\Controller;

use Core\Http\Controllers\Controller;
use Core\Http\Response;
use Lib\Authentication\Auth;

class StaticPagesController extends Controller
{
    public function home(): Response
    {
        if (Auth::check()) {
            return Response::redirectTo(route("dashboard"));
        }
        return Response::render("home");
    }

    public function register(): Response
    {
        if (Auth::check()) {
            return Response::redirectTo(route("dashboard"));
        }
        return Response::render("register");
    }
}
