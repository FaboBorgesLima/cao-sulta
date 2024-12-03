<?php

namespace App\Controller;

use App\Models\CRMVRegister;
use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class VetController extends Controller
{
    public function create(): Response
    {
        return Response::render("vet/create");
    }

    public function store(Request $request): Response
    {
        $userAttributes = [
            "cpf" => $request->getParam("cpf", ""),
            "name" => $request->getParam("name", ""),
            "email" => $request->getParam("email", "")
        ];

        $crmvAttributes = [
            "crmv" => $request->getParam("crmv", ""),
            "state" => $request->getParam("state", ""),
            "vet_id" => 0
        ];

        $user = new User($userAttributes);
        $crmv = new CRMVRegister($crmvAttributes);

        if (!$user->isValid() || !$crmv->isValid()) {
            return Response::render("vet/create", [
                "errors" => array_merge(
                    $user->getAllErrors(),
                    $crmv->getAllErrors()
                )
            ]);
        }

        $user->save();

        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $vet->attachCRMVRegister(new CRMVRegister($crmvAttributes));

        $vet->save();

        return Response::redirectTo(route("auth.send", [
            "id" => $user->id,
            "cpf" => $user->cpf
        ]));
    }
}
