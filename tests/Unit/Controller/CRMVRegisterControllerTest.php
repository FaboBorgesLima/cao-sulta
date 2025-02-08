<?php

namespace Tests\Unit\Controller;

use App\Controller\CRMVRegisterController;
use Database\Factory\CRMVRegisterFactory;
use Database\Factory\UserFactory;
use Lib\Random;
use Tests\Unit\Controller\ControllerTestCase;

class CRMVRegisterControllerTest extends ControllerTestCase
{
    public function test_index(): void
    {

        $user = UserFactory::create();

        $vet = $user->vet()->new([]);

        $crmv_register = CRMVRegisterFactory::make();

        $vet->attachCRMVRegister($crmv_register);
        $vet->save();


        $this->assertTrue($user->isVet());

        $this->render = true;

        $response = $this->get(CRMVRegisterController::class, 'index', [
            'profile_id' => $user->id
        ]);

        $this->assertStringContainsString($crmv_register->crmv, $response);

        $this->assertStringContainsString($crmv_register->state, $response);
    }

    public function test_create(): void
    {

        $this->loginVet();

        $this->render = true;

        $response = $this->get(CRMVRegisterController::class, 'create');

        $this->assertStringContainsString("New CRMV", $response);

        $this->assertStringContainsString("<form", $response);
    }

    public function test_store(): void
    {
        $this->loginVet();

        $response = $this->post(CRMVRegisterController::class, 'store');

        $this->assertNotEquals(302, $response->code);

        $response = $this->post(CRMVRegisterController::class, 'store', [
            "crmv" => Random::CRMV(),
            "state" => Random::state(),
        ]);

        $this->assertEquals(302, $response->code);
    }
}
