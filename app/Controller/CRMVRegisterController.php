<?php

namespace App\Controller;

use App\Models\CRMVRegister;
use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Lib\State;

class CRMVRegisterController extends Controller
{
    public function index(Request $request): Response
    {
        $profile_id = $request->getParam("profile");

        if (!$profile_id) {
            return Response::notFound("user or vet not found");
        }

        $profile = User::findById((int) $profile_id);

        if (!$profile) {
            return Response::notFound("user or vet not found");
        }

        $vet = $profile->vet()->get();

        if (!$vet) {
            return Response::notFound("user or vet not found");
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


        $view_data["vet"] = $vet->toArray();
        $view_data["vet"]["profile"] = $profile->toArray();

        $view_data["vet"]["crmv_registers"] = array_map(function ($crmv_register) {
            return $crmv_register->toArray();
        }, $vet->CRMVRegisters()->get());

        if ($request->acceptJson()) {
            return Response::json($view_data);
        }

        return Response::render("crmv-register/index", $view_data)->withUser();
    }

    public function create(): Response
    {
        return Response::render('crmv-register/form', [
            "states" => State::getStates(),
        ]);
    }

    public function store(Request $request): Response
    {
        $crmv_register = $request->user()->vet()->get()->CRMVRegisters()->new($request->only(CRMVRegister::columns()));

        if ($crmv_register->save()) {
            return Response::redirectTo(route('crmv-register.index', ["profile" => $request->user()->id]));
        }

        return Response::render('crmv-register/form', [
            "errors" => $crmv_register->getAllErrors(),
            "states" => State::getStates(),
        ]);
    }


    public function update(Request $request): Response
    {
        $crmv_register = CRMVRegister::findById((int) $request->getParam("crmv_register"));

        if (!$crmv_register) {
            return Response::notFound('crmv register not founf');
        }

        return Response::render('crmv-register/form', [
            "states" => State::getStates(),
            "update" => true,
            "crmv_register" => $crmv_register->toArray()
        ]);
    }

    public function save(Request $request): Response
    {
        $crmv_register = CRMVRegister::findById((int) $request->getParam("crmv_register"));

        if (!$crmv_register) {
            return Response::notFound('crmv register not founf');
        }

        if ($crmv_register->vet()->get()->user()->get()->id != $request->user()->id) {
            return Response::forbidden();
        }

        $attributes = $request->only(["crmv", "state"]);

        foreach ($attributes as $attribute => $value) {
            $crmv_register->$attribute = $value;
        }

        if (!$crmv_register->save()) {
            return Response::render('crmv-register/form', [
                "states" => State::getStates(),
                "update" => true,
                "crmv_register" => $crmv_register->toArray(),
                "errors" => $crmv_register->getAllErrors(),
            ]);
        }

        return Response::redirectTo(route('crmv-register.index', [
            'profile_id' => $request->user()->id
        ]));
    }

    public function destroy(Request $request): Response
    {
        $crmv_register = CRMVRegister::findById((int) $request->getParam("crmv_register"));

        if (!$crmv_register) {
            return Response::notFound();
        }

        if ($crmv_register->vet()->get()->user()->get()->id != $request->user()->id) {
            return Response::forbidden();
        }

        if (!$crmv_register->destroy()) {
            return Response::render('crmv-register/form', [
                "states" => State::getStates(),
                "update" => true,
                "crmv_register" => $crmv_register->toArray(),
                "errors" => array_merge($crmv_register->getAllErrors(), ["destroy" => "cannot destroy register"]),
            ]);
        }

        return Response::redirectTo(route('crmv-register.index', ['profile' => $request->user()->id]));
    }
}
