<?php

namespace App\Controller;

use App\Models\Permission;
use App\Models\User;
use Core\Http\Controllers\Controller;
use Core\Http\Request;
use Core\Http\Response;
use Lib\Authentication\Auth;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $page_n = (int) $request->getParam('page');
        $email = (string) $request->getParam('email', '');

        $paginator = User::paginate(10, ['email', 'LIKE', "%$email%"]);

        $page = $paginator->getPage($page_n);

        return Response::render('user/index', [
            'page' => $page,
        ]);
    }

    public function show(Request $request): Response
    {
        $profile = User::findById((int) $request->getParam("id"));



        $data = [
            "profile" => [
                "name" => "not found",
                "id" => $request->getParam("id"),
            ],
            "is_vet" => false,
            "sended_permission" => null
        ];

        if (!$profile) {
            return Response::render("user/show", $data)->withUser();
        }

        if ($request->user() && $request->user()->id != $profile->id && $vet = $request->user()->vet()->get()) {

            $result = Permission::where([
                ['vet_id', '=', $vet->id],
                ['user_id', '=', $profile->id]
            ]);

            if ($result) {
                $data['sended_permission'] = $result[0];
            }
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
        $user = User::create([
            "name" => $request->getParam("name"),
            "cpf" => $request->getParam("cpf"),
            "email" => $request->getParam("email")
        ]);

        if ($user->hasErrors()) {
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
