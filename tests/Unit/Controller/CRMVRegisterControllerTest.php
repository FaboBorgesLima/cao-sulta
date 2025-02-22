<?php

namespace Tests\Unit\Controller;

use App\Controller\CRMVRegisterController;
use App\Models\CRMVRegister;
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
            'profile' => $user->id
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
        $user = $this->loginVet();

        $response = $this->post(CRMVRegisterController::class, 'store');

        $this->assertNotEquals(302, $response->code);

        $crmv_register_before_post = $user->vet()->get()->CRMVRegisters()->get();

        $response = $this->post(CRMVRegisterController::class, 'store', [
            "crmv" => Random::CRMV(),
            "state" => Random::state(),
        ]);

        $this->assertEquals(302, $response->code);

        $crmv_register_after_post = $user->vet()->get()->CRMVRegisters()->get();

        $this->assertEquals(count($crmv_register_before_post) + 1, count($crmv_register_after_post));
    }

    public function test_update(): void
    {

        $user = $this->loginVet();

        $this->render = true;

        $crmv_register = $user->vet()->get()->CRMVRegisters()->get()[0];

        $response = $this->get(CRMVRegisterController::class, 'update', [
            "crmv_register" => $crmv_register->id
        ]);

        $this->assertStringContainsString($crmv_register->state . '-' . $crmv_register->crmv, $response);
    }

    public function test_destroy(): void
    {

        $user = $this->loginVet();

        $this->render = true;

        $crmv_register = $user->vet()->get()->CRMVRegisters()->get()[0];

        $this->post(CRMVRegisterController::class, 'destroy', [
            "crmv_register" => $crmv_register->id,
            "_method" => "DELETE"
        ]);

        $this->assertNotNull(CRMVRegister::findById($crmv_register->id));

        $crmv_resgister2 = $user->vet()->get()->CRMVRegisters()->new([
            "state" => Random::state(),
            "crmv" => Random::CRMV()
        ]);

        $crmv_resgister2->save();

        $crmv_register_before_delete = $user->vet()->get()->CRMVRegisters()->get();

        $this->post(CRMVRegisterController::class, 'destroy', [
            "crmv_register" => $crmv_register->id,
            "_method" => "DELETE"
        ]);

        $crmv_register_after_delete = $user->vet()->get()->CRMVRegisters()->get();

        $this->assertEquals(count($crmv_register_before_delete) - 1, count($crmv_register_after_delete));
    }
}
