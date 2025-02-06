<?php

namespace Tests\Unit\Controller;

use App\Controller\UserController;
use App\Models\CRMVRegister;
use App\Models\User;
use App\Models\UserToken;
use Core\Http\Response;
use Lib\Authentication\Auth;
use Lib\Random;
use Tests\Unit\Controller\ControllerTestCase;

class UserControllerTest extends ControllerTestCase
{
    public function test_show(): void
    {
        // can show without being logged
        $user = User::factory();

        $user->save();

        $this->render = true;

        $view = $this->get(UserController::class, "show", ["id" => $user->id]);

        $this->assertIsString($view);

        $this->assertStringContainsString("<title>" . $user->name . "</title>", $view);

        $this->assertStringContainsString("tutor", $view);

        // show when logged

        $user = $this->login();

        $view = $this->get(UserController::class, "show", ["id" => $user->id]);

        $this->assertIsString($view);

        $this->assertStringContainsString("<title>" . $user->name . "</title>", $view);

        $this->assertStringContainsString("tutor", $view);

        $this->assertStringNotContainsString("veterinary", $view);

        // show that user is vet
        /** @var \App\Models\Vet */
        $vet = $user->vet()->new([]);

        $vet->attachCRMVRegister(CRMVRegister::make(["state" => "SP", "crmv" => "2024123"]));

        $this->assertTrue($vet->save());

        $view = $this->get(UserController::class, "show", ["id" => $user->id]);

        $this->assertIsString($view);

        $this->assertStringContainsString("<title>" . $user->name . "</title>", $view);

        $this->assertStringContainsString("veterinary", $view);
    }

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

        $token = UserToken::withRandomToken($user->id);

        $token->save();

        Auth::login($token->token);

        $res = $this->post(UserController::class, "dashboard");

        $this->assertInstanceOf(Response::class, $res);

        $this->assertEquals(200, $res->code);

        $this->assertStringEndsWith("user/dashboard.phtml", $res->getFile());
    }
}
