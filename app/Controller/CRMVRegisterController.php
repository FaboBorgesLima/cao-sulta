<?php

namespace App\Controller;

use App\Models\User;
use App\Models\Vet;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class CRMVRegisterController extends Controller
{
    public function show(Request $request): Response
    {
        $vet_id = $request->getParam("id");

        if (!$vet_id) {
            return Response::redirectTo(route("dashboard"));
        }

        /** @var \App\Models\Vet|null */
        $vet = Vet::findById((int) $vet_id);

        if (!$vet) {
            return Response::notFound();
        }

        $view_data = [
            "vet" => [
                "id" => 0,
                "profile" => [
                    "id" => 0,
                    "name" => "not found"
                ],
                "crmv_registers" => []
            ],
        ];
        /** @var \App\Models\Vet|null */
        $vet_profile = $vet->user()->get();

        $view_data["vet"] = $vet->toArray();
        $view_data["vet"]["profile"] = $vet_profile->toArray();

        $view_data["vet"]["crmv_registers"] = array_map(function ($crmv_register) {
            return $crmv_register->toArray();
        }, $vet->CRMVRegisters()->get());

        return Response::render("crmv-register/show", $view_data)->withUser();
    }
}
