<?php

namespace Tests\Unit\Controller;

use App\Controller\UserController;
use App\Models\User;
use App\Models\UserToken;
use Core\Http\Response;
use Lib\Authentication\Auth;
use Lib\Random;
use Tests\Unit\Controller\ControllerTestCase;

class UserControllerTest extends ControllerTestCase
{
    public function test_create(): void
    {
        $res = $this->get(UserController::class, "create");

        $this->assertInstanceOf(Response::class, $res);

        $this->assertStringEndsWith("user/create.phtml", $res->getFile());
    }

    public function test_store(): void
    {

        $res = $this->post(UserController::class, "store");

        $this->assertInstanceOf(Response::class, $res);

        $this->assertStringEndsWith("user/create.phtml", $res->getFile());

        $this->assertArrayHasKey("errors", $res->getData());

        $userParams =  [
            "name" => Random::name(),
            "cpf" => Random::CPF(),
            "email" => Random::email()
        ];

        $res = $this->post(UserController::class, "store", $userParams);

        $this->assertInstanceOf(Response::class, $res);

        $this->assertEquals(302, $res->code);
    }

    public function test_dashboard(): void
    {

        $user = User::factory();

        $user->save();

        $token = UserToken::make($user->id);

        $token->save();

        Auth::login($token->token);

        $res = $this->post(UserController::class, "dashboard");

        $this->assertInstanceOf(Response::class, $res);

        $this->assertEquals(200, $res->code);

        $this->assertStringEndsWith("user/dashboard.phtml", $res->getFile());
    }
}
