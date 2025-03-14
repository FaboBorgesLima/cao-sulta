<?php

namespace App\Controller;

use App\Models\CRMVRegister;
use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Lib\State;

class VetController extends Controller
{
    public function create(): Response
    {
        return Response::render("vet/create", ["states" => State::getStates()]);
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

        $user = User::make($userAttributes);
        $crmv = CRMVRegister::make($crmvAttributes);

        if (!$user->isValid() || !$crmv->isValid()) {
            return Response::render("vet/create", [
                "states" => State::getStates(),
                "errors" => array_merge(
                    $user->getAllErrors(),
                    $crmv->getAllErrors()
                )
            ]);
        }

        $user->save();

        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $vet->attachCRMVRegister(CRMVRegister::make($crmvAttributes));

        $vet->save();

        return Response::redirectTo(route("auth.send", [
            "id" => $user->id,
            "cpf" => $user->cpf
        ]));
    }
}
