<?php

namespace App\Controller;

use App\Models\Pet;
use App\Models\User;
use App\Services\Storage;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class PetController extends Controller
{
    public function index(Request $request): Response
    {
        $view_data = [
            "pets" => [],
            "profile" => [
                "id" => null,
                "name" => "not found"
            ]
        ];

        $profile = User::findById($request->getParam("id"));

        if (!$profile) {
            return Response::render("pet/index", $view_data)->withUser();
        }

        $view_data["profile"] = $profile->toArray();

        $view_data["pets"] = $profile->pets()->get();

        array_map(function ($pet) {
            return $pet->toArray();
        }, $view_data["pets"]);

        return Response::render("pet/index", $view_data)->withUser();
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

        $pet = Pet::create($attributes);

        if ($pet->hasErrors()) {
            return Response::render("pet/create", ["errors" => $pet->getAllErrors()]);
        }

        $storage = new Storage('pets');

        if ($request->file('image')) {

            $pet->image = $storage->upload($request->file('image'), $pet->id);
        }

        $pet->save();

        return Response::redirectTo(route("pet.index", ["id" => $user->id]));
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

        return Response::redirectTo(route("pet.index", ["id" => $request->user()->id]));
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

        return Response::redirectTo(route("pet.index", ["id" => $request->user()->id]));
    }
}
