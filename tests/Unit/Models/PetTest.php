<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\User;
use Lib\Random;
use Tests\TestCase;

class PetTest extends TestCase
{
    public function test_can_crud(): void
    {
        $pet = User::factory();

        $pet->save();

        $pet = $pet->pets()->new(["name" => Random::name()]);

        // create
        $this->assertTrue($pet->save());

        // read 

        $this->assertInstanceOf(Pet::class, Pet::findById($pet->id));

        // update
        $newName = "rodolfo";

        $pet->name = $newName;

        $this->assertTrue($pet->save());

        $pet = Pet::findById($pet->id);

        $this->assertEquals($newName, $pet->name);

        // delete

        $this->assertTrue($pet->destroy());

        $pet = Pet::findById($pet->id);

        $this->assertNull($pet);
    }

    public function test_cannot_put_wrong_name(): void
    {
        $user = User::factory();

        $user->save();

        $pet = new Pet(["user_id" => $user->id, "name" => Random::name()]);

        $this->assertTrue($pet->save());

        $pet->name = "f4k3 n4m3";

        $this->assertFalse($pet->save());

        $pet->name = "\$teve";

        $this->assertFalse($pet->save());

        $pet->name = "legit name";

        $this->assertTrue($pet->save());
    }
}
