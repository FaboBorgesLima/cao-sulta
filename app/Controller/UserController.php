<?php

namespace App\Controller;

use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Lib\Authentication\Auth;

class UserController extends Controller
{
    public function show(Request $request): Response
    {
        $user = User::findById((int) $request->getParam("id"));

        $loggedUser = Auth::user();

        $data = [
            "name" => "not found",
            "isVet" => false,
            "userId" => $request->getParam("id"),
            "loggedUserId" => null
        ];

        if (!$user) {
            return Response::render("user/show", $data);
        }

        $data["name"] = $user->name;
        $data["isVet"] = $user->isVet();

        if ($loggedUser) {
            $data["loggedUserId"] = $loggedUser->id;
        }

        if (!$loggedUser) {
            return Response::render("user/show", $data);
        }

        return Response::render("user/show", $data);
    }

    public function create(): Response
    {
        return Response::render("user/create");
    }

    public function store(Request $request): Response
    {
        $user = new User([
            "name" => $request->getParam("name"),
            "cpf" => $request->getParam("cpf"),
            "email" => $request->getParam("email")
        ]);

        if (!$user->isValid()) {
            return Response::render(
                "user/create",
                ["errors" => $user->getAllErrors()]
            );
        }

        $user->save();

        return Response::redirectTo(route("auth.send", ["id" => $user->id, "cpf" => $user->cpf]));
    }

    public function dashboard(): Response
    {
        $user = Auth::user();
        $vet = Auth::isVet();
        $loggedUserId = $user->id;

        return Response::render("user/dashboard", [
            "name" => $user->name,
            "loggedUserId" => $loggedUserId,
            "isVet" => (bool) $vet,
        ]);
    }
}
