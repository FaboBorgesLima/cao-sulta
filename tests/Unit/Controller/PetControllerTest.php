<?php

namespace Tests\Unit\Controller;

use App\Controller\PetController;
use App\Models\Pet;
use App\Models\User;
use Lib\Random;
use Tests\Unit\Controller\ControllerTestCase;

class PetControllerTest extends ControllerTestCase
{
    public function test_all(): void
    {
        $user = User::factory()->make();

        $user->save();

        $pet = $user->pets()->new(["name" => Random::name()]);

        $pet->save();

        $this->render = true;

        $view = $this->get(PetController::class, "index", ["id" => $user->id]);

        $this->assertIsString($view);

        $this->assertStringContainsString($pet->name, $view);
    }

    public function test_create(): void
    {
        $this->login();

        $res = $this->get(PetController::class, "create");

        $this->assertEquals(200, $res->code);
    }

    public function test_store(): void
    {
        $user = $this->login();

        $name = Random::name();

        $res = $this->post(PetController::class, "store", ["name" => $name]);

        $this->assertNotNull(Pet::findBy([["name", $name]]));

        $this->assertEquals($user->id, Pet::findBy([["name", $name]])->user_id);

        $this->assertEquals(302, $res->code);
    }

    public function test_update(): void
    {
        $user = $this->login();

        $res = $this->get(PetController::class, "update");

        $this->assertEquals(302, $res->code);

        $pet = $user->pets()->new(["name" => Random::name()]);

        $pet->save();

        $this->render = true;

        $view = $this->get(PetController::class, "update", ["id" => $pet->id]);

        $this->assertStringContainsString($pet->name, $view);
    }

    public function test_save(): void
    {
        $user = $this->login();

        // redirect if not exists
        $res = $this->get(PetController::class, "save");

        $this->assertEquals(302, $res->code);

        // updates correctly
        $pet = $user->pets()->new(["name" => Random::name()]);

        $pet->save();

        $this->post(PetController::class, "save", ["id" => $pet->id, "name" => "newName"]);

        $this->assertEquals("newName", Pet::findById($pet->id)->name);

        // cannot update with wrong auth

        $this->login();

        $this->post(PetController::class, "save", ["id" => $pet->id, "name" => Random::name()]);

        $this->assertEquals("newName", Pet::findById($pet->id)->name);
    }

    public function test_destroy(): void
    {
        $user = $this->login();

        // redirect if not exists
        $res = $this->get(PetController::class, "save");

        $this->assertEquals(302, $res->code);

        // deletes correctly
        $pet1 = $user->pets()->new(["name" => Random::name()]);

        $pet1->save();

        $pet2 = $user->pets()->new(["name" => Random::name()]);

        $pet2->save();

        $this->get(PetController::class, "delete", ["id" => $pet1->id]);

        $this->assertNull(Pet::findById($pet1->id));

        // cannot delete with wrong auth

        $this->login();

        $this->get(PetController::class, "delete", ["id" => $pet2->id]);

        $this->assertNotNull(Pet::findById($pet2->id));
    }
}
