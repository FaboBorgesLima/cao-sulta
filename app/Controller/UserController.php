<?php

namespace App\Controller;

use App\Models\User;
use App\Models\UserToken;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Lib\Authentication\Auth;

class UserController extends Controller
{
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
        /** @var null|\App\Models\Vet */
        $vet = $user->vet()->get();
        $crmvs = [];
        if ($vet) {
            $crmvs = $vet->CRMVRegisters()->get();
        }

        return Response::render("user/dashboard", [
            "name" => $user->name,
            "isVet" => (bool) $vet,
            "crmvs" => $crmvs
        ]);
    }
}
