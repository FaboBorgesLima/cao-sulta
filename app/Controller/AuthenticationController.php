<?php

namespace App\Controller;

use App\Models\User;
use App\Models\UserToken;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Lib\Authentication\Auth;

class AuthenticationController extends Controller
{
    public function authenticate(Request $request): Response
    {
        Auth::login($request->getParam("token", ""));

        if (Auth::user()) {
            return Response::redirectTo(route("dashboard"));
        }

        return Response::redirectTo(route("user.login"));
    }

    public function new(): Response
    {
        return Response::render("auth/new");
    }


    public function login(Request $request): Response
    {
        $user = User::findBy([
            "email" => $request->getParam("email", ""),
            "cpf" => $request->getParam("cpf", "")
        ]);

        if (!$user) {
            return Response::redirectTo(route("auth.new"));
        }

        return Response::redirectTo(route("auth.send", ["id" => $user->id]));
    }

    public function send(Request $request): Response
    {

        $user = User::findBy([
            "id" => $request->getParam("id", ""),
            "cpf" => $request->getParam("cpf", "")
        ]);

        if (!$user) {
            return Response::redirectTo(route("auth.new"));
        }

        $userToken = UserToken::make($user->id);
        $userToken->save();

        return Response::render("auth/send", ["token" => $userToken->token]);
    }

    public function find(Request $request): Response
    {
        $user = User::findBy([
            "cpf" => $request->getParam("cpf", ""),
            "email" => $request->getParam("email", "")
        ]);
        ;

        if (!$user) {
            return Response::goBack();
        }

        return Response::redirectTo(route("auth.send", ["id" => $user->id, "cpf" => $user->cpf]));
    }

    public function logout(): Response
    {
        Auth::logout();

        return Response::redirectTo("/");
    }
}
