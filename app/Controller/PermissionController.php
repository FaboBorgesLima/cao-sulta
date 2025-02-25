<?php

namespace App\Controller;

use App\Models\Permission;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;

class PermissionController extends Controller
{
    public function index(Request $request): Response
    {

        $data['permissionsVets'] = array_map(
            function ($vet) {
                $user = $vet->user()->get();
                return array_merge($vet->toArray(), [
                    "user" => $user->toArray(),
                    "permission" => Permission::where([
                        ['vet_id', '=', $vet->id],
                        ['user_id', '=', $user->id]
                    ])[0]->toArray()
                ]);
            },
            $request->user()->permissionsVets()->get()
        );

        $vet = $request->user()->vet()->get();

        if ($vet) {
            $data['vet'] = $vet->toArray();

            $data['vet']['permissionsUsers'] = array_map(
                fn($user) => $user->toArray(),
                $vet->permissionsUsers()->get()
            );
        }

        return Response::render('permission/index', $data)->withUser();
    }

    public function create(): Response
    {
        return Response::render('permission/form');
    }

    public function store(Request $request): Response
    {
        $vet = $request->user()->vet()->get();

        $permission = Permission::make([
            "user_id" => (int) $request->getParam('user'),
            "vet_id" => $vet->id,
        ]);

        $permission->save();

        return Response::goBack();
    }

    public function update(Request $request): Response
    {

        $permission = Permission::where([
            ['user_id', '=', (int) $request->getParam('user')],
            ['vet_id', '=', (int) $request->getParam('vet')]
        ])[0];

        $accepted = $request->getParam('accepted');

        if (!$permission) {
            return Response::notFound(json: true);
        }

        if ($accepted === null) {
            return Response::badRequest(json: true);
        }

        if ($permission->user()->get()->id != $request->user()->id) {
            return Response::forbidden(json: true);
        }

        $permission->accepted = (int) (bool) $accepted;

        if (!$permission->save()) {
            return Response::badRequest(json: true);
        }


        return Response::json(['accepted' => (int) (bool) $accepted]);
    }
}
