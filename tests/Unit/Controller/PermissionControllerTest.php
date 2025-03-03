<?php

namespace Tests\Unit\Controller;

use App\Controller\PermissionController;
use App\Controller\PetController;
use App\Models\Pet;
use App\Models\User;
use Lib\Random;
use Tests\Unit\Controller\ControllerTestCase;

class PermissionControllerTest extends ControllerTestCase
{
    public function test_store(): void
    {
        $user = $this->loginVet();

        $vet = $user->vet()->get();

        $request = $this->post(PermissionController::class, "store", ["user" => $user->id]);

        $this->assertEquals(302, $request->code);

        $this->assertEquals(1, count($user->permissions()->get()));
    }

    public function test_update(): void
    {
        $user = $this->loginVet();

        $vet = $user->vet()->get();

        $permission = $user->permissions()->new([
            "vet_id" => $vet->id,
            "accepted" => 0
        ]);

        $permission->save();

        $request = $this->post(PermissionController::class, "update", [
            "user" => $user->id,
            'vet' => $vet->id,
            'accepted' => true,
            '_method' => "PUT"
        ]);

        $this->assertEquals(200, $request->code);

        $this->assertTrue((bool) $user->permissions()->get()[0]->accepted);
    }

    public function test_destroy(): void
    {
        $user = $this->loginVet();

        $vet = $user->vet()->get();

        $permission = $user->permissions()->new(["vet_id" => $vet->id, "accepted" => 0]);

        $permission->save();

        $this->assertEquals(1, count($user->permissions()->get()));

        $request = $this->post(PermissionController::class, "destroy", [
            "user" => $user->id,
            'vet' => $vet->id,
            '_method' => "DELETE"
        ]);

        $this->assertEquals(200, $request->code);

        $this->assertEquals(0, count($user->permissions()->get()));
    }
}
