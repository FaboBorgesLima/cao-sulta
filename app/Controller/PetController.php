<?php

namespace App\Controller;

use App\Models\Pet;
use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class PetController extends Controller
{
    public function all(Request $request): Response
    {
        $view_data = [
            "logged_user_id" => null,
            "pets" => [],
            "name" => "not found",
            "user_id" => $request->getParam("id")
        ];

        $logged_user = $request->user();

        if ($logged_user) {
            $view_data["logged_user_id"] = $logged_user->id;
        }

        $user = User::findById($request->getParam("id"));

        if (!$user) {
            return Response::render("pet/all", $view_data);
        }

        $view_data["name"] = $user->name;

        $view_data["pets"] = $user->pets()->get();

        array_map(function ($pet) {
            return [
                "name" => $pet->name,
                "id" => $pet->id
            ];
        }, $view_data["pets"]);

        return Response::render("pet/all", $view_data);
    }

    public function create(): Response
    {
        return Response::render("pet/create");
    }

    public function store(Request $request): Response
    {
        $user = $request->user();

        $attributes = [
            "name" => $request->getParam("name"),
            "user_id" => $user->id
        ];

        $pet =  new Pet($attributes);

        if ($pet->save()) {
            return Response::redirectTo(route("user.pets", ["id" => $user->id]));
        }

        return Response::render("pet/create", ["errors" => $pet->getAllErrors()]);
    }

    public function update(Request $request): Response
    {
        $pet = Pet::findById((int) $request->getParam("id"));

        if (!$pet) {
            return Response::redirectTo(route("dashboard"));
        }

        return Response::render("pet/update-delete", [
            "id" => $pet->id,
            "name" => $pet->name
        ]);
    }

    public function save(Request $request): Response
    {
        $pet = Pet::findById((int) $request->getParam("id"));

        if (!$pet) {
            return Response::redirectTo(route("dashboard"));
        }

        if ($pet->user()->get()->id != $request->user()->id) {
            $res = Response::goBack();

            $res->code = 401;

            return $res;
        }

        $pet->name = $request->getParam("name");

        if (!$pet->isValid()) {
            return Response::render("pet/update-delete", [
                "id" => $pet->id,
                "name" => $pet->name,
                "errors" => $pet->getAllErrors()
            ]);
        }

        $pet->save();

        return Response::redirectTo(route("user.pets", ["id" => $request->user()->id]));
    }

    public function delete(Request $request): Response
    {
        $pet = Pet::findById((int) $request->getParam("id"));

        if (!$pet) {
            return Response::redirectTo(route("dashboard"));
        }

        if ($pet->user()->get()->id != $request->user()->id) {
            $res = Response::goBack();

            $res->code = 401;

            return $res;
        }

        $pet->destroy();

        return Response::redirectTo(route("user.pets", ["id" => $request->user()->id]));
    }
}
