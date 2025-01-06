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
        $profile = User::findById((int) $request->getParam("id"));

        $data = [
            "profile" => [
                "name" => "not found",
                "id" => $request->getParam("id"),
            ],
            "is_vet" => false,
        ];

        if (!$profile) {
            return Response::render("user/show", $data)->withUser();
        }

        $data["is_vet"] = $profile->isVet();
        $data["profile"] = $profile->toArray();

        return Response::render("user/show", $data)->withUser();
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
        $vet = Auth::isVet();

        return Response::render("user/dashboard", [
            "is_vet" => (bool) $vet,
        ])->withUser();
    }
}
